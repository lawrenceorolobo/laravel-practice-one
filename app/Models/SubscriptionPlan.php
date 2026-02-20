<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'monthly_price',
        'annual_discount_percent',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_price' => 'decimal:2',
            'annual_discount_percent' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getAnnualPriceAttribute(): float
    {
        $monthlyTotal = $this->monthly_price * 12;
        $discount = $monthlyTotal * ($this->annual_discount_percent / 100);
        return round($monthlyTotal - $discount, 2);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'plan_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'subscription_plan_id');
    }
}
