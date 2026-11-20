<?php

use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/hunian', [LandingPageController::class, 'catalog'])->name('hunian.index');
Route::get('/hunian/{gedung}', [LandingPageController::class, 'show'])->name('hunian.show');
