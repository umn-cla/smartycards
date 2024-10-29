<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'default_xp',
    ];

    protected static function boot()
    {
        parent::boot();

        // validate that the slug is a valid ActivityTypeEnum
        static::saving(function ($model) {
            if (! ActivityTypeEnum::tryFrom($model->name)) {
                throw new \InvalidArgumentException('Invalid activity name for ActivityTypeEnum');
            }
        });
    }

    public function activityEvents()
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function getXPToAward(int $correctCount, int $totalCount): int
    {
        // $typeNameEnum = ActivityTypeEnum::tryFrom($this->name);

        // ignoring correctCount for now
        // instead award XP based on total count
        // or proportional if less than a certain threshold
        // $minForFullXP = match ($typeNameEnum) {
        //     ActivityTypeEnum::PRACTICE_ALL_CARDS => 20,
        //     ActivityTypeEnum::QUIZ => 10,
        //     ActivityTypeEnum::MATCHING => 8,
        //     default => 1,
        // };

        // $proportionOfXP = min(1.0, $totalCount / $minForFullXP);

        // return (int) round($this->default_xp * $proportionOfXP);

        // for now just return the default XP regardless of deck size
        return $this->default_xp;
    }
}
