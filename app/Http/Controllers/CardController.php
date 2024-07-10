<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Models\Card;
use Gate;
use Illuminate\Http\Request;

class CardController extends Controller
{
    private function getCardSideRules()
    {
        return [
            'type' => 'required|string|in:text,image,audio,embed',
            'content' => 'required|string',
            'meta' => [
                'required',
                'array',
                'hint' => 'string',
                'alt' => 'nullable|string',
            ],
        ];
    }

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
            'front' => ['required', 'array', $this->getCardSideRules()],
            'back' => ['required', 'array', $this->getCardSideRules()],
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
            'front' => [
                'required', 'array', $this->getCardSideRules(),
            ],
            'back' => [
                'required', 'array', $this->getCardSideRules(),
            ],
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
