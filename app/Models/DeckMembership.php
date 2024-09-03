<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DeckMembership extends Model implements AuditableContract
{
    use AuditableTrait, HasFactory;

    const ROLE_OWNER = 'owner';

    const ROLE_EDITOR = 'editor';

    const ROLE_VIEWER = 'viewer';

    protected $fillable = [
        'deck_id',
        'user_id',
        'role',
    ];

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
