<?php

use App\Models\FeatureFlag;

if (!function_exists('feature')) {
    /**
     * Check if a feature flag is enabled (cached, single DB query)
     */
    function feature(string $key): bool
    {
        $flags = cache()->remember('feature_flags', 60, function () {
            return FeatureFlag::pluck('enabled', 'key')->toArray();
        });

        return (bool) ($flags[$key] ?? false);
    }
}

if (!function_exists('feature_flags_public')) {
    /**
     * Get all feature flag states (cached)
     */
    function feature_flags_public(): array
    {
        return cache()->remember('feature_flags', 60, function () {
            return FeatureFlag::pluck('enabled', 'key')->toArray();
        });
    }
}
