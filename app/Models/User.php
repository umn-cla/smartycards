<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements AuditableContract
{
    use AuditableTrait, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'umndid',
        'emplid',
        'first_name',
        'last_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function decks()
    {
        return $this->belongsToMany(Deck::class, 'deck_memberships')->withPivot('role');
    }

    public function cards()
    {
        return $this->hasManyThrough(Card::class, DeckMembership::class, 'user_id', 'deck_id', 'id', 'deck_id');
    }

    public function cardAttempts()
    {
        return $this->hasMany(CardAttempt::class);
    }

    public function memberships()
    {
        return $this->hasMany(DeckMembership::class);
    }

    public function isOwnerOfDeck(Deck $deck): bool
    {
        return $this->hasRoleInDeck($deck, 'owner');
    }

    public function isMemberOfDeck(Deck $deck): bool
    {
        return $deck->memberships()
            ->where('user_id', $this->id)
            ->exists();
    }

    public function hasRoleInDeck(Deck $deck, string|array $roleArray): bool
    {
        if (is_string($roleArray)) {
            $roleArray = [$roleArray];
        }

        return $deck->memberships()
            ->where('user_id', $this->id)
            ->whereIn('role', $roleArray)
            ->exists();
    }
}
