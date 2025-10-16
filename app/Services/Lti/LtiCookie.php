<?php

namespace App\Services\Lti;

use Packback\Lti1p3\Interfaces\ICookie;
use Illuminate\Support\Facades\Cookie;

class LtiCookie implements ICookie
{

    const COOKIE_PREFIX = 'lti_';
    const COOKIE_EXPIRY = 3600; // 1 hour

    public function getCookie(string $name): ?string
    {
        $value = Cookie::get(self::COOKIE_PREFIX . $name);

        return is_string($value) ? $value : null;
    }

    public function setCookie(string $name, string $value, int $exp = self::COOKIE_EXPIRY, array $options = []): void
    {
        $minutes = $exp / 60; //Laravel uses minutes for cookie expiry

        Cookie::queue(
            name: $name,
            value: $value,
            minutes: $minutes,
            path: $options['path'] ?? '/',
            domain: $options['domain'] ?? null,
            secure: true, // must be secure for cross-site cookies
            httpOnly: $options['httpOnly'] ?? true,
            raw: $options['raw'] ?? false,
            // cross-site cookies should specify SameSite=None and Secure
            sameSite: $options['sameSite'] ?? 'None',
        );
    }
}
