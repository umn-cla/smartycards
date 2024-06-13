<?php

use App\Http\Controllers\DeckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'hello',
    ]);
});

// stateful api routes ()
Route::middleware(['auth'])
    ->prefix('api')
    ->group(function () {
        Route::get('profile', function (Request $request) {
            return response()->json(
                $request->user()
            );
        });

        Route::resource('decks', DeckController::class);

    });

require __DIR__.'/shib.php';
