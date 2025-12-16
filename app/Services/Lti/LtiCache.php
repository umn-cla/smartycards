<?php

namespace App\Services\Lti;

use Illuminate\Support\Facades\Cache;
use Packback\Lti1p3\Interfaces\ICache;

class LtiCache implements ICache
{
    const LAUNCH_PREFIX = 'lti_launch_';
    const NONCE_PREFIX = 'lti_nonce_';
    const ACCESS_TOKEN_PREFIX = 'lti_access_token_';

    // Cache TTL constants (in seconds)
    private const LAUNCH_TTL = 3600;      // 1 hour
    private const NONCE_TTL = 300;        // 5 minutes
    private const TOKEN_TTL = 3500;       // ~1 hour minus buffer for clock drift

    /**
     * Get cached launch data (the decoded JWT body)
     */
    public function getLaunchData(string $key): ?array
    {
        $data = Cache::get(self::LAUNCH_PREFIX . $key);

        return is_array($data) ? $data : null;
    }

    /**
     * Cache the launch data from a validated JWT
     */
    public function cacheLaunchData(string $key, array $jwtBody): void
    {
        Cache::put(self::LAUNCH_PREFIX . $key, $jwtBody, self::LAUNCH_TTL);
    }

    /**
     * Store a nonce with its associated state for replay protection
     */
    public function cacheNonce(string $nonce, string $state): void
    {
        Cache::put(self::NONCE_PREFIX . $nonce, $state, self::NONCE_TTL);
    }

    /**
     * Check if a nonce is valid (exists and matches state)
     *
     * @param string $nonce The nonce to validate
     * @param string $state The state to match against
     * @return bool True if nonce is valid and unused
     */
    public function checkNonceIsValid(string $nonce, string $state): bool
    {
        $cachedState = Cache::get(self::NONCE_PREFIX . $nonce);

        if ($cachedState === null) {
            // Nonce doesn't exist (already used or expired)
            return false;
        }

        if ($cachedState !== $state) {
            // State mismatch - possible attack
            return false;
        }

        // Nonce is valid - delete it so it can't be reused (one-time use)
        Cache::forget(self::NONCE_PREFIX . $nonce);

        return true;
    }

    /**
     * Cache an OAuth access token for calling Canvas APIs
     */
    public function cacheAccessToken(string $key, string $accessToken): void
    {
        Cache::put(self::ACCESS_TOKEN_PREFIX . $key, $accessToken, self::TOKEN_TTL);
    }

    /**
     * Get a cached OAuth access token (if it exists and is still valid)
     */
    public function getAccessToken(string $key): ?string
    {
        $token = Cache::get(self::ACCESS_TOKEN_PREFIX . $key);

        return is_string($token) ? $token : null;
    }

    /**
     * Clear a cached access token (e.g., after it's been revoked)
     */
    public function clearAccessToken(string $key): void
    {
        Cache::forget(self::ACCESS_TOKEN_PREFIX . $key);
    }
}
