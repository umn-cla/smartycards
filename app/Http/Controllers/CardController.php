<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Gate;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'front' => 'required|string',
            'back' => 'required|string',
            'deck_id' => 'required|exists:decks,id',
        ]);

        Gate::authorize('create', [
            Card::class,
            $validated['deck_id'],
        ]);

        return Card::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        Gate::authorize('update', $card);

        $validated = $request->validate([
            'front' => 'required|string',
            'back' => 'required|string',
        ]);

        $card->update($validated);

        return $card->fresh();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        Gate::authorize('delete', $card);

        $card->delete();

        return response()->noContent();
    }
}
