<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;

class ActivityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ActivityType::all();
    }
}
