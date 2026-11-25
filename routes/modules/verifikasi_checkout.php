
<?php

use App\Http\Controllers\VerifikasiCheckout\VerifikasiCheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Fasilitator'])
    ->prefix('fasilitator')
    ->name('fasilitator.')
    ->group(function () {
        Route::get('/verifikasi-checkout', [VerifikasiCheckoutController::class, 'index'])
            ->name('verifikasi-checkout.index');

        Route::get('/verifikasi-checkout/{id}', [VerifikasiCheckoutController::class, 'show'])
            ->name('verifikasi-checkout.show');

        Route::post('/verifikasi-checkout/{id}/setujui', [VerifikasiCheckoutController::class, 'setujui'])
            ->name('verifikasi-checkout.setujui');

        Route::post('/verifikasi-checkout/{id}/tolak', [VerifikasiCheckoutController::class, 'tolak'])
            ->name('verifikasi-checkout.tolak');
    });
