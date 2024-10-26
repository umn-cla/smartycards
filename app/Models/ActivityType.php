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
}
