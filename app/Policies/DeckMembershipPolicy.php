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
        return $user->hasRoleInDeck($deck, ['owner', 'editor']);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeckMembership $deckMembership): bool
    {
        return $user->hasRoleInDeck($deckMembership->deck, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Deck $deck): bool
    {
        return $user->hasRoleInDeck($deck, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeckMembership $deckMembership): bool
    {
        // for now, just prevent any changes to owner roles
        if ($deckMembership->role === 'owner') {
            return false;
        }

        return $user->hasRoleInDeck($deckMembership->deck, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeckMembership $deckMembership): bool
    {
        // prevent deletion of owner roles
        if ($deckMembership->role === 'owner') {
            return false;
        }

        return $user->hasRoleInDeck($deckMembership->deck, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeckMembership $deckMembership): bool
    {
        return $user->hasRoleInDeck($deckMembership->deck, ['owner', 'editor']);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeckMembership $deckMembership): bool
    {
        return false;
    }
}
