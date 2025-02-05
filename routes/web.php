<?php

use App\Http\Controllers\ActivityEventController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\CardAttemptController;
use App\Http\Controllers\CardStatsController;
use App\Http\Controllers\DeckCardController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\DeckInviteController;
use App\Http\Controllers\DeckMembershipController;
use App\Http\Controllers\DeckQuizController;
use App\Http\Controllers\DeckReportController;
use App\Http\Controllers\FeatureFlagController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TTSController;
use App\Http\Controllers\UploadFileController;
use Illuminate\Support\Facades\Route;

// homepage is public
Route::get('/', function () {
    return view('app');
});

// stateful api routes ()
Route::middleware(['auth'])
    ->prefix('api')
    ->group(function () {
        Route::singleton('profile', ProfileController::class);
        Route::get('features', [FeatureFlagController::class, 'index']);

        Route::resource('decks', DeckController::class);

        Route::resource('decks.memberships', DeckMembershipController::class)
            ->shallow();
        Route::delete('decks/{deck}/memberships/self', [DeckMembershipController::class, 'leave'])
            ->name('decks.memberships.leave');

        Route::post('decks/{deck}/memberships/share', [DeckMembershipController::class, 'shareLink'])
            ->name('decks.memberships.share');

        Route::post('decks/{deck}/memberships/share/regenerate', [DeckMembershipController::class, 'regenerateShareLink'])
            ->name('decks.memberships.share.regenerate');

        Route::get('community/decks', [DeckController::class, 'publicDecks']);

        Route::post('community/decks/{deck}/join', [DeckMembershipController::class, 'joinAsViewer']);

        Route::resource('decks.cards', DeckCardController::class)
            ->shallow();

        Route::post('decks/{deck}/quiz', [DeckQuizController::class, 'quiz']);

        Route::get('decks/{deck}/stats', [
            DeckController::class,
            'stats',
        ]);

        Route::get('decks/{deck}/reports/summary', [DeckReportController::class, 'summary']);

        Route::resource('decks.activity-events', ActivityEventController::class);

        Route::get('activity-types', [ActivityTypeController::class, 'index']);

        Route::resource('cards.attempts', CardAttemptController::class)
            ->shallow();

        Route::get('cards/{card}/stats', [CardStatsController::class, 'show']);

        Route::post('decks/{deck}/import', [DeckController::class, 'import']);

        Route::post('files', UploadFileController::class);

        Route::post('tts', TTSController::class);
    });

require __DIR__.'/shib.php';

Route::get('decks/{deck}/invite', DeckInviteController::class)
    ->name('decks.memberships.acceptInvite')
    ->middleware(['auth', 'signed']);

// guard everything else with auth
Route::fallback(function () {
    return view('app');
})->middleware(['auth'])->name('app');
