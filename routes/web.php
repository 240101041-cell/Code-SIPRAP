<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\LaboranController;
use App\Http\Controllers\AdminController;

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
    Route::post('/pengajuan/simpan', [PeminjamController::class, 'simpanPengajuan']);
    Route::get('/riwayat', [PeminjamController::class, 'riwayat']);
    Route::get('/detail/{id?}', [PeminjamController::class, 'detail']);
    Route::get('/notifikasi', [PeminjamController::class, 'notifikasi']);
    Route::get('/profil', [PeminjamController::class, 'profil']);
    Route::get('/cek-slot', [PeminjamController::class, 'cekSlotWaktu']);
});

// Rute Laboran / Teknisi (F4 - F7)
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
    Route::post('/peralatan/{id}/update', [LaboranController::class, 'updatePeralatan']);
    Route::post('/jadwal/tambah', [LaboranController::class, 'tambahJadwalReguler']);
});

// Rute Kepala / Admin (F8 - F13)
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/pengguna', [AdminController::class, 'pengguna']);
    Route::post('/pengguna/tambah', [AdminController::class, 'tambahPengguna']);
    Route::post('/pengguna/{id}/update', [AdminController::class, 'updatePengguna']);
    Route::post('/pengguna/{id}/status', [AdminController::class, 'updateStatusPengguna']);
    Route::post('/pengguna/{id}/hapus', [AdminController::class, 'hapusPengguna']);
    Route::get('/ruangan', [AdminController::class, 'ruangan']);
    Route::post('/ruangan/tambah', [AdminController::class, 'tambahRuangan']);
    Route::post('/ruangan/{id}/update', [AdminController::class, 'updateRuangan']);  // <-- Ditambahkan
    Route::post('/ruangan/{id}/hapus', [AdminController::class, 'hapusRuangan']);    // <-- Ditambahkan
    Route::get('/laporan', [AdminController::class, 'laporan']);
    Route::post('/sanksi/{id}/pulihkan', [AdminController::class, 'pulihkanSanksi']);
    Route::get('/profil', [AdminController::class, 'profil']);
    Route::get('/laporan', [AdminController::class, 'laporan']);
    Route::get('/laporan/ekspor-excel', [AdminController::class, 'eksporExcel']); // <-- Tambahkan ini
});