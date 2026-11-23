<?php

use App\Http\Controllers\GO\GODashboardController;
use App\Http\Controllers\GO\PengecekanKamarGOController;
use App\Http\Controllers\GO\LaporanKerusakanGOController;
use App\Http\Controllers\GO\RiwayatPengecekanController;
use App\Http\Controllers\GO\ProfilGOController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:GO|General Officer'])->prefix('go')->name('go.')->group(function () {
    Route::get('/dashboard', [GODashboardController::class, 'index'])->name('dashboard');

    Route::get('/pengecekan-kamar', [PengecekanKamarGOController::class, 'index'])->name('pengecekan-kamar.index');
    Route::get('/pengecekan-kamar/{id}', [PengecekanKamarGOController::class, 'show'])->name('pengecekan-kamar.show');
    Route::post('/pengecekan-kamar/{id}/mulai', [PengecekanKamarGOController::class, 'mulai'])->name('pengecekan-kamar.mulai');
    Route::post('/pengecekan-kamar/{id}/selesai', [PengecekanKamarGOController::class, 'selesai'])->name('pengecekan-kamar.selesai');
    Route::post('/pengecekan-kamar/{id}/draft', [PengecekanKamarGOController::class, 'draft'])->name('pengecekan-kamar.draft');

    Route::get('/laporan-kerusakan', [LaporanKerusakanGOController::class, 'index'])->name('laporan-kerusakan.index');
    Route::get('/laporan-kerusakan/create', [LaporanKerusakanGOController::class, 'create'])->name('laporan-kerusakan.create');
    Route::post('/laporan-kerusakan', [LaporanKerusakanGOController::class, 'store'])->name('laporan-kerusakan.store');

    Route::get('/riwayat-pengecekan', [RiwayatPengecekanController::class, 'index'])->name('riwayat-pengecekan.index');

    Route::get('/profil', [ProfilGOController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilGOController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilGOController::class, 'updatePassword'])->name('profil.password');
});
