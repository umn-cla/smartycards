<?php

namespace App\Http\Controllers;

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
        ]);

        $attempt = CardAttempt::create([
            'user_id' => $request->user()->id,
            'card_id' => $card->id,
            'score' => $validated['score'],
        ]);

        $attempt->deck_id = $attempt->card->deck_id;

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
