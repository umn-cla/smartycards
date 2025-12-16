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
    ];

    protected $casts = [
        'score_given' => 'decimal:2',
        'score_maximum' => 'decimal:2',
        'submitted_at' => 'datetime',
        'success' => 'boolean',
        'request_payload' => 'array',
        'response_data' => 'array',
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
     * Scope to get only the latest submission per user
     * Uses a subquery to find the maximum submitted_at per user, then joins to get those records
     */
    public function scopeLatestPerUser($query)
    {
        $latestSubmissions = static::query()
            ->selectRaw('user_id, lti_resource_link_id, MAX(submitted_at) as max_submitted_at')
            ->groupBy('user_id', 'lti_resource_link_id');

        return $query
            ->joinSub(
                $latestSubmissions,
                'latest',
                function ($join) {
                    $join->on('lti_grade_submissions.user_id', '=', 'latest.user_id')
                        ->on('lti_grade_submissions.lti_resource_link_id', '=', 'latest.lti_resource_link_id')
                        ->on('lti_grade_submissions.submitted_at', '=', 'latest.max_submitted_at');
                }
            );
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
