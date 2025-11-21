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
        'lti_resource_link_id',
        'xp',
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

    public function ltiResourceLink()
    {
        return $this->belongsTo(LtiResourceLink::class);
    }

    public function gradeSubmissions()
    {
        return $this->hasMany(LtiGradeSubmission::class);
    }

    /**
     * Check if this activity was launched from an LTI context
     */
    public function isLtiContext(): bool
    {
        return $this->lti_resource_link_id !== null;
    }

    public static function awardXP(int $userId, int $deckId, ActivityTypeEnum $activityType, ?int $xp = null): ActivityEvent
    {
        $activityType = ActivityType::where('name', $activityType->value)->first();
        $xp = $xp ?? $activityType->default_xp;

        $event = ActivityEvent::create([
            'activity_type_id' => $activityType->id,
            'user_id' => $userId,
            'deck_id' => $deckId,
            'xp' => $xp,
        ]);

        return $event;
    }
}
