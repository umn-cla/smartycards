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

        $usersDecks = $request->user()->decks->loadCount(['cards']);

        return DeckResource::collection($usersDecks);
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

        return DeckResource::make($deck->fresh())
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Deck $deck)
    {
        Gate::authorize('view', $deck);

        $relationsToLoad = ['cards'];
        if ($request->user()->can('viewMemberships', $deck)) {
            $relationsToLoad[] = 'memberships';
        }

        $deck->load($relationsToLoad);

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
        ]);

        $deck->update([
            'name' => $validated['name'] ?? $deck->name,
            'description' => $validated['description'] ?? $deck->description,
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
}