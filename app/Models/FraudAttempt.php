<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'assessment_id',
        'email',
        'ip_address',
        'device_fingerprint',
        'matched_session_id',
        'match_type',
        'similarity_score',
        'blocked',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'similarity_score' => 'decimal:2',
            'blocked' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function matchedSession(): BelongsTo
    {
        return $this->belongsTo(TestSession::class, 'matched_session_id');
    }

    public static function logAttempt(
        string $assessmentId,
        string $matchType,
        array $data,
        ?string $matchedSessionId = null,
        ?float $similarityScore = null
    ): self {
        return self::create([
            'assessment_id' => $assessmentId,
            'email' => $data['email'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'device_fingerprint' => $data['device_fingerprint'] ?? null,
            'matched_session_id' => $matchedSessionId,
            'match_type' => $matchType,
            'similarity_score' => $similarityScore,
            'blocked' => true,
            'metadata' => $data,
        ]);
    }
}
