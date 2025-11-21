<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LtiGradeSubmission extends Model
{
    protected $fillable = [
        'lti_resource_link_id',
        'user_id',
        'activity_event_id',
        'score_given',
        'score_maximum',
        'activity_progress',
        'grading_progress',
        'lti_user_id',
        'launch_id',
        'submitted_at',
        'success',
        'error_message',
        'request_payload',
        'response_data',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'score_given' => 'decimal:2',
        'score_maximum' => 'decimal:2',
        'submitted_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'success' => 'boolean',
        'request_payload' => 'array',
        'response_data' => 'array',
        'retry_count' => 'integer',
    ];

    /**
     * The LTI resource link (Canvas assignment)
     */
    public function resourceLink(): BelongsTo
    {
        return $this->belongsTo(LtiResourceLink::class, 'lti_resource_link_id');
    }

    /**
     * The user who received the grade
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The activity event that triggered this grade submission
     */
    public function activityEvent(): BelongsTo
    {
        return $this->belongsTo(ActivityEvent::class);
    }

    /**
     * Check if this submission was successful
     */
    public function wasSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Get the percentage score
     */
    public function getScorePercentage(): float
    {
        if ($this->score_maximum == 0) {
            return 0;
        }

        return ($this->score_given / $this->score_maximum) * 100;
    }
}
