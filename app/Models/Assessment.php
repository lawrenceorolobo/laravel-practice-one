<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'duration_minutes',
        'pass_percentage',
        'allow_back_navigation',
        'shuffle_questions',
        'shuffle_options',
        'show_result_to_taker',
        'proctoring_enabled',
        'webcam_required',
        'fullscreen_required',
        'start_datetime',
        'end_datetime',
        'status',
        'total_questions',
        'total_invites',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'pass_percentage' => 'decimal:2',
            'allow_back_navigation' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_result_to_taker' => 'boolean',
            'proctoring_enabled' => 'boolean',
            'webcam_required' => 'boolean',
            'fullscreen_required' => 'boolean',
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'total_questions' => 'integer',
            'total_invites' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('question_order');
    }

    public function invitees(): HasMany
    {
        return $this->hasMany(Invitee::class);
    }

    public function testSessions(): HasMany
    {
        return $this->hasMany(TestSession::class);
    }

    public function fraudAttempts(): HasMany
    {
        return $this->hasMany(FraudAttempt::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->between($this->start_datetime, $this->end_datetime);
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && $this->start_datetime->isFuture();
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->end_datetime->isPast();
    }

    public function getMaxScoreAttribute(): int
    {
        return $this->questions->sum('points');
    }
}
