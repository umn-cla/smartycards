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
        // LTI 1.3 requires SameSite=None and Secure=true for cross-domain cookie support
        // These cookies are used during the OIDC flow between Canvas and our app
        $minutes = $exp / 60;
        $prefixedName = self::COOKIE_PREFIX . $name;
        Cookie::queue(
            Cookie::make($prefixedName, $value, $minutes)
                ->withSameSite('none')
                ->withSecure(true)
                ->withHttpOnly(true)
        );
    }
}
