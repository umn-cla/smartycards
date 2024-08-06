<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

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
}
