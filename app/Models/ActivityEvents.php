<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityEvents extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_type_id',
        'user_id',
        'deck_id',
        'xp_earned',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }
}
