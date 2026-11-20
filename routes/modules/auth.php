
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Hanya untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});

// Hanya untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::get('/portal', [AuthController::class, 'home'])->name('portal');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
