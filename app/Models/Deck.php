<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Deck extends Model implements AuditableContract
{
    use AuditableTrait, HasFactory;

    protected $casts = [
        'last_attempted_at' => 'datetime',
        'avg_score' => 'float',
        'is_public' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'is_public',
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
        // Subquery to calculate the average score per card
        $avgScorePerCardSubQuery = Card::selectRaw('AVG(card_attempts.score) as avg_card_score')
            ->join('card_attempts', 'cards.id', '=', 'card_attempts.card_id')
            ->whereColumn('cards.deck_id', 'decks.id')
            ->where('card_attempts.user_id', $user->id)
            ->groupBy('cards.id');

        // Subquery to calculate the average of these averages
        $avgOfAveragesSubQuery = DB::table($avgScorePerCardSubQuery, 'avg_scores')
            ->selectRaw('AVG(avg_scores.avg_card_score)');

        // this might be a bad idea
        return $query->addSelect(['avg_score' => $avgOfAveragesSubQuery]);
    }

    public function scopeWithCurrentUserRole(Builder $query, User $user): Builder
    {
        return $query->addSelect([
            'current_user_role' => DeckMembership::select('role')
                ->whereColumn('deck_id', 'decks.id')
                ->where('user_id', $user->id)
                ->limit(1),
        ]);
    }

    public function scopeWithUserStats(Builder $query, User $user): Builder
    {
        return $query
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

    public function currentUserMembership()
    {
        return $this->memberships()->where('user_id', Auth::id())->first();
    }
}
