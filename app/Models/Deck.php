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
    public function scopeWithLastAttemptedAt(Builder $query, $userId): Builder
    {
        return $query->addSelect(['last_attempted_at' => CardAttempt::select('created_at')
            ->where('user_id', $userId)
            ->latest()
            ->take(1),
        ]);
    }

    public function scopeWithUserAvgScore(Builder $query, $userId): Builder
    {
        return $query->withAvg(['cardAttempts as avg_score' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }], 'score');
    }

    public function scopeWithUserDetails(Builder $query, $userId): Builder
    {
        return $query->with('currentUserMemberships')
            ->withCount('cards')
            ->withCount('memberships')
            ->withLastAttemptedAt($userId)
            ->withUserAvgScore($userId);
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
