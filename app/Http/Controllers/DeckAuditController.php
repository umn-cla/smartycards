<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditResource;
use App\Models\Card;
use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Models\Audit;

class DeckAuditController extends Controller
{
    public function index(Request $request, Deck $deck)
    {
        Gate::authorize('viewAuditHistory', [Deck::class, $deck]);

        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'object' => ['sometimes', 'string', Rule::in(['Deck', 'Card'])],
            'id' => ['sometimes', 'integer', 'min:1'],
            'action' => ['sometimes', 'string', Rule::in(['created', 'updated', 'deleted', 'restored'])],
            'user' => ['sometimes', 'string', 'max:255'],
            'from' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:from'],
            'sort' => ['sometimes', 'string', Rule::in(['auditable_type', 'auditable_id', 'event', 'user', 'created_at'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ]);

        // include soft-deleted cards in the audit trail
        $cardIds = $deck->cards()->withTrashed()->pluck('id');

        $query = Audit::query()
            // wrap in `where` to group the OR conditions
            ->where(function ($q) use ($deck, $cardIds) {
                // (auditable_type = Deck::class AND auditable_id = $deck->id)
                $q->where(function ($inner) use ($deck) {
                    $inner->where('auditable_type', Deck::class)
                        ->where('auditable_id', $deck->id);
                })
                    // OR (auditable_type = Card::class AND auditable_id IN ($cardIds))
                    ->orWhere(function ($inner) use ($cardIds) {
                        $inner->where('auditable_type', Card::class)
                            ->whereIn('auditable_id', $cardIds);
                    });
            })
            ->with('user:id,name,email');

        // add filters
        $query
            ->when($validated['object'] ?? false, function ($q) use ($validated) {
                $objectType = $validated['object'] === 'Deck'
                    ? Deck::class
                    : Card::class;
                $q->where('auditable_type', $objectType);
            })
            ->when($validated['id'] ?? false, function ($q) use ($validated) {
                $q->where('auditable_id', $validated['id']);
            })
            ->when($validated['action'] ?? false, function ($q) use ($validated) {
                $q->where('event', $validated['action']);
            })
            ->when($validated['user'] ?? false, function ($q) use ($validated) {
                $q->whereHas('user', function ($subQ) use ($validated) {
                    $subQ->where('name', 'like', '%'.$validated['user'].'%');
                });
            })
            ->when($validated['from'] ?? false, function ($q) use ($validated) {
                $q->whereDate('created_at', '>=', $validated['from']);
            })
            ->when($validated['to'] ?? false, function ($q) use ($validated) {
                $q->whereDate('created_at', '<=', $validated['to']);
            });

        // Sorting
        $sortField = $validated['sort'] ?? 'created_at';
        $sortDirection = $validated['direction'] ?? 'desc';

        if ($sortField === 'user') {
            $query->leftJoin('users', 'audits.user_id', '=', 'users.id')
                ->orderBy('users.name', $sortDirection)
                // only return audit cols, join is just for sorting
                // this avoids ambiguous column errors
                // (e.g. audit.id vs users.id)
                ->select('audits.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        return AuditResource::collection($query->paginate(50));
    }
}
