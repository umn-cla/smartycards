<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;
use Illuminate\Http\Request;

class DeckInviteController extends Controller
{
    private function hasValidToken(string $token, Deck $deck, string $role): bool
    {
        $permission = $role === 'viewer' ? 'view' : 'edit';

        return $deck->tokens()->where('token', $token)
            ->where('permission', $permission)
            ->where('deck_id', $deck->id)
            ->exists();
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Deck $deck)
    {
        $validated = $request->validate([
            'fromUserId' => 'required|exists:users,id',
            'role' => 'required|string|in:viewer,editor',
            'token' => 'required|string|exists:deck_invite_tokens,token',
        ]);

        // verify that the token is valid for deck and role
        if (! $this->hasValidToken(token: $validated['token'], deck: $deck, role: $validated['role'])) {
            abort(403, 'Invalid token.');
        }

        // verify that the invitingUser has permission to invite
        // if the invitation is from a user who
        // no longer has privileges to update the deck, 403
        $fromUser = User::find($validated['fromUserId']);
        if ($fromUser?->cannot('update', $deck)) {
            abort(403, 'User does not have permission to invite to this deck.');
        }

        $userId = $request->user()->id;
        $existingMembership = DeckMembership::where('deck_id', $deck->id)
            ->where('user_id', $userId)
            ->withTrashed()
            ->first();

        if (! $existingMembership) {
            DeckMembership::create([
                'deck_id' => $deck->id,
                'user_id' => $userId,
                'role' => $validated['role'],
            ]);
        } elseif ($existingMembership->trashed()) {
            $existingMembership->restore();
            $existingMembership->update([
                'role' => $validated['role'],
            ]);
        } elseif ($existingMembership->isRoleAPromotion($validated['role'])) {
            $existingMembership->update([
                'role' => $validated['role'],
            ]);
        }

        return redirect("/decks/{$deck->id}");
    }
}
