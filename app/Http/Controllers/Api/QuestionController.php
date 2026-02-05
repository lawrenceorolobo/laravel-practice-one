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
            'options' => ['sometimes', 'array', 'min:2', 'max:10'],
            'options.*.text' => ['required_with:options', 'string', 'max:500'],
            'options.*.is_correct' => ['required_with:options', 'boolean'],
        ]);

        return DB::transaction(function () use ($question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'] ?? $question->question_text,
                'question_type' => $validated['question_type'] ?? $question->question_type,
                'points' => $validated['points'] ?? $question->points,
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
