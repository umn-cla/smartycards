<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\LtiGradeSubmission;
use Gate;
use Auth;

class DeckReportController extends Controller
{
    public function summary(Deck $deck)
    {
        Gate::authorize('viewReports', [Deck::class, $deck]);

        return response()->json([
            'cards_count' => $deck->cards()->count(),
            'memberships_count' => $deck->memberships()->count(),
            'cards_with_stats' => $deck->cards()->withGlobalStats()->get(),
            'memberships_with_stats' => $deck
                ->memberships()
                ->with('user')
                ->withStats()
                ->get(),
        ]);
    }

    public function grades(Deck $deck)
    {
        Gate::authorize('viewGrades', [Deck::class, $deck]);

        // Filter to only Canvas courses where user has staff role
        // (Policy ensures user has staff role in at least one course)
        $resourceLinks = $deck->ltiResourceLinks()
            ->whereHas('memberships', function ($query) {
                $query->where('user_id', Auth::id())
                    ->where('is_staff', true);
            })
            ->get();

        if ($resourceLinks->isEmpty()) {
            return response()->json([
                'has_lti_context' => false,
                'resource_links' => [],
                'message' => 'No Canvas courses found where you have instructor access',
            ]);
        }

        $resourceLinkIds = $resourceLinks->pluck('id');

        // Get all latest submissions for all resource links in a single query
        $submissions = LtiGradeSubmission::query()
            ->whereIn('lti_grade_submissions.lti_resource_link_id', $resourceLinkIds)
            ->latestPerUser()
            ->with(['user', 'resourceLink'])
            ->orderBy('lti_grade_submissions.submitted_at', 'desc')
            ->get();

        // Group submissions by resource link
        $submissionsByResourceLink = $submissions->groupBy('lti_resource_link_id');

        // Build the response structure
        $resourceLinksWithSubmissions = $resourceLinks->map(function ($resourceLink) use ($submissionsByResourceLink) {
            $resourceLinkSubmissions = $submissionsByResourceLink->get($resourceLink->id, collect());

            $formattedSubmissions = $resourceLinkSubmissions->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'user' => $submission->user,
                    'score_given' => $submission->score_given,
                    'score_maximum' => $submission->score_maximum,
                    'score_percentage' => $submission->getScorePercentage(),
                    'success' => $submission->success,
                    'error_message' => $submission->error_message,
                    'submitted_at' => $submission->submitted_at,
                    'activity_progress' => $submission->activity_progress,
                    'grading_progress' => $submission->grading_progress,
                    'can_retry' => !$submission->success,
                ];
            });

            return [
                'resource_link' => [
                    'id' => $resourceLink->id,
                    'resource_link_id' => $resourceLink->resource_link_id,
                    'title' => $resourceLink->title,
                    'context_title' => $resourceLink->context_title,
                    'context_label' => $resourceLink->context_label,
                ],
                'submissions' => $formattedSubmissions,
            ];
        });

        return response()->json([
            'has_lti_context' => true,
            'resource_links' => $resourceLinksWithSubmissions,
        ]);
    }
}
