<?php

namespace App\Models;

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

    public function scopeWithLastAttemptedAt($query, $userId)
    {
        return $query
            ->withMax(['attempts as last_attempted_at' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }], 'created_at');
    }

    public function scopeWithUserAttemptsCount($query, $userId)
    {
        return $query
            ->withCount([
                'attempts' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
            ]);
    }

    public function scopeWithUserAvgScore($query, $userId)
    {
        return $query
            ->withAvg(['attempts as avg_score' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }], 'score');
    }

    public function scopeWithUserDetails($query, $userId)
    {
        return $query
            ->withUserAttemptsCount($userId)
            ->withLastAttemptedAt($userId)
            ->withUserAvgScore($userId);
    }
}
