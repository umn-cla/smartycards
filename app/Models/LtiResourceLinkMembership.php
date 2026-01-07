<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LtiResourceLinkMembership extends Model
{
    protected $fillable = [
        'user_id',
        'lti_resource_link_id',
        'roles',
        'is_staff',
        'last_launch_at',
        'lti_user_id',
    ];

    protected $casts = [
        'roles' => 'array',
        'is_staff' => 'boolean',
        'last_launch_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resourceLink(): BelongsTo
    {
        return $this->belongsTo(LtiResourceLink::class, 'lti_resource_link_id');
    }

    /**
     * Scope to get only staff memberships
     */
    public function scopeStaff($query)
    {
        return $query->where('is_staff', true);
    }

    /**
     * Scope to find the most recent ungraded LTI assignment for a specific deck and user
     * Returns memberships that:
     * - Match the given deck_id and user_id
     * - Are not staff memberships
     * - Have not been successfully graded yet
     * - Ordered by most recent launch first
     */
    public function scopeUngradedForDeck($query, int $deckId, int $userId)
    {
        return $query
            ->join('lti_resource_links', 'lti_resource_link_memberships.lti_resource_link_id', '=', 'lti_resource_links.id')
            ->leftJoin('lti_grade_submissions', function ($join) use ($userId) {
                $join->on('lti_resource_link_memberships.lti_resource_link_id', '=', 'lti_grade_submissions.lti_resource_link_id')
                    ->where('lti_grade_submissions.user_id', '=', $userId)
                    ->where('lti_grade_submissions.success', '=', true);
            })
            ->where('lti_resource_links.deck_id', $deckId)
            ->where('lti_resource_link_memberships.user_id', $userId)
            ->where('lti_resource_link_memberships.is_staff', false)
            ->whereNull('lti_grade_submissions.id')
            ->orderBy('lti_resource_link_memberships.last_launch_at', 'desc')
            ->select('lti_resource_link_memberships.*');
    }
}
