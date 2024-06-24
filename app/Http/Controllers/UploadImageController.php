<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;

class UploadImageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Deck $deck)
    {

        Gate::authorize('update', $deck);

        $validated = $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $validated['image']->store("decks/{$deck->id}/images", 'public');

        return response()->json([
            'path' => $path,
        ]);
    }
}
