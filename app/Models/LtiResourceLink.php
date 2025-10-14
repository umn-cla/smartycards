<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LtiResourceLink extends Model
{
    protected $fillable = [
        'lti_deployment_id',
        'resource_link_id',
        'title',
        'description',
        'context_id', // canvas course id
        'context_label', // canvas course code
        'context_title', // canvas course name
        'deck_id',
        'settings', // JSON field for additional settings
        'custom_params', // JSON field for custom params from LTI launch
        // helpful for tracking assignments after courses have been copied
    ];

    protected $casts = [
        'settings' => 'array',
        'custom_params' => 'array',
    ];

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(LtiDeployment::class, 'lti_deployment_id');
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class, 'deck_id');
    }

    /**
     * Get the LTI Platform through the deployment
     */
    public function platform(): LtiPlatform
    {
        return $this->deployment->platform;
    }

    public static function findByDeploymentAndResourceLinkId(LtiDeployment $ltiDeployment, string $resourceLinkId): ?static
    {
        return static::where('lti_deployment_id', $ltiDeployment->id)
            ->where('resource_link_id', $resourceLinkId)
            ->first();
    }

    public function getDeckId(): ?int
    {
        return $this->deck_id;
    }
}
