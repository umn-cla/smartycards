<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\LtiGradeSubmission;
use Gate;

class LtiResourceLinkController extends Controller
{
    /**
     * Get the current user's assignments (LTI resource links) for a deck
     */
    public function userAssignments(Deck $deck)
    {
        Gate::authorize('view', $deck);

        $user = request()->user();

        // Get all resource links for this deck where the user has a membership
        $resourceLinks = $deck->ltiResourceLinks()
            ->forUser($user)
            ->with(['ltiResourceLinkMemberships' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        // Get the user's latest grade submissions for these resource links
        $resourceLinkIds = $resourceLinks->pluck('id');
        $submissions = LtiGradeSubmission::query()
            ->whereIn('lti_grade_submissions.lti_resource_link_id', $resourceLinkIds)
            ->where('lti_grade_submissions.user_id', $user->id)
            ->latestPerUser()
            ->get()
            ->keyBy('lti_resource_link_id');

        // Format the response
        $assignments = $resourceLinks->map(function ($resourceLink) use ($submissions) {
            $membership = $resourceLink->ltiResourceLinkMemberships->first();
            $isStaff = $membership?->is_staff ?? false;

            $submission = $submissions->get($resourceLink->id);

            return [
                'id' => $resourceLink->id,
                'title' => $resourceLink->title,
                'description' => $resourceLink->description,
                'context_id' => $resourceLink->context_id,
                'context_title' => $resourceLink->context_title,
                'context_label' => $resourceLink->context_label,
                'canvas_url' => $resourceLink->getCanvasUrl(),
                'is_staff' => $isStaff,
                'score' => ($submission && ! $isStaff) ? [
                    'score_given' => $submission->score_given,
                    'score_maximum' => $submission->score_maximum,
                    'score_percentage' => $submission->getScorePercentage(),
                    'submitted_at' => $submission->submitted_at,
                ] : null,
            ];
        });

        return response()->json([
            'assignments' => $assignments,
        ]);
    }
}
