<?php

namespace App\Library;

class FeatureFlagService
{
    public static function isEnabled($feature): bool
    {
        return config("features.{$feature}", false);
    }

    public static function getAllFeatures(): array
    {
        return config('features', []);
    }
}
