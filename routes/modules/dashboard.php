<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Pimpinan'])->group(function () {
    Route::get('/dashboard/okupansi', [DashboardController::class, 'index'])
        ->name('dashboard.okupansi');
    Route::get('/dashboard/okupansi/export/pdf', [DashboardController::class, 'exportPdf'])
        ->name('dashboard.okupansi.pdf');
    Route::get('/dashboard/okupansi/export/csv', [DashboardController::class, 'exportCsv'])
        ->name('dashboard.okupansi.csv');
});
