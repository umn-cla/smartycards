<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Gate;

class DeckReportController extends Controller
{
    public function summary(Deck $deck)
    {
        Gate::authorize('viewReports', [Deck::class, $deck]);

        $cards = $deck->cards()
            ->withGlobalStats()
            ->withAuditInfo()
            ->get()
            ->map(fn ($card) => [
                ...$card->toArray(),
                'created_by' => $card->created_by ? [
                    'id' => $card->created_by->id,
                    'name' => $card->created_by->name,
                ] : null,
                'updated_by' => $card->updated_by ? [
                    'id' => $card->updated_by->id,
                    'name' => $card->updated_by->name,
                ] : null,
            ]);

        return response()->json([
            'cards_count' => $deck->cards()->count(),
            'memberships_count' => $deck->memberships()->count(),
            'cards_with_stats' => $cards,
            'memberships_with_stats' => $deck
                ->memberships()
                ->with('user')
                ->withStats()
                ->get(),
        ]);
    }
}
