<?php

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
            // dd($request->user());

            return response()->json(
                $request->user()
            );
        });

    });

require __DIR__.'/shib.php';
