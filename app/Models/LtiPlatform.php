<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LtiPlatform extends Model
{
    protected $fillable = [
        'issuer', // https://canvas.umn.edu
        'name', // "UMN Canvas - Prod"
        'auth_login_url', // "https://.../api/lti/authorize_redirect",
        'auth_token_url', // "https://.../login/oauth2/token",
        'key_set_url', // "https://.../api/lti/security/jwks"
    ];

    public function deployments(): HasMany
    {
        return $this->hasMany(LtiDeployment::class);
    }

    public function resourceLinks(): HasManyThrough
    {
        return $this->through(LtiDeployment::class)
            ->hasMany(LtiResourceLink::class);
    }

    public static function findByIssuer(string $issuer): ?static
    {
        return static::where('issuer', $issuer)->first();
    }
}
