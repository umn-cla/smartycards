<?php

use App\Http\Controllers\CardAttemptController;
use App\Http\Controllers\CardStatsController;
use App\Http\Controllers\DeckCardController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\DeckMembershipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadFileController;
use App\Http\Controllers\UserLookupController;
use Illuminate\Support\Facades\Route;

// stateful api routes ()
Route::middleware(['auth'])
    ->prefix('api')
    ->group(function () {
        Route::singleton('profile', ProfileController::class);

        Route::resource('decks', DeckController::class);

        Route::resource('decks.memberships', DeckMembershipController::class)
            ->shallow();
        Route::delete('decks/{deck}/memberships/self', [DeckMembershipController::class, 'leave'])
            ->name('decks.memberships.leave');

        Route::get('decks/{deck}/memberships/share/view', [DeckMembershipController::class, 'shareView'])
            ->name('decks.memberships.share.view');

        Route::get('decks/{deck}/memberships/share/edit', [DeckMembershipController::class, 'shareEdit'])
            ->name('decks.memberships.share.edit');

        Route::get('decks/{deck}/memberships/accept', [DeckMembershipController::class, 'acceptInvite'])
            ->name('decks.memberships.acceptInvite')
            ->middleware('signed');

        Route::resource('decks.cards', DeckCardController::class)
            ->shallow();

        Route::resource('cards.attempts', CardAttemptController::class)
            ->shallow();

        Route::get('cards/{card}/stats', [CardStatsController::class, 'show']);

        Route::get('users/lookup', UserLookupController::class);

        Route::post('decks/{deck}/import', [DeckController::class, 'import']);

        Route::post('files', UploadFileController::class);

    });

require __DIR__.'/shib.php';

Route::fallback(function () {
    return view('app');
});
