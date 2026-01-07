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
        ]);

        $activityType = ActivityType::where('name', $validated['activity_type_name'])->first();

        $xp = $activityType->getXPToAward(
            correctCount: $validated['correct_count'],
            totalCount: $validated['total_count']
        );

        // Get user's LTI entries for this deck (if any)
        $entries = $ltiService->getEntriesForUserAndDeck(
            userId: Auth::id(),
            deckId: $deck->id
        );

        // Users can have more than one assignment for a deck.
        // We want the ones that haven't been completed yet (score is null)
        // If there are multiple incomplete entries, pick the most recent one
        $pendingEntry = $entries
            ->filter(fn ($entry) => !$entry->isCompleted())
            ->sortByDesc('updated_at')
            ->first();

        $ltiResourceLinkId = $pendingEntry?->lti_resource_link_id;

        // record the event with the LTI resource link if we have one
        $event = ActivityEvent::create([
            'deck_id' => $deck->id,
            'activity_type_id' => $activityType->id,
            'user_id' => Auth::id(),
            'lti_resource_link_id' => $ltiResourceLinkId,
            'xp' => $xp,
        ]);

        // Queue score submission if we have an incomplete entry
        $updatedEntry = null;

        if ($pendingEntry !== null) {
            try {
                $updatedEntry = $ltiService->queueScoreSubmission(
                    entry: $pendingEntry,
                    userId: Auth::id(),
                    activityEventId: $event->id,
                    score: 100.0,
                    scoreMaximum: 100.0
                );

                \Log::info('Score submission queued for Canvas via LTI', [
                    'activity_event_id' => $event->id,
                    'entry_id' => $updatedEntry->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to queue score submission to Canvas', [
                    'error' => $e->getMessage(),
                    'activity_event_id' => $event->id,
                    'lti_resource_link_id' => $ltiResourceLinkId,
                ]);
            }
        }

        return response()->json([
            'activity_event' => $event,
            'score' => $updatedEntry ? [
                'id' => $updatedEntry->id,
                'status' => 'queued',
                'score' => $updatedEntry->score,
                'score_maximum' => $updatedEntry->score_maximum,
            ] : null,
        ], 201);
    }
}
