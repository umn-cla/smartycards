<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'hello',
    ]);
});

// require __DIR__.'/auth.php';
require __DIR__.'/shib.php';
