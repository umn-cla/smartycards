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
        'lineitem_url', // AGS endpoint for this assignment's gradebook column
        'lineitems_url', // AGS endpoint for all lineitems in this context
        'ags_scopes', // AGS operations allowed
    ];

    protected $casts = [
        'settings' => 'array',
        'custom_params' => 'array',
        'ags_scopes' => 'array',
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

    /**
     * Get all activity events associated with this resource link
     */
    public function activityEvents()
    {
        return $this->hasMany(ActivityEvent::class);
    }

    /**
     * Check if this resource link has AGS (Assignment and Grade Services) enabled
     */
    public function hasAgs(): bool
    {
        return !empty($this->lineitem_url) || !empty($this->lineitems_url);
    }

    /**
     * Get all entries for this resource link
     */
    public function entries()
    {
        return $this->hasMany(LtiResourceLinkEntry::class, 'lti_resource_link_id');
    }

    /**
     * Get only student entries
     */
    public function studentEntries()
    {
        return $this->entries()->students();
    }

    /**
     * Get only staff entries
     */
    public function staffEntries()
    {
        return $this->entries()->staff();
    }

    /**
     * Check if a user has staff role in this resource link
     */
    public function userHasStaffRole(User $user): bool
    {
        return $this->staffEntries()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Scope to only include resource links where the user has an entry
     */
    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('entries', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Get the Canvas URL for this assignment
     */
    public function getCanvasUrl(): ?string
    {
        if (!$this->lineitem_url) {
            return null;
        }

        // Parse the lineitem URL to extract base URL, course ID, and assignment ID
        // Typical format: https://canvas.domain.com/api/lti/courses/{course_id}/line_items/{line_item_id}
        if (preg_match('#^(https?://[^/]+)/api/lti/courses/(\d+)/line_items/(\d+)#', $this->lineitem_url, $matches)) {
            $baseUrl = $matches[1];
            $courseId = $matches[2];
            $lineItemId = $matches[3];

            return "{$baseUrl}/courses/{$courseId}/assignments/{$lineItemId}";
        }

        return null;
    }
}
