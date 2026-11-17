<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/dashboard.php';
require __DIR__.'/modules/gedung.php';
require __DIR__.'/modules/periode.php';
require __DIR__.'/modules/pendaftaran.php';
require __DIR__.'/modules/pembayaran.php';