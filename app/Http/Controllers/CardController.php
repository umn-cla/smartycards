<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Models\Card;
use Gate;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function show(Card $card)
    {
        Gate::authorize('view', $card);

        return CardResource::make($card);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'deck_id' => 'required|exists:decks,id',

            // json
            'front' => 'required|array',
            'front.type' => 'required|string|in:text,image,audio,embed',
            'front.content' => 'required|string',
            'front.metadata' => 'nullable|array',

            // json
            'back' => 'required|array',
            'back.type' => 'required|string|in:text,image,audio,embed',
            'back.content' => 'required|string',
            'back.metadata' => 'nullable|array',
        ]);

        Gate::authorize('create', [
            Card::class,
            $validated['deck_id'],
        ]);

        $card = Card::create([
            'deck_id' => $validated['deck_id'],
            'front' => $validated['front'],
            'back' => $validated['back'],
        ]);

        return CardResource::make($card)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            // json
            'front' => 'required|array',
            'front.type' => 'required|string|in:text,image,audio,embed',
            'front.content' => 'required|string',
            'front.meta' => 'required|array',
            'front.meta.hints' => 'required|array',
            'front.meta.hints.*.id' => 'required|string',
            'front.meta.hints.*.content' => 'required|string',
            'front.meta.hints.*.type' => 'required|string|in:text',
            'front.meta.alt' => 'nullable|string',

            // json
            'back' => 'required|array',
            'back.type' => 'required|string|in:text,image,audio,embed',
            'back.content' => 'required|string',
            'back.meta' => 'required|array',
            'back.meta.hints' => 'required|array',
            'back.meta.hints.*.id' => 'required|string',
            'back.meta.hints.*.content' => 'required|string',
            'back.meta.hints.*.type' => 'required|string|in:text',
            'back.meta.alt' => 'nullable|string',
        ]);

        Gate::authorize('update', $card);

        $card->update($validated);

        return CardResource::make($card->fresh());
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
