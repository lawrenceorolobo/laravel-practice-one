<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasUuids;

    protected $fillable = [
        'assessment_id',
        'question_text',
        'question_type',
        'points',
        'question_order',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'question_order' => 'integer',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('option_order');
    }

    public function correctOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->where('is_correct', true);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function isSingleChoice(): bool
    {
        return $this->question_type === 'single_choice';
    }

    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice';
    }

    public function isTextInput(): bool
    {
        return $this->question_type === 'text_input';
    }

    public function getCorrectLabelsAttribute(): array
    {
        return $this->correctOptions->pluck('option_label')->toArray();
    }
}
