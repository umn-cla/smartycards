<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\DeckMembership;
use App\Models\User;

class DeckPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function viewOwn(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Deck $deck): bool
    {
        return $user->isMemberOfDeck($deck) || $deck->is_public;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    public function clone(User $user, Deck $deck): bool
    {
        return $user->isMemberOfDeck($deck) || $deck->is_public;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Deck $deck): bool
    {
        return $user->isOwnerOfDeck($deck);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deck $deck): bool
    {
        return $user->isOwnerOfDeck($deck);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Deck $deck): bool
    {
        return $user->isOwnerOfDeck($deck);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Deck $deck): bool
    {
        return false;
    }

    public function viewMemberships(User $user, Deck $deck): bool
    {
        return $user->can('viewAny', [DeckMembership::class, $deck]);
    }

    public function createMembership(User $user, Deck $deck): bool
    {
        return $user->can('create', [DeckMembership::class, $deck]);
    }

    public function viewReports(User $user, Deck $deck): bool
    {
        return $user->isOwnerOfDeck($deck);
    }

    public function leave(User $user, Deck $deck): bool
    {
        $deckMembership = $user->memberships()->where('deck_id', $deck->id)->first();

        if (! $deckMembership) {
            return false;
        }

        return $user->can('removeSelf', $deckMembership);
    }

    public function joinAsViewer(User $user, Deck $deck): bool
    {
        return $deck->is_public;
    }
}
