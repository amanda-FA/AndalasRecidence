<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pendaftaran\PembayaranController;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/tagihan', [PembayaranController::class, 'terbitkan'])->name('pembayaran.terbitkan');
    Route::post('/pembayaran/simulasi', [PembayaranController::class, 'simulasi'])->name('pembayaran.simulasi');
});
