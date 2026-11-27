<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kerusakan\LaporanKerusakanController;
use App\Http\Controllers\Kerusakan\MonitoringLaporanController;
use App\Http\Controllers\Kerusakan\KinerjaTeknisiController;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/lapor-kerusakan', [LaporanKerusakanController::class, 'index'])->name('kerusakan.index');
    Route::post('/lapor-kerusakan', [LaporanKerusakanController::class, 'store'])->name('kerusakan.store');
    Route::get('/lapor-kerusakan/{laporan}/foto', [LaporanKerusakanController::class, 'photo'])->name('kerusakan.photo');
});

Route::middleware(['auth', 'role:Pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/monitoring-kerusakan', [MonitoringLaporanController::class, 'index'])->name('monitoring-kerusakan.index');
    Route::get('/kinerja-teknisi', [KinerjaTeknisiController::class, 'index'])->name('kinerja-teknisi.index');
});
