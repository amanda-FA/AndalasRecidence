<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kerusakan\LaporanKerusakanController;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/lapor-kerusakan', [LaporanKerusakanController::class, 'index'])->name('kerusakan.index');
    Route::post('/lapor-kerusakan', [LaporanKerusakanController::class, 'store'])->name('kerusakan.store');
    Route::get('/lapor-kerusakan/{laporan}/foto', [LaporanKerusakanController::class, 'photo'])->name('kerusakan.photo');
});
