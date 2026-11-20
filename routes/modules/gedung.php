<?php

use App\Http\Controllers\Gedung\GedungController;
use App\Http\Controllers\Gedung\KamarController;
use App\Http\Controllers\Gedung\PilihanFasilitasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Fasilitator'])->group(function () {
    Route::post('/pilihan-fasilitas', [PilihanFasilitasController::class, 'store'])->name('pilihan-fasilitas.store');
    Route::resource('gedung', GedungController::class)->except(['show']);
    Route::resource('kamar', KamarController::class)->except(['show']);
});
