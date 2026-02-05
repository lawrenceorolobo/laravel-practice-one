<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Invitee extends Model
{
    use HasUuids;

    protected $fillable = [
        'assessment_id',
        'email',
        'invite_token',
        'email_sent_at',
        'email_opened_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'email_opened_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitee $invitee) {
            if (empty($invitee->invite_token)) {
                $invitee->invite_token = Str::random(64);
            }
        });
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function testSession(): HasOne
    {
        return $this->hasOne(TestSession::class);
    }

    public function hasStarted(): bool
    {
        return in_array($this->status, ['started', 'completed']);
    }

    public function hasCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'email_sent_at' => now(),
        ]);
    }

    public function markAsOpened(): void
    {
        if ($this->status === 'sent') {
            $this->update([
                'status' => 'opened',
                'email_opened_at' => now(),
            ]);
        }
    }
}
