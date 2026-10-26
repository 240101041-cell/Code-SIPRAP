<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\PeminjamController;

Route::prefix('peminjam')->group(function () {
    Route::get('/pengajuan', [PeminjamController::class, 'pengajuan']);
    Route::get('/riwayat', [PeminjamController::class, 'riwayat']);
});