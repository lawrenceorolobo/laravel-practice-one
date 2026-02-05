<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    use HasUuids;

    protected $fillable = [
        'question_id',
        'option_text',
        'option_label',
        'is_correct',
        'option_order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'option_order' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
