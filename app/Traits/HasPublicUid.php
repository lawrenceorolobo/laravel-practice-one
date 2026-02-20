<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait to automatically generate public UIDs for models
 * 
 * Usage: Add `use HasPublicUid;` to model and define `protected $uidPrefix = 'usr';`
 */
trait HasPublicUid
{
    public static function bootHasPublicUid(): void
    {
        static::creating(function ($model) {
            $uidColumn = $model->getUidColumn();
            
            if (empty($model->{$uidColumn})) {
                $model->{$uidColumn} = $model->generatePublicUid();
            }
        });
    }

    /**
     * Get the column name for the UID
     */
    public function getUidColumn(): string
    {
        return $this->uidColumn ?? 'uid';
    }

    /**
     * Get the prefix for UIDs (override in model)
     */
    public function getUidPrefix(): string
    {
        return $this->uidPrefix ?? 'rec';
    }

    /**
     * Generate a unique public UID
     * Format: prefix_timestamp_random (e.g., usr_1707235200_8x3k2m)
     */
    public function generatePublicUid(): string
    {
        $prefix = $this->getUidPrefix();
        $timestamp = now()->timestamp;
        $random = strtolower(Str::random(6));
        
        return "{$prefix}_{$timestamp}_{$random}";
    }

    /**
     * Find model by public UID
     */
    public static function findByUid(string $uid): ?static
    {
        $instance = new static;
        return static::where($instance->getUidColumn(), $uid)->first();
    }

    /**
     * Find model by public UID or fail
     */
    public static function findByUidOrFail(string $uid): static
    {
        $instance = new static;
        return static::where($instance->getUidColumn(), $uid)->firstOrFail();
    }
}
