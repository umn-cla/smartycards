<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Support\Facades\Gate;
use OwenIt\Auditing\Models\Audit;

class DeckAuditController extends Controller
{
    public function index(Deck $deck)
    {
        Gate::authorize('viewAuditHistory', [Deck::class, $deck]);

        $cardIds = $deck->cards()->withTrashed()->pluck('id');

        $audits = Audit::query()
            ->where(function ($query) use ($deck, $cardIds) {
                $query->where(function ($q) use ($deck) {
                    $q->where('auditable_type', Deck::class)
                        ->where('auditable_id', $deck->id);
                })
                ->orWhere(function ($q) use ($cardIds) {
                    $q->where('auditable_type', Card::class)
                        ->whereIn('auditable_id', $cardIds);
                });
            })
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json([
            'data' => $audits->map(fn (Audit $audit) => [
                'id' => $audit->id,
                'event' => $audit->event,
                'auditable_type' => class_basename($audit->auditable_type),
                'auditable_id' => $audit->auditable_id,
                'old_values' => $audit->old_values,
                'new_values' => $audit->new_values,
                'user' => $audit->user ? [
                    'id' => $audit->user->id,
                    'name' => $audit->user->name,
                    'email' => $audit->user->email,
                ] : null,
                'created_at' => $audit->created_at,
            ]),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
            'links' => [
                'prev' => $audits->previousPageUrl(),
                'next' => $audits->nextPageUrl(),
            ],
        ]);
    }
}
