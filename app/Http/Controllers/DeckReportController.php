<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Gate;

class DeckReportController extends Controller
{
    public function summary(Deck $deck)
    {
        Gate::authorize('viewReports', [Deck::class, $deck]);

        return response()->json([
            'cards_count' => $deck->cards()->count(),
            'memberships_count' => $deck->memberships()->count(),
            'cards_with_stats' => $deck->cards()->withGlobalStats()->get(),
            'memberships_with_stats' => $deck
                ->memberships()
                ->with('user')
                ->withStats()
                ->get(),
        ]);
    }
}
