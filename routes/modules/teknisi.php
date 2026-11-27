<?php

use App\Http\Controllers\Teknisi\TeknisiKerusakanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/laporan-kerusakan', [TeknisiKerusakanController::class, 'index'])->name('kerusakan.index');
    Route::get('/laporan-kerusakan/{laporan}', [TeknisiKerusakanController::class, 'show'])->name('kerusakan.show');
    Route::get('/laporan-kerusakan/{laporan}/ubah-status', [TeknisiKerusakanController::class, 'editStatus'])->name('kerusakan.edit-status');
    Route::put('/laporan-kerusakan/{laporan}/status', [TeknisiKerusakanController::class, 'updateStatus'])->name('kerusakan.update-status');
    Route::get('/laporan-kerusakan/{laporan}/cetak-pk', [TeknisiKerusakanController::class, 'cetakPk'])->name('kerusakan.cetak-pk');
});
