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

        $allTypes = 'single_choice,multiple_choice,text_input,true_false,ordering,matching,fill_blank,numeric,sequence_pattern,matrix_pattern,odd_one_out,spatial_rotation,shape_assembly,analogy,drag_drop_sort,hotspot,code_snippet,likert_scale,pattern_recognition,mental_maths,word_problem';
        $noOptionTypes = ['text_input', 'fill_blank', 'numeric', 'mental_maths', 'word_problem', 'code_snippet'];

        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:2000'],
            'question_type' => ['required', "in:{$allTypes}"],
            'question_metadata' => ['nullable', 'array'],
            'points' => ['integer', 'min:1', 'max:100'],
            'expected_answer' => ['nullable', 'string', 'max:1000'],
            'options' => [in_array($request->question_type, $noOptionTypes) ? 'nullable' : 'required', 'array', 'min:2', 'max:10'],
            'options.*.text' => ['required', 'string', 'max:500'],
            'options.*.is_correct' => ['required', 'boolean'],
            'options.*.media_url' => ['nullable', 'string', 'max:500'],
            'options.*.media_type' => ['nullable', 'string', 'max:50'],
        ]);

        // Validate at least one correct option for choice types
        if (!in_array($validated['question_type'], $noOptionTypes) && !empty($validated['options'])) {
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
            // Check for duplicate question text
            $exists = $assessment->questions()
                ->whereRaw('LOWER(question_text) = ?', [strtolower($validated['question_text'])])
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages([
                    'question_text' => ['A question with this text already exists in this assessment.'],
                ]);
            }

            $maxOrder = $assessment->questions()->max('question_order') ?? 0;

            $question = Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'question_metadata' => $validated['question_metadata'] ?? null,
                'expected_answer' => $validated['expected_answer'] ?? null,
                'points' => $validated['points'] ?? 1,
                'question_order' => $maxOrder + 1,
            ]);

            if (!in_array($validated['question_type'], $noOptionTypes) && !empty($validated['options'])) {
                $optionRows = [];
                foreach ($validated['options'] as $index => $option) {
                    $optionRows[] = [
                        'id' => \Illuminate\Support\Str::uuid()->toString(),
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'option_label' => chr(65 + $index),
                        'is_correct' => $option['is_correct'],
                        'option_order' => $index + 1,
                        'media_url' => $option['media_url'] ?? null,
                        'media_type' => $option['media_type'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                QuestionOption::insert($optionRows);
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

        $allTypes = 'single_choice,multiple_choice,text_input,true_false,ordering,matching,fill_blank,numeric,sequence_pattern,matrix_pattern,odd_one_out,spatial_rotation,shape_assembly,analogy,drag_drop_sort,hotspot,code_snippet,likert_scale,pattern_recognition,mental_maths,word_problem';

        $validated = $request->validate([
            'question_text' => ['sometimes', 'string', 'max:2000'],
            'question_type' => ['sometimes', "in:{$allTypes}"],
            'question_metadata' => ['nullable', 'array'],
            'points' => ['integer', 'min:1', 'max:100'],
            'expected_answer' => ['nullable', 'string', 'max:2000'],
            'options' => ['sometimes', 'array', 'min:2', 'max:10'],
            'options.*.text' => ['required_with:options', 'string', 'max:500'],
            'options.*.is_correct' => ['required_with:options', 'boolean'],
            'options.*.media_url' => ['nullable', 'string', 'max:500'],
            'options.*.media_type' => ['nullable', 'string', 'max:50'],
        ]);

        return DB::transaction(function () use ($question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'] ?? $question->question_text,
                'question_type' => $validated['question_type'] ?? $question->question_type,
                'points' => $validated['points'] ?? $question->points,
                'expected_answer' => $validated['expected_answer'] ?? $question->expected_answer,
            ]);

            if (isset($validated['options'])) {
                // Delete existing options and bulk recreate
                $question->options()->delete();
                $optionRows = [];
                foreach ($validated['options'] as $index => $option) {
                    $optionRows[] = [
                        'id' => \Illuminate\Support\Str::uuid()->toString(),
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'option_label' => chr(65 + $index),
                        'is_correct' => $option['is_correct'],
                        'option_order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                QuestionOption::insert($optionRows);
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

        // Reorder remaining questions with single query
        $remaining = $assessment->questions()->orderBy('question_order')->pluck('id');
        if ($remaining->isNotEmpty()) {
            $cases = [];
            $ids = [];
            foreach ($remaining as $i => $qId) {
                $cases[] = "WHEN id = '{$qId}' THEN " . ($i + 1);
                $ids[] = "'{$qId}'";
            }
            DB::statement("UPDATE questions SET question_order = CASE " . implode(' ', $cases) . " END WHERE id IN (" . implode(',', $ids) . ")");
        }

        $assessment->update(['total_questions' => $assessment->questions()->count()]);

        return response()->json([
            'message' => 'Question deleted successfully.',
        ]);
    }

    /**
     * Reorder questions — single CASE-WHEN UPDATE instead of N queries
     */
    public function reorder(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*' => ['required', 'uuid', 'exists:questions,id'],
        ]);

        if (count($validated['questions']) > 0) {
            $cases = [];
            $ids = [];
            foreach ($validated['questions'] as $order => $questionId) {
                $newOrder = $order + 1;
                $cases[] = "WHEN id = '{$questionId}' THEN {$newOrder}";
                $ids[] = "'{$questionId}'";
            }
            $caseStr = implode(' ', $cases);
            $idStr = implode(',', $ids);
            DB::statement("UPDATE questions SET question_order = CASE {$caseStr} END WHERE id IN ({$idStr}) AND assessment_id = ?", [$assessment->id]);
        }

        return response()->json([
            'message' => 'Questions reordered successfully.',
        ]);
    }

    /**
     * Import questions from CSV file — bulk insert for speed.
     * Handles 500 questions in ~1s instead of timing out.
     */
    public function importCSV(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $noOptionCsvTypes = ['text_input', 'fill_blank', 'numeric', 'mental_maths', 'word_problem', 'code_snippet'];
        $allCsvTypes = ['single_choice','multiple_choice','text_input','true_false','ordering','matching','fill_blank','numeric','sequence_pattern','matrix_pattern','odd_one_out','spatial_rotation','shape_assembly','analogy','drag_drop_sort','hotspot','code_snippet','likert_scale','pattern_recognition','mental_maths','word_problem'];

        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');
        $rowCount = 0;
        $errors = [];
        $headerSkipped = false;
        $maxOrder = $assessment->questions()->max('question_order') ?? 0;
        $now = now();

        // Phase 1: Parse all rows into memory
        $questionRows = [];
        $optionBatch = [];

        // Pre-load existing question texts for duplicate detection
        $existingTexts = $assessment->questions()
            ->pluck('question_text')
            ->map(fn($t) => strtolower(trim($t)))
            ->flip()
            ->all();
        $seenInCsv = []; // track duplicates within the CSV itself
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            if ($rowCount > 500) break;

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

            if (empty($questionText)) {
                $errors[] = "Row {$rowCount}: Missing question text";
                continue;
            }

            // Duplicate detection
            $lowerText = strtolower($questionText);
            if (isset($existingTexts[$lowerText]) || isset($seenInCsv[$lowerText])) {
                $skipped++;
                continue;
            }
            $seenInCsv[$lowerText] = true;

            if (!in_array($questionType, $allCsvTypes)) {
                $errors[] = "Row {$rowCount}: Invalid type '{$questionType}'";
                continue;
            }

            // Validate options for choice types before creating anything
            $optionsForRow = [];
            if (!in_array($questionType, $noOptionCsvTypes)) {
                if ($questionType === 'true_false') {
                    $correctTF = strtoupper(trim($correctAnswer));
                    // Auto-generate True/False options
                    $optionsForRow = [
                        ['text' => 'True', 'label' => 'A', 'is_correct' => in_array($correctTF, ['A', 'TRUE']), 'order' => 1],
                        ['text' => 'False', 'label' => 'B', 'is_correct' => in_array($correctTF, ['B', 'FALSE']), 'order' => 2],
                    ];
                } else {
                    $rawOptions = array_filter([$optionA, $optionB, $optionC, $optionD], fn ($o) => $o !== '');
                    if (count($rawOptions) < 2) {
                        $errors[] = "Row {$rowCount}: Need at least 2 options for {$questionType}";
                        continue;
                    }
                    $correctLetters = array_map('trim', explode(',', $correctAnswer));
                    $hasCorrect = false;
                    foreach ($rawOptions as $index => $optionText) {
                        $label = chr(65 + $index);
                        $isCorrect = in_array($label, $correctLetters);
                        if ($isCorrect) $hasCorrect = true;
                        $optionsForRow[] = ['text' => $optionText, 'label' => $label, 'is_correct' => $isCorrect, 'order' => $index + 1];
                    }
                    if (!$hasCorrect) {
                        $errors[] = "Row {$rowCount}: No correct answer specified";
                        continue;
                    }
                }
            }

            $maxOrder++;
            $qId = \Illuminate\Support\Str::uuid()->toString();

            $questionRows[] = [
                'id' => $qId,
                'assessment_id' => $assessment->id,
                'question_text' => $questionText,
                'question_type' => $questionType,
                'expected_answer' => in_array($questionType, $noOptionCsvTypes) ? ($expectedAnswer ?: null) : null,
                'points' => min($points, 100),
                'question_order' => $maxOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (!empty($optionsForRow)) {
                $optionBatch[$qId] = $optionsForRow;
            }
        }

        fclose($handle);

        if (empty($questionRows)) {
            $message = $skipped > 0
                ? "All {$skipped} question(s) already exist in this assessment (duplicates skipped)."
                : 'No valid questions found in CSV.';
            return response()->json([
                'message' => $message,
                'created' => 0,
                'skipped' => $skipped,
                'errors' => $errors,
            ], 422);
        }

        // Phase 2: Bulk insert everything in one transaction (2 queries total)
        DB::transaction(function () use ($assessment, $questionRows, $optionBatch, $now) {
            // Bulk insert questions in chunks of 100
            foreach (array_chunk($questionRows, 100) as $chunk) {
                Question::insert($chunk);
            }

            // Build all option rows and bulk insert
            if (!empty($optionBatch)) {
                $allOptions = [];
                foreach ($optionBatch as $qId => $opts) {
                    foreach ($opts as $opt) {
                        $allOptions[] = [
                            'id' => \Illuminate\Support\Str::uuid()->toString(),
                            'question_id' => $qId,
                            'option_text' => $opt['text'],
                            'option_label' => $opt['label'],
                            'is_correct' => $opt['is_correct'],
                            'option_order' => $opt['order'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                foreach (array_chunk($allOptions, 100) as $chunk) {
                    QuestionOption::insert($chunk);
                }
            }

            $assessment->update(['total_questions' => $assessment->questions()->count()]);
        });

        $created = count($questionRows);

        return response()->json([
            'message' => "Imported {$created} question(s) successfully." . ($skipped ? " {$skipped} duplicate(s) skipped." : ''),
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
            'total_questions' => $assessment->fresh()->total_questions,
        ], 201);
    }

    /**
     * Batch delete multiple questions at once
     */
    public function batchDelete(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getEditableAssessment($request, $assessmentId);

        $validated = $request->validate([
            'question_ids' => ['required', 'array', 'min:1'],
            'question_ids.*' => ['required', 'uuid'],
        ]);

        $deleted = Question::where('assessment_id', $assessment->id)
            ->whereIn('id', $validated['question_ids'])
            ->delete();

        // Reorder remaining questions
        $remaining = $assessment->questions()->orderBy('question_order')->pluck('id');
        if ($remaining->isNotEmpty()) {
            $cases = [];
            $ids = [];
            foreach ($remaining as $i => $qId) {
                $cases[] = "WHEN id = '{$qId}' THEN " . ($i + 1);
                $ids[] = "'{$qId}'";
            }
            DB::statement("UPDATE questions SET question_order = CASE " . implode(' ', $cases) . " END WHERE id IN (" . implode(',', $ids) . ")");
        }

        $assessment->update(['total_questions' => $remaining->count()]);

        return response()->json([
            'message' => "Deleted {$deleted} question(s) successfully.",
            'deleted' => $deleted,
        ]);
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
