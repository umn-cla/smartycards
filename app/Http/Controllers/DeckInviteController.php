<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;
use Illuminate\Http\Request;

class DeckInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Deck $deck)
    {
        $validated = $request->validate([
            'fromUserId' => 'required|exists:users,id',
            'role' => 'required|string|in:viewer,editor',
        ]);

        // verify that the invitingUser has permission to invite
        // if the invitation is from a user who
        // no longer has privileges to update the deck, 403
        $fromUser = User::find($validated['fromUserId']);
        if ($fromUser?->cannot('update', $deck)) {
            abort(403, 'User does not have permission to invite to this deck.');
        }

        DeckMembership::updateOrCreate([
            'deck_id' => $deck->id,
            'user_id' => $request->user()->id,
        ], [
            'role' => $validated['role'],
        ]);

        // redirect to the deck
        return redirect("/decks/{$deck->id}");
    }
}
