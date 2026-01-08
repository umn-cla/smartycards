<?php

namespace App\Http\Controllers;

use App\Http\Resources\LtiResourceLinkEntryResource;
use App\Models\Deck;
use App\Services\Lti\LtiService;
use Gate;
use Illuminate\Support\Facades\Auth;

class DeckScoreController extends Controller
{
    public function index(Deck $deck, LtiService $ltiService)
    {
        Gate::authorize('view', $deck);

        $entries = $ltiService->getEntriesForUserAndDeck(Auth::user()->id, $deck->id);

        return LtiResourceLinkEntryResource::collection($entries);
    }
}
