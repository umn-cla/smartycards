<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LtiResourceLinkEntry extends Model
{
    protected $fillable = [
        'user_id',
        'lti_resource_link_id',
        'lti_user_id',
        'roles',
        'is_staff',
        'last_launch_at',
        'score',
        'score_maximum',
        'activity_event_id',
        'completed_at',
        'submitted_at',
        'submission_success',
        'submission_error',
    ];

    protected function casts(): array
    {
        return [
            'roles' => 'array',
            'is_staff' => 'boolean',
            'last_launch_at' => 'datetime',
            'score' => 'decimal:2',
            'score_maximum' => 'decimal:2',
            'completed_at' => 'datetime',
            'submitted_at' => 'datetime',
            'submission_success' => 'boolean',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resourceLink(): BelongsTo
    {
        return $this->belongsTo(LtiResourceLink::class, 'lti_resource_link_id');
    }

    public function activityEvent(): BelongsTo
    {
        return $this->belongsTo(ActivityEvent::class);
    }

    // Helper Methods
    public function isCompleted(): bool
    {
        return !is_null($this->score);
    }

    public function isSubmitted(): bool
    {
        return $this->submission_success === true;
    }

    // Scopes
    public function scopeStaff($query)
    {
        return $query->where('is_staff', true);
    }

    public function scopeStudents($query)
    {
        return $query->where('is_staff', false);
    }

    public function scopePending($query)
    {
        return $query->whereNull('score');
    }

    public function scopeUnsubmitted($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('submission_success')
              ->orWhere('submission_success', false);
        });
    }
}
