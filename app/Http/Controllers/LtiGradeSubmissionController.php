<?php

namespace App\Http\Controllers;

use App\Jobs\SubmitLtiGrade;
use App\Models\Deck;
use App\Models\LtiGradeSubmission;
use Gate;

class LtiGradeSubmissionController extends Controller
{
    public function retry(LtiGradeSubmission $submission)
    {
        $deck = $submission->resourceLink->deck;
        Gate::authorize('viewReports', [Deck::class, $deck]);

        // Create a new submission record to preserve audit trail
        $newSubmission = LtiGradeSubmission::create([
            'lti_resource_link_id' => $submission->lti_resource_link_id,
            'user_id' => $submission->user_id,
            'activity_event_id' => $submission->activity_event_id,
            'score_given' => $submission->score_given,
            'score_maximum' => $submission->score_maximum,
            'activity_progress' => $submission->activity_progress,
            'grading_progress' => $submission->grading_progress,
            'lti_user_id' => $submission->lti_user_id,
            'launch_id' => $submission->launch_id,
            'submitted_at' => now(),
            'success' => false,
        ]);

        // Dispatch the job
        SubmitLtiGrade::dispatch($newSubmission);

        return response()->json([
            'message' => 'Grade submission queued for retry',
            'submission_id' => $newSubmission->id,
        ]);
    }
}
