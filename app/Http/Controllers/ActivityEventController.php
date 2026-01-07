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

        // Get user's LTI assignments for this deck (if any)
        $assignments = $ltiService->getAssignmentsForUserAndDeck(
            userId: Auth::id(),
            deckId: $deck->id
        );

        // users can have more than one assignment for a deck.
        // we want the ones that don't have score submissions yet
        // and if there are multiple assignments without score submissions
        // pick the one with the most recent launch
        $gradeableAssignment = $assignments?->filter(function ($assignment) {
            return is_null($assignment->submission_id);
        })->sortByDesc('last_launch_at')->first();

        $ltiResourceLinkId = $gradeableAssignment?->lti_resource_link_id ?? null;

        // record the event with the LTI resource link if we have one
        $event = ActivityEvent::create([
            'deck_id' => $deck->id,
            'activity_type_id' => $activityType->id,
            'user_id' => Auth::id(),
            'lti_resource_link_id' => $ltiResourceLinkId,
            'xp' => $xp,
        ]);

        // Queue grade submission if we have a gradeable assignment
        $gradeSubmission = null;

        if ($gradeableAssignment !== null) {
            try {
                $gradeSubmission = $ltiService->queueGradeSubmission(
                    assignment: $gradeableAssignment,
                    launchId: $validated['launch_id'] ?? 'deferred',
                    userId: Auth::id(),
                    activityEventId: $event->id,
                    scoreGiven: 100.0,
                    scoreMaximum: 100.0
                );

                \Log::info('Grade submission queued for Canvas via LTI', [
                    'activity_event_id' => $event->id,
                    'grade_submission_id' => $gradeSubmission?->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to queue grade submission to Canvas', [
                    'error' => $e->getMessage(),
                    'activity_event_id' => $event->id,
                    'lti_resource_link_id' => $ltiResourceLinkId,
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
