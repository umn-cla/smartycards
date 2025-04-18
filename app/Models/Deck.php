<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Deck extends Model implements AuditableContract
{
    use AuditableTrait, HasFactory, SoftDeletes;

    protected $casts = [
        'last_attempted_at' => 'datetime',
        'avg_score' => 'float',
        'is_public' => 'boolean',
        'current_user_xp' => 'integer',
        'is_tts_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'is_public',
        'is_tts_enabled',
        'tts_locale_front',
        'tts_locale_back',
    ];

    protected static function booted()
    {
        static::created(function ($deck) {
            foreach (['view', 'edit'] as $permission) {
                DeckInviteToken::create([
                    'deck_id' => $deck->id,
                    'permission' => $permission,
                    'token' => Str::random(32),
                ]);
            }
        });
    }

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

    public function tokens()
    {
        return $this->hasMany(DeckInviteToken::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function userActvities($userId)
    {
        return $this->hasMany(ActivityEvent::class)->where('user_id', $userId);
    }

    public function getTokenForPermission($permission)
    {
        return $this->tokens()->where('permission', $permission)->first();
    }

    public function rotateTokenForPermission($permission)
    {
        $this->getTokenForPermission($permission)->rotate();
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeWithUserMembership(Builder $query, User $user): Builder
    {
        return $query->addSelect([
            'user_role' => DeckMembership::select('role')
                ->whereColumn('deck_id', 'decks.id')
                ->where('user_id', $user->id)
                ->limit(1),
        ]);
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

    public function scopeWithUserDetails(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return $query->withCount(['cards', 'memberships']);
        }

        return $query
            ->withCount('cards')
            ->withCount('memberships')
            ->addSelect([
                'current_user_xp' => ActivityEvent::selectRaw('SUM(xp)')
                    ->whereColumn('deck_id', 'decks.id')
                    ->where('user_id', $user->id),

                'last_activity_at' => ActivityEvent::selectRaw('MAX(updated_at)')
                    ->whereColumn('deck_id', 'decks.id')
                    ->where('user_id', $user->id),

                'current_user_role' => $user->memberships()
                    ->select('role')
                    ->whereColumn('deck_id', 'decks.id')
                    ->limit(1),
            ]);
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

    public function userXP(User $user): int
    {
        return (int) $this->activities()->where('user_id', $user->id)->sum('xp');
    }

    public function getLastActivityForUser(User $user)
    {
        return $this->activities()->where('user_id', $user->id)->latest()->first();
    }

    public function getStats()
    {
        $lastActivityForUser = $this->getLastActivityForUser(Auth::user());

        return [
            'cards' => $this->cards()->count(),
            'members' => $this->memberships()->count(),
            'last_activity_at' => $lastActivityForUser?->updated_at ?? null,
            'current_user_xp' => $this->userXP(Auth::user()),
        ];
    }
}
