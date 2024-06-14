<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeckMembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Deck $deck)
    {
        Gate::authorize('view', $deck);

        $memberships = $deck->memberships->load('user');

        return response()->json($memberships);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Deck $deck)
    {
        $validated = $request->validate([
            'deck_id' => 'required|exists:decks,id',
            'email' => 'required|email|',
            'role' => 'required|string|in:viewer,editor',
        ]);

        // check if user exists, and create it if not
        // TODO: make sure Shib refreshes user data
        // if user is created here
        $user = User::firstOrCreate([
            'email' => $validated['email'],
            [
                'name' => Str::before($validated['email'], '@'),
                'password' => bcrypt(Str::random(16)),
            ],
        ]);

        // check if user is already a member of the deck
        if ($deck->memberships()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this deck.',
            ], 409);
        }

        $deckMembership = DeckMembership::create($validated);

        return response()->json($deckMembership, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeckMembership $deckMembership)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeckMembership $deckMembership)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeckMembership $deckMembership)
    {
        //
    }
}
