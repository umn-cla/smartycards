<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeckResource;
use App\Imports\CardsImport;
use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DeckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewOwn', Deck::class);

        $decks = $request->user()->decks()->withUserDetails()->get();

        return DeckResource::collection($decks);
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
            'is_tts_enabled' => 'boolean|nullable',
            'tts_locale_front' => 'string|nullable',
            'tts_locale_back' => 'string|nullable',
        ]);

        $deck = Deck::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_tts_enabled' => $validated['is_tts_enabled'] ?? false,
            'tts_locale_front' => $validated['tts_locale_front'] ?? null,
            'tts_locale_back' => $validated['tts_locale_back'] ?? null,
        ]);

        $deck->memberships()->create([
            'user_id' => $request->user()->id,
            'role' => 'owner',
        ]);

        return DeckResource::make($deck->fresh())
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $deckId)
    {
        $user = $request->user();

        $deck = Deck::query()
            ->withUserDetails($user)
            ->with([
                'cards' => function ($query) use ($user) {
                    $query->withUserStats($user);
                },
            ])
            ->findOrFail($deckId);

        Gate::authorize('view', $deck);

        if ($user->can('viewMemberships', $deck)) {
            $deck->load('memberships');
        }

        return DeckResource::make($deck);
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
            'is_tts_enabled' => 'boolean|nullable',
            'tts_locale_front' => 'string|nullable',
            'tts_locale_back' => 'string|nullable',
        ]);

        $deck->update([
            'name' => $validated['name'] ?? $deck->name,
            'description' => $validated['description'] ?? $deck->description,
            'is_tts_enabled' => $validated['is_tts_enabled'] ?? $deck->is_tts_enabled,
            'tts_locale_front' => $validated['tts_locale_front'] ?? $deck->tts_locale_front,
            'tts_locale_back' => $validated['tts_locale_back'] ?? $deck->tts_locale_back,
        ]);

        return DeckResource::make($deck->fresh());
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

    public function import(Deck $deck)
    {
        Gate::authorize('update', $deck);

        $validated = request()->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new CardsImport($deck->id), $validated['file']);

        return response()->json(null, 204);
    }

    public function publicDecks(Request $request)
    {
        $decks = Deck::query()
            ->where('is_public', true)
            ->withCurrentUserRole($request->user())
            ->get();

        return DeckResource::collection($decks);
    }

    public function clone(Request $request, Deck $deck)
    {
        Gate::authorize('clone', $deck);

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['string', 'nullable'],
            'is_tts_enabled' => ['boolean', 'nullable'],
        ]);

        $newDeck = $deck->create($validated);

        $deck->cards->each(function ($card) use ($newDeck) {
            $newDeck->cards()->create($card->toArray());
        });

        // enroll the current user as the owner of the new deck
        $newDeck->memberships()->create([
            'user_id' => $request->user()->id,
            'role' => 'owner',
        ]);

        // refresh to get all attributes like `is_public`
        return DeckResource::make($newDeck->fresh())
            ->response()
            ->setStatusCode(201);
    }

    public function stats(Deck $deck)
    {
        Gate::authorize('view', $deck);

        $stats = $deck->getStats();

        return response()->json($stats);
    }
}
