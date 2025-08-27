<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LTI13ResourceLink extends Model
{

    
    protected $table = 'lti13_resource_links';
    protected $casts = [
        'endpoint' => 'array'
    ];

    public function deployment() {
        return $this->belongsTo(LTI13Deployment::class);
    }



}
