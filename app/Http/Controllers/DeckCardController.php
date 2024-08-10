<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;

class DeckCardController extends Controller
{
    private function validateCardRequest(Request $request)
    {
        return $request->validate([
            // front content blocks
            'front' => ['required', 'array'],
            'front.*.id' => ['required', 'uuid'],
            'front.*.type' => ['required', 'string', 'in:text,image,audio,embed,video,reveal'],
            'front.*.content' => ['required', 'string'],
            'front.*.meta' => ['nullable', 'array'],
            'front.*.meta.*' => ['nullable', 'string'],

            // back content blocks
            'back' => ['required', 'array'],
            'back.*.id' => ['required', 'uuid'],
            'back.*.type' => ['required', 'string', 'in:text,image,audio,embed,video,reveal'],
            'back.*.content' => ['required', 'string'],
            'back.*.meta' => ['nullable', 'array'],
            'back.*.meta.*' => ['nullable', 'string'],
        ]);
    }

    public function show(Card $card)
    {
        Gate::authorize('view', $card);

        $user = auth()->user();
        $cardWithStats = $card->loadUserStats($user);

        return CardResource::make($cardWithStats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Deck $deck)
    {

        $validated = $this->validateCardRequest($request);

        Gate::authorize('create', [
            Card::class,
            $deck->id,
        ]);

        $card = Card::create([
            'deck_id' => $deck->id,
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
        $validated = $this->validateCardRequest($request);

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
