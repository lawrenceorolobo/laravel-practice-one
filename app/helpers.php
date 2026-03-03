<?php

use App\Models\FeatureFlag;

if (!function_exists('feature')) {
    /**
     * Check if a feature flag is enabled
     */
    function feature(string $key): bool
    {
        return FeatureFlag::isEnabled($key);
    }
}
