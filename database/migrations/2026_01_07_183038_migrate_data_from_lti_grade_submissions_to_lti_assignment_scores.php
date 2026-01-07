<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrate existing successful grade submissions to the new lti_assignment_scores table
     */
    public function up(): void
    {
        // Only migrate if the old table exists (it won't exist in production)
        if (!Schema::hasTable('lti_grade_submissions')) {
            return;
        }

        // Get all successful grade submissions (latest per user/resource)
        $submissions = DB::table('lti_grade_submissions')
            ->where('success', true)
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->groupBy(function ($submission) {
                return $submission->user_id.'-'.$submission->lti_resource_link_id;
            })
            ->map(function ($group) {
                return $group->first(); // Get most recent
            });

        foreach ($submissions as $submission) {
            DB::table('lti_assignment_scores')->insertOrIgnore([
                'user_id' => $submission->user_id,
                'lti_resource_link_id' => $submission->lti_resource_link_id,
                'lti_user_id' => $submission->lti_user_id,
                'score' => $submission->score_given,
                'score_maximum' => $submission->score_maximum,
                'activity_event_id' => $submission->activity_event_id,
                'completed_at' => $submission->submitted_at, // Best guess
                'submitted_at' => $submission->submitted_at,
                'submission_success' => $submission->success,
                'submission_error' => $submission->error_message,
                'created_at' => $submission->created_at ?? now(),
                'updated_at' => $submission->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lti_assignment_scores')->truncate();
    }
};
