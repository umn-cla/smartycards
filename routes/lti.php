<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LtiController;
use App\Http\Controllers\JwksController;

// LTI OIDC login and launch
Route::post('/lti/login', [LtiController::class, 'login'])->name('lti.login');
Route::post('/lti/launch', [LtiController::class, 'launch'])->name('lti.launch');

// Deep linking (instructor selects content)
Route::get('/lti/deep-link', [LtiController::class, 'deepLink'])->name('lti.deep_link');
Route::post('/lti/deep-link/response', [LtiController::class, 'deepLinkResponse'])->name('lti.deep_link.response');

// Resource launch (student accesses assignment)
Route::get('/lti/resource', [LtiController::class, 'resource'])->name('lti.resource');

// Submission review (instructor reviews student work)
Route::get('/lti/submission-review', [LtiController::class, 'submissionReview'])->name('lti.submission_review');

// Error handling
Route::get('/lti/error', [LtiController::class, 'error'])->name('lti.error');

// JWKS endpoint for LTI authentication
Route::get('/.well-known/jwks.json', [JwksController::class, 'keys'])->name('lti.keys');
