<?php

namespace App\Policies;

use App\Models\Deck;
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
        return $user->isMemberOfDeck($deck);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Deck $deck): bool
    {
        return $user->memberships()->where('deck_id', $deck->id)->whereIn('role', ['editor', 'owner'])->exists();
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
        return $user->hasRoleInDeck($deck, ['owner', 'editor']);
    }

    public function updateMemberships(User $user, Deck $deck): bool
    {
        return $user->hasRoleInDeck($deck, ['owner', 'editor']);
    }
}
