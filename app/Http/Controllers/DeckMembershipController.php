<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeckMembershipResource;
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
        Gate::authorize('viewAny', [DeckMembership::class, $deck]);

        $memberships = $deck->memberships->load('user');

        return DeckMembershipResource::collection($memberships);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Deck $deck)
    {

        Gate::authorize('create', [DeckMembership::class, $deck]);

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|string|in:viewer,editor',
        ]);

        // check if user exists, and create it if not
        // TODO: make sure Shib refreshes user data
        // if user is created here
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['email'],
                'password' => bcrypt(Str::random(16)),
                'umndid' => $validated['email'],
            ],
        );

        // check if user is already a member of the deck
        if ($deck->memberships()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this deck.',
            ], 409);
        }

        $deckMembership = DeckMembership::create([
            'deck_id' => $deck->id,
            'user_id' => $user->id,
            'role' => $validated['role'],
        ]);

        return DeckMembershipResource::make($deckMembership)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeckMembership $deckMembership)
    {
        Gate::authorize('view', $deckMembership);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeckMembership $membership)
    {
        Gate::authorize('update', $membership);

        $validated = $request->validate([
            'role' => 'required|string|in:viewer,editor',
        ]);

        $membership->update($validated);

        return DeckMembershipResource::make($membership);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeckMembership $deckMembership)
    {
        Gate::authorize('delete', $deckMembership);

        $deckMembership->delete();

        return response()->noContent();
    }
}
