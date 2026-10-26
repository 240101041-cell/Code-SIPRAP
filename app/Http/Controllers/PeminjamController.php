<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeminjamController extends Controller
{
    // Halaman Form Pengajuan (F3)
    public function pengajuan()
    {
        // Data dummy sementara agar form bisa tampil & dites
        $ruangan = [
            (object)['id_ruangan' => 1, 'nama_ruangan' => 'Lab Komputer 1', 'kapasitas' => 40],
            (object)['id_ruangan' => 2, 'nama_ruangan' => 'Lab Jaringan & Sistem', 'kapasitas' => 35],
            (object)['id_ruangan' => 3, 'nama_ruangan' => 'Ruang Kelas Teori 204', 'kapasitas' => 50],
        ];

        $alat = [
            (object)['id_alat' => 1, 'nama_alat' => 'Proyektor Epson', 'stok_total' => 5, 'stok_rusak' => 1],
            (object)['id_alat' => 2, 'nama_alat' => 'Kabel LAN Tester', 'stok_total' => 10, 'stok_rusak' => 0],
            (object)['id_alat' => 3, 'nama_alat' => 'Crimping Tool Set', 'stok_total' => 12, 'stok_rusak' => 2],
        ];

        return view('peminjam.pengajuan', compact('ruangan', 'alat'));
    }

    // Halaman Riwayat Pengajuan (F4)
    public function riwayat()
    {
        $riwayat = [
            (object)[
                'id_peminjaman' => 'PMJ-001',
                'tanggal_peminjaman' => '2026-10-28',
                'ruangan' => 'Lab Komputer 1',
                'keperluan' => 'Praktikum Pengganti Jarkom',
                'status_peminjaman' => 'Menunggu Persetujuan'
            ],
            (object)[
                'id_peminjaman' => 'PMJ-002',
                'tanggal_peminjaman' => '2026-10-25',
                'ruangan' => 'Lab Jaringan',
                'keperluan' => 'Workshop HMP',
                'status_peminjaman' => 'Disetujui'
            ]
        ];

        return view('peminjam.riwayat', compact('riwayat'));
    }
}