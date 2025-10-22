<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LtiLaunchController;
use App\Http\Controllers\LtiLoginController;
use App\Http\Controllers\LtiDeepLinkController;
use App\Http\Controllers\LtiErrorController;
use App\Http\Controllers\JwksController;

// LTI login, launch, and services
Route::post('/lti/login', [LtiLoginController::class, 'login'])->name('lti.login');
Route::post('/lti/launch', [LtiLaunchController::class, 'launch'])->name('lti.launch');
Route::get('/lti/deep-link', [LtiDeepLinkController::class, 'index'])->name('lti.deep_link');
Route::post('/lti/deep-link/response', [LtiDeepLinkController::class, 'response'])->name('lti.deep_link.response');
Route::get('/lti/error', [LtiErrorController::class, 'index'])->name('lti.error');

// JWKS endpoint
Route::get('/.well-known/jwks.json', [JwksController::class, 'keys'])->name('lti.keys');
