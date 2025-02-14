<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;

class DeckMembershipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Deck $deck): bool
    {
        return $user->hasRoleInDeck($deck, 'owner');

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeckMembership $deckMembership): bool
    {
        return $user->hasRoleInDeck($deckMembership->deck, 'owner');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Deck $deck): bool
    {
        return $user->hasRoleInDeck($deck, 'owner');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeckMembership $deckMembership): bool
    {
        return $user->hasRoleInDeck($deckMembership->deck, 'owner');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeckMembership $deckMembership): bool
    {

        // a user can remove themselves from a deck
        if ($user->id === $deckMembership->user_id) {
            return $user->can('removeSelf', $deckMembership);
        }

        // prevent deletion of owner roles
        if ($deckMembership->role === 'owner') {
            return false;
        }

        return $user->hasRoleInDeck($deckMembership->deck, 'owner');
    }

    public function removeSelf(User $user, DeckMembership $deckMembership): bool
    {
        $deck = $deckMembership->deck;
        if (! $user->isOwnerOfDeck($deck)) {
            return true;
        }

        // if the user is the only owner, they cannot remove themselves
        $ownerCount = $deck->memberships()->where('role', 'owner')->count();

        return $ownerCount > 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeckMembership $deckMembership): bool
    {
        return $user->hasRoleInDeck($deckMembership->deck, 'owner');

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeckMembership $deckMembership): bool
    {
        return false;
    }
}
