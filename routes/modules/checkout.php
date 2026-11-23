<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Checkout\PengajuanCheckoutController;

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [PengajuanCheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/client/checkout', [PengajuanCheckoutController::class, 'index'])->name('client.checkout');
    Route::post('/checkout', [PengajuanCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/surat-bebas/download', [PengajuanCheckoutController::class, 'downloadSurat'])->name('checkout.surat-bebas.download');
    Route::get('/checkout/surat-bebas/preview', [PengajuanCheckoutController::class, 'previewSurat'])->name('checkout.surat-bebas.preview');
});
