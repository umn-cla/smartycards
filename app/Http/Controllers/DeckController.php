<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewOwn', Deck::class);

        $usersDecks = $request->user()->decks;

        return response()->json($usersDecks->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Deck::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'string|nullable',
        ]);

        $deck = Deck::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $deck->memberships()->create([
            'user_id' => $request->user()->id,
            'role' => 'owner',
        ]);

        return response()->json($deck->toArray(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Deck $deck)
    {
        Gate::authorize('view', $deck);

        // load the relationships
        $deck->load(['cards'])->loadCount('memberships');

        return response()->json($deck->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deck $deck)
    {
        Gate::authorize('update', $deck);

        $validated = $request->validate([
            'name' => 'string',
            'description' => 'string|nullable',
        ]);

        $deck->update([
            'name' => $validated['name'] ?? $deck->name,
            'description' => $validated['description'] ?? $deck->description,
        ]);

        return response()->json($deck->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deck $deck)
    {
        Gate::authorize('delete', $deck);

        $deck->delete();

        return response()->json(null, 204);
    }
}
