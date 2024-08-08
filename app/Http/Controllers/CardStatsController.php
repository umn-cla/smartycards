<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardStatsResource;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CardStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // get all user cards and summarize their stats
        $cardsWithStats = Card::withUserStats($request->user())->get();

        return CardStatsResource::collection($cardsWithStats);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Card $card)
    {
        Gate::authorize('view', $card);

        $user = $request->user();

        // Refetch the card with user stats
        $cardWithStats = $card->loadUserStats($user);

        return CardStatsResource::make($cardWithStats);
    }
}
