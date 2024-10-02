<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeckMembershipResource;
use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
     * Display the specified resource.
     */
    public function show(DeckMembership $membership)
    {
        Gate::authorize('view', $membership);
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
    public function destroy(DeckMembership $membership)
    {
        Gate::authorize('delete', $membership);

        $membership->delete();

        return response()->noContent();
    }

    public function leave(Request $request, Deck $deck)
    {
        Gate::authorize('leave', $deck);

        $membership = $deck->memberships()->where('user_id', $request->user()->id)->first();

        if (! $membership) {
            return response()->json([
                'message' => 'User is not a member of this deck.',
            ], 404);
        }

        $membership->delete();

        return response()->noContent();
    }

    public function joinAsViewer(Request $request, Deck $deck)
    {
        // if user is already a member of the deck, return 409
        $currentUserId = $request->user()->id;
        $isMemberOfDeck = $deck->memberships()->where('user_id', $currentUserId)->exists();
        if ($isMemberOfDeck) {
            return response()->json([
                'message' => 'User is already a member of this deck.',
            ], 409); // conflict
        }

        Gate::authorize('joinAsViewer', $deck);

        $membership = $deck->memberships()->create([
            'user_id' => $request->user()->id,
            'role' => DeckMembership::ROLE_VIEWER,
        ]);

        return DeckMembershipResource::make($membership)
            ->response()
            ->setStatusCode(201);
    }

    public function shareView(Deck $deck)
    {
        Gate::authorize('update', $deck);

        $signedURL = URL::signedRoute(
            'decks.memberships.acceptInvite',
            [
                'deck' => $deck->id,
                'fromUserId' => Auth::user()->id,
                'role' => 'viewer',
            ],
            expiration: null,
        );

        return response()->json(['url' => $signedURL]);
    }

    public function shareEdit(Deck $deck)
    {
        Gate::authorize('update', $deck);

        $signedURL = URL::signedRoute(
            'decks.memberships.acceptInvite',
            [
                'deck' => $deck->id,
                'fromUserId' => Auth::user()->id,
                'role' => 'editor',
            ],
            expiration: null,
        );

        return response()->json(['url' => $signedURL]);
    }
}
