<?php

namespace App\Models;

use App\Traits\HasPublicUid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUuids, HasPublicUid;

    protected string $uidPrefix = 'pay';
    protected string $uidColumn = 'public_id';

    protected $fillable = [
        'public_id',
        'user_id',
        'plan_id',
        'idempotency_key',
        'amount',
        'currency',
        'billing_cycle',
        'paystack_reference',
        'paystack_transaction_id',
        'flutterwave_reference',
        'flutterwave_tx_id',
        'status',
        'paid_at',
        'expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
