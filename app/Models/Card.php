<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Card extends Model implements AuditableContract
{
    use AuditableTrait, HasFactory;

    protected $casts = [
        'front' => 'array',
        'back' => 'array',
        'avg_score' => 'float',
        'last_attempted_at' => 'datetime',
    ];

    protected $fillable = [
        'front',
        'back',
        'deck_id',
    ];

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    public function attempts()
    {
        return $this->hasMany(CardAttempt::class);
    }

    public function scopeWithGlobalStats(Builder $query)
    {
        return $query
            ->withCount('attempts')
            ->addSelect([
                // using addSelect to rename the column from to 'avg_score'
                'avg_score' => CardAttempt::select(DB::raw('AVG(score)'))
                    ->whereColumn('card_id', 'cards.id'),
            ]);
    }

    public function scopeWithLastAttemptedAt(Builder $query, User $user)
    {
        return $query
            ->withMax(['attempts as last_attempted_at' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }], 'created_at');
    }

    public function scopeWithUserAttemptsCount(Builder $query, User $user)
    {
        return $query
            ->withCount([
                'attempts' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
            ]);
    }

    public function scopeWithUserAvgScore(Builder $query, User $user)
    {
        return $query
            ->withAvg(['attempts as avg_score' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }], 'score');
    }

    public function scopeWithUserStats(Builder $query, User $user)
    {
        return $query
            ->withUserAttemptsCount($user)
            ->withLastAttemptedAt($user)
            ->withUserAvgScore($user);
    }

    /**
     * Load user stats for the card.
     *
     * @return $this
     */
    public function loadUserStats(User $user)
    {
        $this->loadCount(['attempts' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);

        $this->last_attempted_at = $this->attempts()
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->value('created_at');

        $this->avg_score = $this->attempts()
            ->where('user_id', $user->id)
            ->avg('score');

        return $this;
    }
}
