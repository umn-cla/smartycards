<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class DeckInviteToken extends Model
{
    use HasFactory;

    protected $table = 'deck_invite_tokens';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'deck_id',
        'permission',
        'token',
    ];

    /**
     * Get the deck that owns the token.
     */
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    public function rotate()
    {
        $this->token = Str::random(32);
        $this->save();
    }
}
