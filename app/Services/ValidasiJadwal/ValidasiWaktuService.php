<?php

namespace App\Services\ValidasiJadwal;

class ValidasiWaktuService
{
    public function validasi(string $tanggal, string $jamMulai, string $jamSelesai): array
    {
        $d = \DateTime::createFromFormat('Y-m-d', $tanggal);
        if (!$d || $d->format('Y-m-d') !== $tanggal) {
            return $this->gagal('tanggal_tidak_valid', 'Format tanggal harus YYYY-MM-DD.');
        }

        $mulai = $this->normalisasi($jamMulai);
        $selesai = $this->normalisasi($jamSelesai);
        if ($mulai === null || $selesai === null) {
            return $this->gagal('jam_tidak_valid', 'Format jam harus HH:MM.');
        }

        if ($selesai <= $mulai) {
            return $this->gagal(
                'rentang_jam_tidak_valid',
                'Jam selesai harus lebih besar dari jam mulai. Peminjaman tidak boleh melewati tengah malam.'
            );
        }

        return ['valid' => true, 'jenis' => null, 'pesan' => null];
    }

    // Interval setengah terbuka [mulai, selesai). Panggil setelah validasi() lolos.
    public function bertumpangTindih(string $mulaiLama, string $selesaiLama, string $mulaiBaru, string $selesaiBaru): bool
    {
        return $this->normalisasi($mulaiLama) < $this->normalisasi($selesaiBaru)
            && $this->normalisasi($selesaiLama) > $this->normalisasi($mulaiBaru);
    }

    private function normalisasi(string $jam): ?string
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)(?::([0-5]\d))?$/', $jam, $m)) {
            return null;
        }
        return sprintf('%s:%s:%s', $m[1], $m[2], $m[3] ?? '00');
    }

    private function gagal(string $jenis, string $pesan): array
    {
        return ['valid' => false, 'jenis' => $jenis, 'pesan' => $pesan];
    }
}