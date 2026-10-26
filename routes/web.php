<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeminjamController;

// Rute Login & Logout
Route::get('/', [PeminjamController::class, 'login'])->name('login');
Route::get('/login', [PeminjamController::class, 'login']);
Route::post('/login', [PeminjamController::class, 'loginPost'])->name('login.post');
Route::get('/logout', [PeminjamController::class, 'logout'])->name('logout');

// Rute Peminjam (Mahasiswa / Dosen)
Route::prefix('peminjam')->group(function () {
    Route::get('/dashboard', [PeminjamController::class, 'dashboard']);
    Route::get('/ketersediaan', [PeminjamController::class, 'ketersediaan']);
    Route::get('/pengajuan', [PeminjamController::class, 'pengajuan']);
    Route::get('/riwayat', [PeminjamController::class, 'riwayat']);
    Route::get('/detail/{id?}', [PeminjamController::class, 'detail']);
    Route::get('/notifikasi', [PeminjamController::class, 'notifikasi']);
    Route::get('/profil', [PeminjamController::class, 'profil']);
    Route::post('/pengajuan/simpan', [PeminjamController::class, 'simpanPengajuan']);
});

use App\Http\Controllers\LaboranController;

// Rute Laboran / Teknisi (Lengkap F4 - F7)
Route::prefix('laboran')->group(function () {
    Route::get('/dashboard', [LaboranController::class, 'dashboard']);
    Route::get('/verifikasi', [LaboranController::class, 'verifikasi']);
    Route::get('/verifikasi/{id}', [LaboranController::class, 'detailVerifikasi']);
    Route::post('/verifikasi/{id}/proses', [LaboranController::class, 'prosesVerifikasi']);
    Route::get('/peminjaman', [LaboranController::class, 'peminjaman']);
    Route::post('/peminjaman/{id}/serah-terima', [LaboranController::class, 'prosesSerahTerima']);
    Route::get('/pengembalian', [LaboranController::class, 'pengembalian']);
    Route::post('/pengembalian/{id}/selesai', [LaboranController::class, 'prosesPengembalian']);
    Route::get('/peralatan', [LaboranController::class, 'peralatan']);
    Route::post('/peralatan/tambah', [LaboranController::class, 'tambahPeralatan']);
    Route::post('/peralatan/{kode}/update', [LaboranController::class, 'updatePeralatan']);
    Route::get('/jadwal', [LaboranController::class, 'jadwal']);
    Route::get('/profil', [LaboranController::class, 'profil']);
});

use App\Http\Controllers\AdminController;

// Rute Kepala / Admin (F8 - F9)
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/pengguna', [AdminController::class, 'pengguna']);
    Route::post('/pengguna/tambah', [AdminController::class, 'tambahPengguna']);
    Route::post('/pengguna/{id}/status', [AdminController::class, 'updateStatusPengguna']);
    Route::get('/ruangan', [AdminController::class, 'ruangan']);
    Route::get('/laporan', [AdminController::class, 'laporan']);
    Route::get('/profil', [AdminController::class, 'profil']);
});