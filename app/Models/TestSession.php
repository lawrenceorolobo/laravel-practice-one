<?php

namespace App\Models;

use App\Traits\HasPublicUid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestSession extends Model
{
    use HasUuids, HasPublicUid;

    protected string $uidPrefix = 'tss';
    protected string $uidColumn = 'public_id';

    protected $fillable = [
        'public_id',
        'invitee_id',
        'assessment_id',
        'first_name',
        'last_name',
        'email',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'timezone',
        'canvas_fingerprint',
        'webgl_fingerprint',
        'screen_resolution',
        'fullscreen_exits',
        'tab_switches',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'total_score',
        'max_score',
        'percentage',
        'passed',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fullscreen_exits' => 'integer',
            'tab_switches' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'time_spent_seconds' => 'integer',
            'total_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
        ];
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(Invitee::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class, 'session_id');
    }

    public function fraudAttempts(): HasMany
    {
        return $this->hasMany(FraudAttempt::class, 'matched_session_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'timed_out']);
    }

    public function isFlagged(): bool
    {
        return $this->status === 'flagged';
    }

    public function incrementTabSwitch(): void
    {
        $this->increment('tab_switches');
    }

    public function incrementFullscreenExit(): void
    {
        $this->increment('fullscreen_exits');
    }

    public function calculateScore(): void
    {
        $totalScore = $this->answers->sum('points_earned');
        $maxScore = $this->assessment->maxScore;
        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        $passed = $percentage >= $this->assessment->pass_percentage;

        $this->update([
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => round($percentage, 2),
            'passed' => $passed,
            'time_spent_seconds' => abs(now()->diffInSeconds($this->started_at)),
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Sync invitee status to 'completed'
        $this->invitee?->update(['status' => 'completed']);
    }
}
