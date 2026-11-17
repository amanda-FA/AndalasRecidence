<?php

use App\Http\Controllers\Periode\PeriodePendaftaranController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Admin Layanan'])->group(function () {
    Route::get('/periode-pendaftaran', [
        PeriodePendaftaranController::class, 'index',
    ])->name('periode.index');

    Route::post('/periode-pendaftaran', [
        PeriodePendaftaranController::class, 'store',
    ])->name('periode.store');

    Route::patch('/periode-pendaftaran/{periode}/status', [
        PeriodePendaftaranController::class, 'updateStatus',
    ])->name('periode.status');

    Route::delete('/periode-pendaftaran/{periode}', [
    PeriodePendaftaranController::class, 'destroy',
    ])->name('periode.destroy');

});