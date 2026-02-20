<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    /**
     * Add question to assessment
     */
    public function store(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:2000'],
            'question_type' => ['required', 'in:single_choice,multiple_choice,text_input'],
            'points' => ['integer', 'min:1', 'max:100'],
            'expected_answer' => ['nullable', 'required_if:question_type,text_input', 'string', 'max:1000'],
            'options' => ['required_unless:question_type,text_input', 'array', 'min:2', 'max:10'],
            'options.*.text' => ['required', 'string', 'max:500'],
            'options.*.is_correct' => ['required', 'boolean'],
        ]);

        // Validate at least one correct option for MCQ
        if ($validated['question_type'] !== 'text_input') {
            $correctCount = collect($validated['options'])->where('is_correct', true)->count();
            
            if ($correctCount === 0) {
                throw ValidationException::withMessages([
                    'options' => ['At least one option must be marked as correct.'],
                ]);
            }

            if ($validated['question_type'] === 'single_choice' && $correctCount > 1) {
                throw ValidationException::withMessages([
                    'options' => ['Single choice questions can only have one correct answer.'],
                ]);
            }
        }

        return DB::transaction(function () use ($assessment, $validated) {
            $maxOrder = $assessment->questions()->max('question_order') ?? 0;

            $question = Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'expected_answer' => $validated['expected_answer'] ?? null,
                'points' => $validated['points'] ?? 1,
                'question_order' => $maxOrder + 1,
            ]);

            if ($validated['question_type'] !== 'text_input' && !empty($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'option_label' => chr(65 + $index), // A, B, C, D...
                        'is_correct' => $option['is_correct'],
                        'option_order' => $index + 1,
                    ]);
                }
            }

            $assessment->update(['total_questions' => $assessment->questions()->count()]);

            return response()->json([
                'message' => 'Question added successfully.',
                'question' => $question->load('options'),
            ], 201);
        });
    }

    /**
     * Update question
     */
    public function update(Request $request, string $assessmentId, string $questionId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $question = Question::where('id', $questionId)
            ->where('assessment_id', $assessment->id)
            ->firstOrFail();

        $validated = $request->validate([
            'question_text' => ['sometimes', 'string', 'max:2000'],
            'question_type' => ['sometimes', 'in:single_choice,multiple_choice,text_input'],
            'points' => ['integer', 'min:1', 'max:100'],
            'expected_answer' => ['nullable', 'string', 'max:2000'],
            'options' => ['sometimes', 'array', 'min:2', 'max:10'],
            'options.*.text' => ['required_with:options', 'string', 'max:500'],
            'options.*.is_correct' => ['required_with:options', 'boolean'],
        ]);

        return DB::transaction(function () use ($question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'] ?? $question->question_text,
                'question_type' => $validated['question_type'] ?? $question->question_type,
                'points' => $validated['points'] ?? $question->points,
                'expected_answer' => $validated['expected_answer'] ?? $question->expected_answer,
            ]);

            if (isset($validated['options'])) {
                // Delete existing options and recreate
                $question->options()->delete();

                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'option_label' => chr(65 + $index),
                        'is_correct' => $option['is_correct'],
                        'option_order' => $index + 1,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Question updated successfully.',
                'question' => $question->fresh()->load('options'),
            ]);
        });
    }

    /**
     * Delete question
     */
    public function destroy(Request $request, string $assessmentId, string $questionId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $question = Question::where('id', $questionId)
            ->where('assessment_id', $assessment->id)
            ->firstOrFail();

        $question->delete();

        // Reorder remaining questions
        $assessment->questions()->orderBy('question_order')->get()
            ->each(function ($q, $index) {
                $q->update(['question_order' => $index + 1]);
            });

        $assessment->update(['total_questions' => $assessment->questions()->count()]);

        return response()->json([
            'message' => 'Question deleted successfully.',
        ]);
    }

    /**
     * Reorder questions
     */
    public function reorder(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*' => ['required', 'uuid', 'exists:questions,id'],
        ]);

        DB::transaction(function () use ($assessment, $validated) {
            foreach ($validated['questions'] as $order => $questionId) {
                Question::where('id', $questionId)
                    ->where('assessment_id', $assessment->id)
                    ->update(['question_order' => $order + 1]);
            }
        });

        return response()->json([
            'message' => 'Questions reordered successfully.',
        ]);
    }

    /**
     * Import questions from CSV file.
     * Format: question_text,question_type,points,option_a,option_b,option_c,option_d,correct_answer,expected_answer
     *   question_type: single_choice, multiple_choice, text_input
     *   correct_answer: A,B,C,D (comma-separated for multiple_choice) or leave empty for text_input
     *   expected_answer: only for text_input type
     */
    public function importCSV(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');
        $rowCount = 0;
        $created = 0;
        $errors = [];
        $headerSkipped = false;
        $maxOrder = $assessment->questions()->max('question_order') ?? 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowCount++;

                if ($rowCount > 500) {
                    break; // Safety limit
                }

                // Skip header row
                if (!$headerSkipped) {
                    $headerSkipped = true;
                    $firstCell = strtolower(trim($row[0] ?? ''));
                    if (in_array($firstCell, ['question', 'question_text', 'text', 'q'])) {
                        continue;
                    }
                }

                $questionText = trim($row[0] ?? '');
                $questionType = strtolower(trim($row[1] ?? 'single_choice'));
                $points = (int) ($row[2] ?? 1) ?: 1;
                $optionA = trim($row[3] ?? '');
                $optionB = trim($row[4] ?? '');
                $optionC = trim($row[5] ?? '');
                $optionD = trim($row[6] ?? '');
                $correctAnswer = strtoupper(trim($row[7] ?? ''));
                $expectedAnswer = trim($row[8] ?? '');

                // Validate question text
                if (empty($questionText)) {
                    $errors[] = "Row {$rowCount}: Missing question text";
                    continue;
                }

                if (!in_array($questionType, ['single_choice', 'multiple_choice', 'text_input'])) {
                    $errors[] = "Row {$rowCount}: Invalid type '{$questionType}'";
                    continue;
                }

                $maxOrder++;

                $question = Question::create([
                    'assessment_id' => $assessment->id,
                    'question_text' => $questionText,
                    'question_type' => $questionType,
                    'expected_answer' => $questionType === 'text_input' ? ($expectedAnswer ?: null) : null,
                    'points' => min($points, 100),
                    'question_order' => $maxOrder,
                ]);

                // Create options for choice questions
                if ($questionType !== 'text_input') {
                    $options = array_filter([$optionA, $optionB, $optionC, $optionD], fn ($o) => $o !== '');

                    if (count($options) < 2) {
                        $errors[] = "Row {$rowCount}: Need at least 2 options for {$questionType}";
                        $question->delete();
                        $maxOrder--;
                        continue;
                    }

                    $correctLetters = array_map('trim', explode(',', $correctAnswer));

                    $hasCorrect = false;
                    foreach ($options as $index => $optionText) {
                        $label = chr(65 + $index); // A, B, C, D
                        $isCorrect = in_array($label, $correctLetters);
                        if ($isCorrect) $hasCorrect = true;

                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionText,
                            'option_label' => $label,
                            'is_correct' => $isCorrect,
                            'option_order' => $index + 1,
                        ]);
                    }

                    if (!$hasCorrect) {
                        $errors[] = "Row {$rowCount}: No correct answer specified";
                        $question->options()->delete();
                        $question->delete();
                        $maxOrder--;
                        continue;
                    }
                }

                $created++;
            }

            fclose($handle);

            $assessment->update(['total_questions' => $assessment->questions()->count()]);

            DB::commit();

            return response()->json([
                'message' => "Imported {$created} question(s) successfully.",
                'created' => $created,
                'errors' => $errors,
                'total_questions' => $assessment->fresh()->total_questions,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return response()->json([
                'message' => 'CSV import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function getEditableAssessment(Request $request, string $assessmentId): Assessment
    {
        $assessment = Assessment::where('id', $assessmentId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($assessment->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => ['Only draft assessments can be edited.'],
            ]);
        }

        return $assessment;
    }
}
