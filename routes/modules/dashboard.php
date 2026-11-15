<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Pimpinan'])->group(function () {
    Route::get('/dashboard/okupansi', [DashboardController::class, 'index'])
        ->name('dashboard.okupansi');
});
