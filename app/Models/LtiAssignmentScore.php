<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LtiAssignmentScore extends Model
{
    protected $fillable = [
        'user_id',
        'lti_resource_link_id',
        'lti_user_id',
        'score',
        'score_maximum',
        'activity_event_id',
        'completed_at',
        'submitted_at',
        'submission_success',
        'submission_error',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'score_maximum' => 'decimal:2',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'submission_success' => 'boolean',
    ];

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

    /**
     * Has this assignment been completed by the user?
     */
    public function isCompleted(): bool
    {
        return !is_null($this->score);
    }

    /**
     * Has this score been successfully submitted to Canvas?
     */
    public function isSubmitted(): bool
    {
        return $this->submission_success === true;
    }

    /**
     * Scope to get only uncompleted assignments
     */
    public function scopeUncompleted($query)
    {
        return $query->whereNull('score');
    }

    /**
     * Scope to get only unsubmitted assignments
     */
    public function scopeUnsubmitted($query)
    {
        return $query->whereNull('submission_success')
            ->orWhere('submission_success', false);
    }
}
