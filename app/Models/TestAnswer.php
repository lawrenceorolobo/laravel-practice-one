<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'session_id',
        'question_id',
        'selected_options',
        'text_answer',
        'is_correct',
        'points_earned',
        'answered_at',
        'time_spent_seconds',
    ];

    protected function casts(): array
    {
        return [
            'selected_options' => 'array',
            'is_correct' => 'boolean',
            'points_earned' => 'decimal:2',
            'answered_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TestSession::class, 'session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function checkCorrectness(): void
    {
        $question = $this->question;
        $type = $question->question_type;
        $isCorrect = false;
        $pointsEarned = 0;

        // Types that use option selection (compare selected_options with correct option labels)
        $choiceTypes = [
            'single_choice', 'multiple_choice', 'true_false',
            'odd_one_out', 'analogy',
            'sequence_pattern', 'matrix_pattern', 'spatial_rotation',
            'shape_assembly', 'pattern_recognition', 'hotspot',
        ];

        // Types that use text_answer with expected_answer
        $textTypes = ['text_input', 'fill_blank', 'code_snippet', 'word_problem', 'mental_maths'];

        if (in_array($type, $choiceTypes)) {
            $correctLabels = $question->correctLabels;
            $selectedLabels = $this->selected_options ?? [];

            sort($correctLabels);
            sort($selectedLabels);

            $isCorrect = $correctLabels === $selectedLabels;
            $pointsEarned = $isCorrect ? $question->points : 0;

        } elseif (in_array($type, $textTypes)) {
            if ($question->expected_answer) {
                $answer = strtolower(trim($this->text_answer ?? ''));
                $accepted = array_map(fn($a) => strtolower(trim($a)), explode('||', $question->expected_answer));
                $isCorrect = in_array($answer, $accepted, true);
                $pointsEarned = $isCorrect ? $question->points : 0;
            } else {
                $isCorrect = null; // Manual grading required
            }

        } elseif ($type === 'numeric') {
            if ($question->expected_answer) {
                $given = (float) ($this->text_answer ?? '');
                $expected = (float) $question->expected_answer;
                $tolerance = (float) ($question->question_metadata['tolerance'] ?? 0);
                $isCorrect = abs($given - $expected) <= $tolerance;
                $pointsEarned = $isCorrect ? $question->points : 0;
            } else {
                $isCorrect = null;
            }

        } elseif (in_array($type, ['ordering', 'drag_drop_sort'])) {
            // selected_options contains the user's ordered array
            $userOrder = $this->selected_options ?? [];
            $correctOrder = $question->options()
                ->orderBy('option_order')
                ->pluck('option_label')
                ->toArray();

            $isCorrect = $userOrder === $correctOrder;
            $pointsEarned = $isCorrect ? $question->points : 0;

        } elseif ($type === 'matching') {
            // text_answer contains JSON string of user's matching pairs
            $userMatching = json_decode($this->text_answer ?? '{}', true) ?: [];
            $correctMatching = $question->question_metadata['correct_pairs'] ?? [];

            // Normalize keys for comparison
            ksort($userMatching);
            ksort($correctMatching);

            $isCorrect = $userMatching === $correctMatching;
            $pointsEarned = $isCorrect ? $question->points : 0;

        } elseif ($type === 'likert_scale') {
            // Likert: always "correct" (opinion-based), award full points
            $isCorrect = ($this->text_answer !== null && $this->text_answer !== '');
            $pointsEarned = $isCorrect ? $question->points : 0;

        } elseif ($type === 'shape_puzzle') {
            // Puzzle: text_answer contains JSON of slot→piece mapping
            $userPuzzle = json_decode($this->text_answer ?? '{}', true) ?: [];
            $correctOrder = $question->options()
                ->orderBy('option_order')
                ->get()
                ->mapWithKeys(fn($o, $i) => ["slot_$i" => $o->option_text])
                ->toArray();
            ksort($userPuzzle);
            ksort($correctOrder);
            $isCorrect = $userPuzzle === $correctOrder;
            $pointsEarned = $isCorrect ? $question->points : 0;

        } else {
            // Unknown type — manual grading
            $isCorrect = null;
        }

        $this->update([
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
        ]);
    }
}
