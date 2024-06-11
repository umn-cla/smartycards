<?php

use StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController;

if (config('shibboleth.emulate_idp')) {
    Route::get('login',
        [ShibbolethController::class, 'emulateLogin'])->name('login');
    Route::group(['middleware' => 'web'], function () {
        Route::get('emulated/idp',
            [ShibbolethController::class, 'emulateIdp']);
        Route::post('emulated/idp',
            [ShibbolethController::class, 'emulateIdp']);
        Route::get('emulated/login',
            [ShibbolethController::class, 'emulateLogin']);
        Route::get('emulated/logout',
            [ShibbolethController::class, 'emulateLogout']);
    });
} else {
    Route::get('login',
        [ShibbolethController::class, 'login'])->name('login');
    Route::group(['middleware' => 'web'], function () {
        Route::get('/shibboleth-login', [
            ShibbolethController::class, 'login'])->name('shibboleth-login');
        Route::get('/shibboleth-authenticate',
            [ShibbolethController::class, 'idpAuthenticate'])->name('shibboleth-authenticate');
        Route::get('/shibboleth-logout',
            [ShibbolethController::class, 'destroy'])->name('shibboleth-logout');
    });
}
