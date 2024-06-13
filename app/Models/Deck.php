<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }
}
