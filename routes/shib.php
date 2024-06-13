<?php

use StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController;

if (config('shibboleth.emulate_idp')) {
    Route::get('login',
        [ShibbolethController::class, 'emulateLogin'])->name('login');
    Route::group(['middleware' => 'web'], function () {
        Route::get('logout',
            [ShibbolethController::class, 'emulateLogout'])->name('logout');
        Route::get('emulated/idp',
            [ShibbolethController::class, 'emulateIdp']);
        Route::post('emulated/idp',
            [ShibbolethController::class, 'emulateIdp']);
    });
} else {
    Route::get('login',
        [ShibbolethController::class, 'login'])->name('login');

    Route::group(['middleware' => 'web'], function () {
        Route::get('/logout',
            [ShibbolethController::class, 'destroy'])->name('logout');
        Route::get('/shibboleth-authenticate',
            [ShibbolethController::class, 'idpAuthenticate'])->name('shibboleth-authenticate');
        Route::get('/shibboleth-logout',
            [ShibbolethController::class, 'destroy'])->name('shibboleth-logout');
    });
}
