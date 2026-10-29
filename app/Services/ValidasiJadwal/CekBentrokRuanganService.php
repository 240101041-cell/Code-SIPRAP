<?php

namespace App\Services\ValidasiJadwal;

use Illuminate\Support\Facades\DB;

class CekBentrokRuanganService
{
    public const STATUS_MENGUNCI = ['Disetujui', 'Dipinjam'];

    // Panggil setelah ValidasiWaktuService::validasi() lolos.
    public function cek(
        int $idRuangan,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $idPeminjamanDiabaikan = null
    ): array {
        $mulai = date('H:i:s', strtotime($jamMulai));
        $selesai = date('H:i:s', strtotime($jamSelesai));

        $peminjaman = DB::table('peminjaman')
            ->where('id_ruangan', $idRuangan)
            ->where('tanggal_peminjaman', $tanggal)
            ->whereIn('status_peminjaman', self::STATUS_MENGUNCI)
            ->where('jam_mulai', '<', $selesai)
            ->where('jam_selesai', '>', $mulai)
            ->when($idPeminjamanDiabaikan, fn ($q) => $q->where('id_peminjaman', '!=', $idPeminjamanDiabaikan))
            ->first();

        if ($peminjaman) {
            return $this->gagal(
                'bentrok_peminjaman',
                'Ruangan sudah dipinjam pada ' . substr($peminjaman->jam_mulai, 0, 5) . '-' . substr($peminjaman->jam_selesai, 0, 5) . '.'
            );
        }

        $penutupan = DB::table('penutupan_ruangan')
            ->where('id_ruangan', $idRuangan)
            ->where('tanggal_penutupan', $tanggal)
            ->where('jam_mulai', '<', $selesai)
            ->where('jam_selesai', '>', $mulai)
            ->first();

        if ($penutupan) {
            return $this->gagal(
                'bentrok_penutupan',
                'Ruangan ditutup pada ' . substr($penutupan->jam_mulai, 0, 5) . '-' . substr($penutupan->jam_selesai, 0, 5) . '.'
            );
        }

        return ['valid' => true, 'jenis' => null, 'pesan' => null];
    }

    private function gagal(string $jenis, string $pesan): array
    {
        return ['valid' => false, 'jenis' => $jenis, 'pesan' => $pesan];
    }
}