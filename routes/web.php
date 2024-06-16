<?php

use App\Http\Controllers\CardAttemptController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\DeckMembershipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserLookupController;
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
        Route::singleton('profile', ProfileController::class);

        Route::resource('decks', DeckController::class);

        Route::resource('decks.memberships', DeckMembershipController::class)->shallow();

        Route::resource('cards', CardController::class)->shallow();

        Route::resource('cards.attempts', CardAttemptController::class)->shallow();

        Route::get('users/lookup', UserLookupController::class);
    });

require __DIR__.'/shib.php';
