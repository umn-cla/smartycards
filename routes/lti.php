<?php

use App\Http\Controllers\LtiController;
use Illuminate\Support\Facades\Route;

// Step 1: Canvas initiates Login
Route::get('/lti/login', [LtiController::class, 'login'])->name('lti.login');
Route::post('/lti/login', [LtiController::class, 'login']); // Some LMS use POST
