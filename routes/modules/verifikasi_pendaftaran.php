<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pendaftaran\VerifikasiPendaftaranController;

Route::middleware(['auth', 'role:Admin Layanan'])->group(function () {
    Route::get('/verifikasi-pendaftaran', [VerifikasiPendaftaranController::class, 'index'])->name('verifikasi-pendaftaran.index');
    Route::get('/verifikasi-pendaftaran/{pendaftaran}', [VerifikasiPendaftaranController::class, 'show'])->name('verifikasi-pendaftaran.show');
    Route::get('/verifikasi-pendaftaran/{pendaftaran}/berkas/{berkas}', [VerifikasiPendaftaranController::class, 'document'])->name('verifikasi-pendaftaran.berkas');
    Route::post('/verifikasi-pendaftaran/{pendaftaran}/berkas/{berkas}/jenis', [VerifikasiPendaftaranController::class, 'classifyDocument'])->name('verifikasi-pendaftaran.berkas.jenis');
    Route::post('/verifikasi-pendaftaran/{pendaftaran}/verifikasi', [VerifikasiPendaftaranController::class, 'verify'])->name('verifikasi-pendaftaran.verify');
    Route::get('/verifikasi-pendaftaran/{pendaftaran}/jadwal', [VerifikasiPendaftaranController::class, 'schedule'])->name('verifikasi-pendaftaran.jadwal');
    Route::post('/verifikasi-pendaftaran/{pendaftaran}/jadwal', [VerifikasiPendaftaranController::class, 'saveSchedule'])->name('verifikasi-pendaftaran.jadwal.store');
    Route::get('/verifikasi-pendaftaran/{pendaftaran}/penempatan', [VerifikasiPendaftaranController::class, 'placement'])->name('verifikasi-pendaftaran.penempatan');
    Route::post('/verifikasi-pendaftaran/{pendaftaran}/penempatan', [VerifikasiPendaftaranController::class, 'assign'])->name('verifikasi-pendaftaran.assign');
    Route::post('/verifikasi-pendaftaran/{pendaftaran}/keputusan', [VerifikasiPendaftaranController::class, 'decision'])->name('verifikasi-pendaftaran.decision');
});
