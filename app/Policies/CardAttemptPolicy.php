<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\CardAttempt;
use App\Models\User;

class CardAttemptPolicy
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
    public function view(User $user, CardAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Card $card): bool
    {
        // if a user can view a card, they can create an attempt
        return $user->can('view', $card);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CardAttempt $cardAttempt): bool
    {
        return $user->id === $cardAttempt->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CardAttempt $cardAttempt): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CardAttempt $cardAttempt): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CardAttempt $cardAttempt): bool
    {
        return false;
    }
}
