<?php

namespace App\Models;

use App\Traits\HasPublicUid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Invitee extends Model
{
    use HasUuids, HasPublicUid;

    protected string $uidPrefix = 'inv';
    protected string $uidColumn = 'public_id';

    protected $fillable = [
        'public_id',
        'assessment_id',
        'email',
        'first_name',
        'last_name',
        'invite_token',
        'email_sent_at',
        'email_opened_at',
        'status',
        'email_status',
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
            'email_status' => 'sent',
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
