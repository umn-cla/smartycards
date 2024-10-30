<?php

namespace App\Policies;

use App\Models\ActivityEvent;
use App\Models\Deck;
use App\Models\User;

class ActivityEventPolicy
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
    public function view(User $user, ActivityEvent $activityEvent): bool
    {
        // users can view their own activity events
        // and deck owners can view activity events on their decks
        return $user->id === $activityEvent->user->id
        || $activityEvent->deck->isOwnedBy($user);
    }

    public function viewAnyForDeck(User $user, Deck $deck): bool
    {
        return $deck->isOwnedBy($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Deck $deck): bool
    {
        return $user->isMemberOfDeck($deck);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityEvent $activityEvent): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityEvent $activityEvent): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActivityEvent $activityEvent): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActivityEvent $activityEvent): bool
    {
        return false;
    }
}
