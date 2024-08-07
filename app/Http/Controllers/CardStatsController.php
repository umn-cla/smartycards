<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class CardStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // get all user cards and summarize their stats
        $cards = Card::withUserStats($request->user())->get();

        return response()->json($cards);
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
    }
}
