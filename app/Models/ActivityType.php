<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_xp',
    ];

    public function activityEvents()
    {
        return $this->hasMany(ActivityEvents::class);
    }
}
