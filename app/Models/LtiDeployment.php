<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LtiDeployment extends Model
{
    protected $fillable = [
        'lti_platform_id',
        'deployment_id', // the platform-specific ID for this deployment
        'client_id', // the platform-specific OAuth2 client ID
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(LtiPlatform::class, 'lti_platform_id');
    }

    public function resourceLinks(): HasMany
    {
        return $this->hasMany(LtiResourceLink::class);
    }

    public static function findByPlatformAndDeploymentId(LtiPlatform $platform, string $deploymentId): ?static
    {
        return static::where('lti_platform_id', $platform->id)
            ->where('deployment_id', $deploymentId)
            ->first();
    }
}
