<?php

namespace App\Http\Controllers;

use App\Enums\ActivityTypeEnum;
use App\Models\ActivityEvent;
use App\Models\ActivityType;
use App\Models\Deck;
use App\Models\LtiResourceLinkMembership;
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

        $validated = $request->validate([
            'activity_type_name' => [
                'required',
                new Enum(ActivityTypeEnum::class),
            ],
            'correct_count' => ['integer', 'nullable'],
            'total_count' => ['integer', 'nullable'],
            'launch_id' => ['string', 'nullable'],
        ]);

        $activityType = ActivityType::where('name', $validated['activity_type_name'])->first();

        $xp = $activityType->getXPToAward(
            correctCount: $validated['correct_count'],
            totalCount: $validated['total_count']
        );

        // Handle LTI context: link activity to resource and queue grade submission
        $ltiResourceLinkId = null;
        $gradeSubmission = null;

        if (!empty($validated['launch_id'])) {
            try {
                $launch = $ltiService->getLaunchFromCache($validated['launch_id']);
                $resourceLink = $ltiService->createOrUpdateResourceLink($launch, $deck->id);
                $ltiResourceLinkId = $resourceLink->id;

                // Track user's role in this Canvas course for grade report authorization
                $ltiService->createOrUpdateMembership($launch, Auth::user(), $resourceLink);
            } catch (\Exception $e) {
                \Log::warning('Failed to get LTI resource link for activity event', [
                    'error' => $e->getMessage(),
                    'launch_id' => $validated['launch_id'],
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

        // Queue grade submission to Canvas if LTI context
        if (!empty($validated['launch_id'])) {
            try {
                $gradeSubmission = $ltiService->queueGradeSubmissionFromLaunchId(
                    launchId: $validated['launch_id'],
                    userId: Auth::id(),
                    activityEventId: $event->id,
                    scoreGiven: 100.0,
                    scoreMaximum: 100.0
                );

                \Log::info('Grade submission queued for Canvas via LTI', [
                    'activity_event_id' => $event->id,
                    'grade_submission_id' => $gradeSubmission->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to queue grade submission to Canvas', [
                    'error' => $e->getMessage(),
                    'activity_event_id' => $event->id,
                    'launch_id' => $validated['launch_id'],
                ]);
            }
        } else {
            // No launch_id: check for most recent uncompleted LTI assignment for this deck
            try {
                $membership = LtiResourceLinkMembership::ungradedForDeck($deck->id, Auth::id())->first();

                if ($membership) {
                    $gradeSubmission = $ltiService->queueGradeSubmissionFromMembership(
                        membership: $membership,
                        activityEventId: $event->id,
                        scoreGiven: 100.0,
                        scoreMaximum: 100.0
                    );

                    \Log::info('Deferred grade submission queued for Canvas', [
                        'activity_event_id' => $event->id,
                        'grade_submission_id' => $gradeSubmission->id,
                        'membership_id' => $membership->id,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to queue deferred grade submission to Canvas', [
                    'error' => $e->getMessage(),
                    'activity_event_id' => $event->id,
                    'deck_id' => $deck->id,
                ]);
            }
        }

        return response()->json([
            'activity_event' => $event,
            'grade_submission' => $gradeSubmission ? [
                'id' => $gradeSubmission->id,
                'status' => 'queued',
                'score_given' => $gradeSubmission->score_given,
                'score_maximum' => $gradeSubmission->score_maximum,
            ] : null,
        ], 201);
    }
}
