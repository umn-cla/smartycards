<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LTI Private Key
    |--------------------------------------------------------------------------
    |
    | Your tool's RSA private key, base64 encoded.
    | Generate with: base64 -w 0 storage/lti-private.key
    |
    */
    'private_key' => env('LTI_PRIVATE_KEY')
        ? base64_decode(env('LTI_PRIVATE_KEY'))
        : null,

    /*
    |--------------------------------------------------------------------------
    | LTI Public Key
    |--------------------------------------------------------------------------
    |
    | Your tool's RSA public key, base64 encoded.
    | Generate with: base64 -w 0 storage/lti-public.key
    |
    */
    'public_key' => env('LTI_PUBLIC_KEY')
        ? base64_decode(env('LTI_PUBLIC_KEY'))
        : null,

    /*
    |--------------------------------------------------------------------------
    | Key ID (KID)
    |--------------------------------------------------------------------------
    |
    | A unique identifier for your key. Useful for key rotation.
    | Format: toolname-year-month or toolname-version
    |
    */
    'kid' => env('LTI_KID', 'smartycards-2025-01'),
];
