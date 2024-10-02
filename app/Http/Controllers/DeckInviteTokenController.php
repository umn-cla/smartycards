<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;

class DeckInviteTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function rotate(Request $request, Deck $deck, string $permission)
    {
        // check that permission is valid option
        if (! in_array($permission, ['view', 'edit'])) {
            response()->json(['error' => 'Invalid permission.'], 400);
        }

        // check that the user is authorized to rotate tokens
        Gate::authorize('update', $deck);

        // rotate the token
        $deck->rotateToken($permission);

        return response()->json(['message' => 'Token rotated.']);
    }
}
