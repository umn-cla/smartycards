<?php

namespace App\Http\Controllers;

use App\Enums\ActivityTypeEnum;
use App\Models\ActivityEvent;
use App\Models\Card;
use App\Models\CardAttempt;
use Gate;
use Illuminate\Http\Request;

class CardAttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Card $card)
    {
        Gate::authorize('viewOwn', CardAttempt::class);

        // only return attempts for the authenticated user
        $attempts = $card->attempts->where('user_id', $request->user()->id);

        return response()->json($attempts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Card $card)
    {
        Gate::authorize('create', [CardAttempt::class, $card]);

        $validated = $request->validate([
            'score' => 'required|integer',
            'prompt_side' => 'required|in:front,back',
        ]);

        $attempt = CardAttempt::create([
            'user_id' => $request->user()->id,
            'card_id' => $card->id,
            'score' => $validated['score'],
            'prompt_side' => $validated['prompt_side'],
        ]);
        $attempt->deck_id = $attempt->card->deck_id;

        // award experience points
        ActivityEvent::awardXP(
            userId: $request->user()->id,
            deckId: $attempt->card->deck->id,
            activityType: ActivityTypeEnum::PRACTICE_CARD,
            xp: 1
        );

        return response()->json($attempt, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CardAttempt $cardAttempt)
    {
        Gate::authorize('view', $cardAttempt);

        return response()->json($cardAttempt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CardAttempt $cardAttempt)
    {
        Gate::authorize('update', $cardAttempt);

        $validated = $request->validate([
            'score' => 'required|integer',
        ]);

        $cardAttempt->update([
            'score' => $validated['score'],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CardAttempt $cardAttempt)
    {
        Gate::authorize('delete', $cardAttempt);

        $cardAttempt->delete();
    }
}
