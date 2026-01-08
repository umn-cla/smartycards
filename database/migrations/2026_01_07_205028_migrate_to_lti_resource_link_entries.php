<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only migrate data if old tables exist
        if (!Schema::hasTable('lti_resource_link_memberships')) {
            return;
        }

        // Strategy: Start with memberships (always exist for any launch)
        // LEFT JOIN scores (may not exist if user hasn't completed)
        $entries = DB::table('lti_resource_link_memberships as m')
            ->leftJoin('lti_assignment_scores as s', function ($join) {
                $join->on('m.user_id', '=', 's.user_id')
                    ->on('m.lti_resource_link_id', '=', 's.lti_resource_link_id');
            })
            ->select([
                'm.user_id',
                'm.lti_resource_link_id',
                'm.lti_user_id',
                'm.roles',
                'm.is_staff',
                'm.last_launch_at',
                's.score',
                's.score_maximum',
                's.activity_event_id',
                's.completed_at',
                's.submitted_at',
                's.submission_success',
                's.submission_error',
                'm.created_at',
                'm.updated_at',
            ])
            ->get();

        foreach ($entries as $entry) {
            // Skip entries without lti_user_id (shouldn't happen in production)
            if (!$entry->lti_user_id) {
                continue;
            }

            // Use insertOrIgnore to handle potential duplicate entries gracefully
            DB::table('lti_resource_link_entries')->insertOrIgnore([
                'user_id' => $entry->user_id,
                'lti_resource_link_id' => $entry->lti_resource_link_id,
                'lti_user_id' => $entry->lti_user_id,
                'roles' => $entry->roles,
                'is_staff' => $entry->is_staff,
                'last_launch_at' => $entry->last_launch_at,
                'score' => $entry->score,
                'score_maximum' => $entry->score_maximum ?? 100.00,
                'activity_event_id' => $entry->activity_event_id,
                'completed_at' => $entry->completed_at,
                'submitted_at' => $entry->submitted_at,
                'submission_success' => $entry->submission_success,
                'submission_error' => $entry->submission_error,
                'created_at' => $entry->created_at,
                'updated_at' => $entry->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only migrate back if old tables exist
        if (!Schema::hasTable('lti_resource_link_memberships') || !Schema::hasTable('lti_assignment_scores')) {
            return;
        }

        $entries = DB::table('lti_resource_link_entries')->get();

        foreach ($entries as $entry) {
            // Restore membership (use insertOrIgnore to handle potential duplicates)
            DB::table('lti_resource_link_memberships')->insertOrIgnore([
                'user_id' => $entry->user_id,
                'lti_resource_link_id' => $entry->lti_resource_link_id,
                'lti_user_id' => $entry->lti_user_id,
                'roles' => $entry->roles,
                'is_staff' => $entry->is_staff,
                'last_launch_at' => $entry->last_launch_at,
                'created_at' => $entry->created_at,
                'updated_at' => $entry->updated_at,
            ]);

            // Only restore score if it was completed (use insertOrIgnore to handle potential duplicates)
            if ($entry->score !== null) {
                DB::table('lti_assignment_scores')->insertOrIgnore([
                    'user_id' => $entry->user_id,
                    'lti_resource_link_id' => $entry->lti_resource_link_id,
                    'lti_user_id' => $entry->lti_user_id,
                    'score' => $entry->score,
                    'score_maximum' => $entry->score_maximum,
                    'activity_event_id' => $entry->activity_event_id,
                    'completed_at' => $entry->completed_at,
                    'submitted_at' => $entry->submitted_at,
                    'submission_success' => $entry->submission_success,
                    'submission_error' => $entry->submission_error,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                ]);
            }
        }
    }
};
