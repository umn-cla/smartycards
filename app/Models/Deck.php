<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $casts = [
        'last_attempted_at' => 'datetime',
        'avg_score' => 'float',
    ];

    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'deck_memberships')->withPivot('role');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function memberships()
    {
        return $this->hasMany(DeckMembership::class);
    }

    public function cardAttempts()
    {
        return $this->hasManyThrough(CardAttempt::class, Card::class);
    }

    /**
     * Scope a query to include the last attempted at date for the deck.
     */
    public function scopeWithLastAttemptedAt(Builder $query, User $user): Builder
    {
        $subQuery = CardAttempt::select('card_attempts.created_at')
            ->join('cards', 'cards.id', '=', 'card_attempts.card_id')
            ->whereColumn('cards.deck_id', 'decks.id')
            ->where('card_attempts.user_id', $user->id)
            ->latest('card_attempts.created_at')
            ->take(1);

        return $query->addSelect(['last_attempted_at' => $subQuery]);
    }

    /**
     * Scope a query to include the user's average score per card.
     */
    public function scopeWithUserAvgScore(Builder $query, User $user): Builder
    {
        $subQuery = Card::selectRaw('AVG(card_attempts.score) as avg_card_score')
            ->join('card_attempts', 'cards.id', '=', 'card_attempts.card_id')
            ->whereColumn('cards.deck_id', 'decks.id')
            ->where('card_attempts.user_id', $user->id)
            ->groupBy('cards.id');

        return $query->addSelect(['avg_score' => $subQuery->avg('avg_card_score')]);
    }

    public function scopeWithUserStats(Builder $query, User $user): Builder
    {
        return $query->with('currentUserMemberships')
            ->withCount('cards')
            ->withCount('memberships')
            ->withLastAttemptedAt($user)
            ->withUserAvgScore($user);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->memberships()->where('user_id', $user->id)->where('role', 'owner')->exists();
    }

    public function hasMember(User $user): bool
    {
        return $this->memberships()->where('user_id', $user->id)->exists();
    }

    public function currentUserMemberships()
    {
        $currentUserId = auth()->id();

        return $this->memberships()->where('user_id', $currentUserId);
    }
}
