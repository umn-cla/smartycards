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

    public function acceptInvite(Request $request, Deck $deck)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $validated = $request->validate([
            'fromUserId' => 'required|exists:users,id',
            'role' => 'required|string|in:viewer,editor',
        ]);

        // verify that the invitingUser has permission to invite
        $fromUser = User::find($validated['fromUserId']);

        // if the invitation is from a user who
        // no longer has privileges to update the deck, 403
        if ($fromUser?->cannot('update', $deck)) {
            return response()->json([
                'message' => 'Invalid invitation.',
            ], 403);
        }

        // if user is already a member of the deck, update the role
        if ($deck->memberships()->where('user_id', $request->user()->id)->exists()) {

            // Don't update the membership if the user is already a member.
            // This is a workaround to allow owners to demote users from editor
            // to viewer if needed, after the edit link has been shared.
            return response()->json([
                'message' => 'User is already a member of this deck.',
            ], 409); // conflict
        }

        // add the user to the deck with the role of viewer
        $membership = DeckMembership::create([
            'deck_id' => $deck->id,
            'user_id' => $request->user()->id,
            'role' => $validated['role'],
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
