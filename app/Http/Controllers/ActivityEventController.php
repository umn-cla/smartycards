<?php

namespace App\Http\Controllers;

use App\Enums\ActivityTypeEnum;
use App\Models\ActivityEvent;
use App\Models\ActivityType;
use App\Models\Deck;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class ActivityEventController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Deck $deck)
    {
        Gate::authorize('create', [ActivityEvent::class, $deck]);

        // TODO: maybe we should rate limit or check some
        // sort of token to avoid potential spamming
        $validated = $request->validate([
            'activity_type_name' => [
                'required',
                new Enum(ActivityTypeEnum::class),
            ],
            'correct_count' => ['integer', 'nullable'],
            'total_count' => ['integer', 'nullable'],
        ]);

        $activityType = ActivityType::where('name', $validated['activity_type_name'])->first();

        $xp = $activityType->getXPToAward(
            correctCount: $validated['correct_count'],
            totalCount: $validated['total_count']
        );

        $event = ActivityEvent::create([
            'deck_id' => $deck->id,
            'activity_type_id' => $activityType->id,
            'user_id' => Auth::id(),
            'xp' => $xp,
        ]);

        return response()->json($event, 201);
    }
}
