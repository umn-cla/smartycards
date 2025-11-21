<?php

namespace App\Http\Controllers;

use App\Enums\ActivityTypeEnum;
use App\Models\ActivityEvent;
use App\Models\ActivityType;
use App\Models\Deck;
use App\Services\Lti\LtiService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class ActivityEventController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Deck $deck, LtiService $ltiService)
    {
        Gate::authorize('create', [ActivityEvent::class, $deck]);

        // TODO: maybe we should rate limit or check some
        // sort of token to avoid potential spamming
        $validated = $request->validate([
            'activity_type_name' => [
                'required',
                new Enum(ActivityTypeEnum::class),
            ],
            'correct_count' => ['integer', 'nullable'],
            'total_count' => ['integer', 'nullable'],
            'lti_launch_id' => ['string', 'nullable'],
        ]);

        $activityType = ActivityType::where('name', $validated['activity_type_name'])->first();

        $xp = $activityType->getXPToAward(
            correctCount: $validated['correct_count'],
            totalCount: $validated['total_count']
        );

        // Determine resource link ID if this is an LTI context
        $ltiResourceLinkId = null;
        if (!empty($validated['lti_launch_id'])) {
            try {
                $launch = $ltiService->getLaunchFromCache($validated['lti_launch_id']);
                $resourceLink = $ltiService->createOrUpdateResourceLink($launch, $deck->id);
                $ltiResourceLinkId = $resourceLink->id;
            } catch (\Exception $e) {
                // Log but don't fail - gracefully degrade to non-LTI mode
                \Log::warning('Failed to get LTI resource link for activity event', [
                    'error' => $e->getMessage(),
                    'launch_id' => $validated['lti_launch_id'],
                ]);
            }
        }

        $event = ActivityEvent::create([
            'deck_id' => $deck->id,
            'activity_type_id' => $activityType->id,
            'user_id' => Auth::id(),
            'lti_resource_link_id' => $ltiResourceLinkId,
            'xp' => $xp,
        ]);

        // Submit grade to Canvas if this is an LTI context
        $gradeSubmission = null;
        if (!empty($validated['lti_launch_id'])) {
            try {
                // For v1: Always award full credit (100/100) on completion
                $gradeSubmission = $ltiService->submitGradeFromLaunchId(
                    launchId: $validated['lti_launch_id'],
                    userId: Auth::id(),
                    activityEventId: $event->id,
                    scoreGiven: 100.0,
                    scoreMaximum: 100.0
                );

                \Log::info('Grade submitted to Canvas via LTI', [
                    'activity_event_id' => $event->id,
                    'grade_submission_id' => $gradeSubmission->id,
                    'success' => $gradeSubmission->success,
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the activity event creation
                \Log::error('Failed to submit grade to Canvas', [
                    'error' => $e->getMessage(),
                    'activity_event_id' => $event->id,
                    'launch_id' => $validated['lti_launch_id'],
                ]);
            }
        }

        return response()->json([
            'activity_event' => $event,
            'grade_submission' => $gradeSubmission ? [
                'id' => $gradeSubmission->id,
                'success' => $gradeSubmission->success,
                'score_given' => $gradeSubmission->score_given,
                'score_maximum' => $gradeSubmission->score_maximum,
            ] : null,
        ], 201);
    }
}
