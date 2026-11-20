<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Penempatan\PenempatanController;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/penempatan-kamar', [PenempatanController::class, 'index'])
        ->name('penempatan.index');
});
