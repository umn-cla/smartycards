<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Relations\BelongsToThrough as RelationsBelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough;

class CardAttempt extends Model
{
    use BelongsToThrough, HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function deck(): RelationsBelongsToThrough
    {
        return $this->belongsToThrough(Deck::class, Card::class);
    }
}
