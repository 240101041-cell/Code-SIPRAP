<?php

namespace App\Services\ValidasiJadwal;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CekKetersediaanAlatService
{
    public const STATUS_MENGUNCI = ['Disetujui', 'Dipinjam'];

    /**
     * Cek apakah jumlah alat yang diminta per jenis masih tersedia pada rentang waktu tertentu.
     *
     * @param array<int, int|string> $permintaan  [id_jenis => jumlah_diminta]
     * @param int|null $idPeminjamanDiabaikan     isi saat memeriksa ulang peminjaman yang sudah tersimpan
     */
    public function cek(
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        array $permintaan,
        ?int $idPeminjamanDiabaikan = null
    ): array {
        $detail = [];

        foreach ($permintaan as $idJenis => $jumlah) {
            $jumlah = filter_var($jumlah, FILTER_VALIDATE_INT);
            if ($jumlah === false || $jumlah <= 0) {
                return $this->gagal(
                    'jumlah_tidak_valid',
                    'Jumlah alat yang diminta harus berupa bilangan bulat lebih dari 0.'
                );
            }

            $jenis = DB::table('jenis_alat')->where('id_jenis', $idJenis)->first();
            if (!$jenis || $jenis->status_jenis !== 'aktif') {
                return $this->gagal('jenis_tidak_valid', 'Jenis alat tidak ditemukan atau tidak aktif.');
            }

            $tersedia = $this
                ->queryUnitTersedia((int) $idJenis, $tanggal, $jamMulai, $jamSelesai, $idPeminjamanDiabaikan)
                ->count();

            $detail[(int) $idJenis] = [
                'nama_jenis' => $jenis->nama_jenis,
                'diminta' => $jumlah,
                'tersedia' => $tersedia,
            ];

            if ($tersedia < $jumlah) {
                return $this->gagal(
                    'stok_tidak_cukup',
                    "Alat {$jenis->nama_jenis} tidak mencukupi: diminta {$jumlah}, tersedia {$tersedia}.",
                    $detail
                );
            }
        }

        return ['valid' => true, 'jenis' => null, 'pesan' => null, 'detail' => $detail];
    }

    /**
     * Daftar id_alat (unit) yang bebas pada rentang waktu itu.
     * Dipakai laboran saat memilih unit di proses persetujuan.
     */
    public function daftarUnitTersedia(
        int $idJenis,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $idPeminjamanDiabaikan = null
    ): array {
        return $this
            ->queryUnitTersedia($idJenis, $tanggal, $jamMulai, $jamSelesai, $idPeminjamanDiabaikan)
            ->orderBy('alat.kode_alat')
            ->pluck('alat.id_alat')
            ->all();
    }

    private function queryUnitTersedia(
        int $idJenis,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $idPeminjamanDiabaikan
    ): Builder {
        // Jam disamakan ke HH:MM:SS agar perbandingan teks tidak salah (misalnya di SQLite).
        $mulai = date('H:i:s', strtotime($jamMulai));
        $selesai = date('H:i:s', strtotime($jamSelesai));

        return DB::table('alat')
            ->where('alat.id_jenis', $idJenis)
            ->where('alat.status_alat', 'aktif')
            ->where('alat.kondisi', 'baik')
            // Unit tidak boleh sedang dialokasikan pada peminjaman aktif yang jamnya tumpang tindih.
            ->whereNotExists(function ($q) use ($tanggal, $mulai, $selesai, $idPeminjamanDiabaikan) {
                $q->select(DB::raw(1))
                    ->from('unit_alat_peminjaman as u')
                    ->join('detail_alat_peminjaman as d', 'd.id_detail', '=', 'u.id_detail')
                    ->join('peminjaman as p', 'p.id_peminjaman', '=', 'd.id_peminjaman')
                    ->whereColumn('u.id_alat', 'alat.id_alat')
                    ->where('p.tanggal_peminjaman', $tanggal)
                    ->whereIn('p.status_peminjaman', self::STATUS_MENGUNCI)
                    ->where('p.jam_mulai', '<', $selesai)
                    ->where('p.jam_selesai', '>', $mulai)
                    ->when($idPeminjamanDiabaikan, fn ($qq) => $qq->where('p.id_peminjaman', '!=', $idPeminjamanDiabaikan));
            })
            // Unit tidak boleh sedang dibekukan pada jam yang tumpang tindih.
            ->whereNotExists(function ($q) use ($tanggal, $mulai, $selesai) {
                $q->select(DB::raw(1))
                    ->from('pembekuan_alat as b')
                    ->whereColumn('b.id_alat', 'alat.id_alat')
                    ->where('b.tanggal_pembekuan', $tanggal)
                    ->where('b.jam_mulai', '<', $selesai)
                    ->where('b.jam_selesai', '>', $mulai);
            });
    }

    private function gagal(string $jenis, string $pesan, array $detail = []): array
    {
        return ['valid' => false, 'jenis' => $jenis, 'pesan' => $pesan, 'detail' => $detail];
    }
}