<?php

use Illuminate\Support\Facades\Route;
use Webkul\Iyzico\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web']], function () {

    /**
     * Iyzico payment routes
     */

    Route::get('/iyzico-redirect', [PaymentController::class, 'redirect'])->name('iyzico.redirect');

    Route::get('/iyzico-success', [PaymentController::class, 'success'])->name('iyzico.success');

    Route::get('/iyzico-cancel', [PaymentController::class, 'failure'])->name('iyzico.cancel');

    Route::post('/iyzico-callback', [PaymentController::class, 'callback'])->name('iyzico.callback');
});