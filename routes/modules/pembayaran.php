<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::view('/pembayaran', 'pembayaran.index')
        ->name('pembayaran.index');
});