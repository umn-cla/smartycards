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
}
