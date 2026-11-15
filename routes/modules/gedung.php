<?php

use App\Http\Controllers\Gedung\GedungController;
use App\Http\Controllers\Gedung\KamarController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Fasilitator'])->group(function () {
    Route::resource('gedung', GedungController::class)->except(['show']);
    Route::resource('kamar', KamarController::class)->except(['show']);
});
