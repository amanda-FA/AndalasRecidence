<?php

use App\Http\Controllers\Pendaftaran\PendaftaranHunianController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/pendaftaran-hunian', [
        PendaftaranHunianController::class,
        'create',
    ])->name('pendaftaran.create');

    Route::post('/pendaftaran-hunian', [
        PendaftaranHunianController::class,
        'store',
    ])->name('pendaftaran.store');
});