<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityEvent extends Model
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

    public static function awardXP(User $user, Deck $deck, ActivityTypeEnum $activityType, $xp = null): ActivityEvent
    {
        $activityType = ActivityType::where('name', $activityType->value)->first();
        $xp = $xp ?? $activityType->default_xp;

        $event = ActivityEvent::create([
            'activity_type_id' => $activityType->id,
            'user_id' => $user->id,
            'deck_id' => $deck->id,
            'xp_earned' => $xp,
        ]);

        return $event;
    }
}
