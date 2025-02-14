<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;

class CardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Card $card): bool
    {
        return $user->can('view', $card->deck);
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
    public function update(User $user, Card $card): bool
    {
        return $user->hasRoleInDeck($card->deck, ['owner', 'editor']);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Card $card): bool
    {
        return $user->hasRoleInDeck($card->deck, ['owner', 'editor']);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Card $card): bool
    {
        return $user->hasRoleInDeck($card->deck, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Card $card): bool
    {
        return false;
    }
}
