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
        $isCorrect = false;
        $pointsEarned = 0;

        if ($question->isTextInput()) {
            // Text input requires manual grading or exact match
            $isCorrect = null; // Will be graded manually
        } else {
            $correctLabels = $question->correctLabels;
            $selectedLabels = $this->selected_options ?? [];

            // Sort both arrays for comparison
            sort($correctLabels);
            sort($selectedLabels);

            // All-or-nothing: must match exactly
            $isCorrect = $correctLabels === $selectedLabels;
            $pointsEarned = $isCorrect ? $question->points : 0;
        }

        $this->update([
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
        ]);
    }
}
