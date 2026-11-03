<?php

namespace App\Services\ValidasiJadwal;

use Illuminate\Support\Facades\DB;

class ValidasiJadwalPeminjamanService
{
    private ValidasiWaktuService $waktu;
    private CekBentrokRuanganService $ruangan;
    private CekKetersediaanAlatService $alat;

    public function __construct(
        ?ValidasiWaktuService $waktu = null,
        ?CekBentrokRuanganService $ruangan = null,
        ?CekKetersediaanAlatService $alat = null
    ) {
        $this->waktu = $waktu ?? new ValidasiWaktuService();
        $this->ruangan = $ruangan ?? new CekBentrokRuanganService();
        $this->alat = $alat ?? new CekKetersediaanAlatService();
    }

    /**
     * Pintu tunggal validasi jadwal peminjaman.
     *
     * @param array<int, int|string> $alat  [id_jenis => jumlah_diminta]; kosong jika hanya meminjam ruangan
     * @param int|null $idPeminjaman        isi saat memeriksa ulang peminjaman yang sudah tersimpan
     *
     * @return array{valid: bool, jenis: ?string, pesan: ?string, tahap: ?string, detail: array}
     *         tahap berisi "waktu", "ruangan", atau "alat" saat gagal.
     */
    public function validasi(
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        int $idRuangan,
        array $alat = [],
        ?int $idPeminjaman = null
    ): array {
        $hasilWaktu = $this->waktu->validasi($tanggal, $jamMulai, $jamSelesai);
        if (!$hasilWaktu['valid']) {
            return $this->gagal('waktu', $hasilWaktu);
        }

        $hasilRuangan = $this->ruangan->cek($idRuangan, $tanggal, $jamMulai, $jamSelesai, $idPeminjaman);
        if (!$hasilRuangan['valid']) {
            return $this->gagal('ruangan', $hasilRuangan);
        }

        $detailAlat = [];
        if ($alat !== []) {
            $hasilAlat = $this->alat->cek($tanggal, $jamMulai, $jamSelesai, $alat, $idPeminjaman);
            if (!$hasilAlat['valid']) {
                return $this->gagal('alat', $hasilAlat);
            }
            $detailAlat = $hasilAlat['detail'] ?? [];
        }

        return [
            'valid' => true,
            'jenis' => null,
            'pesan' => null,
            'tahap' => null,
            'detail' => ['alat' => $detailAlat],
        ];
    }

    /**
     * Versi aman untuk persetujuan yang berjalan bersamaan.
     *
     * WAJIB dipanggil di dalam DB::transaction(), paling awal (sebelum SELECT biasa lain
     * di transaksi yang sama). Method ini mengunci baris ruangan dan unit alat yang relevan,
     * sehingga persetujuan lain untuk ruangan atau jenis alat yang sama menunggu sampai
     * transaksi ini selesai, lalu validasinya dihitung ulang dari data terbaru.
     * Alokasi unit dan perubahan status harus disimpan dalam transaksi yang sama.
     */
    public function validasiDenganKunci(
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        int $idRuangan,
        array $alat = [],
        ?int $idPeminjaman = null
    ): array {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('validasiDenganKunci() harus dipanggil di dalam DB::transaction().');
        }

        // Urutan kunci selalu sama (ruangan dulu, lalu unit berurut id) agar tidak terjadi deadlock.
        DB::table('ruangan')->where('id_ruangan', $idRuangan)->lockForUpdate()->first();

        $idJenis = array_map('intval', array_keys($alat));
        sort($idJenis);
        if ($idJenis !== []) {
            DB::table('alat')->whereIn('id_jenis', $idJenis)->orderBy('id_alat')->lockForUpdate()->pluck('id_alat');
        }

        return $this->validasi($tanggal, $jamMulai, $jamSelesai, $idRuangan, $alat, $idPeminjaman);
    }

    private function gagal(string $tahap, array $hasil): array
    {
        return [
            'valid' => false,
            'jenis' => $hasil['jenis'] ?? null,
            'pesan' => $hasil['pesan'] ?? null,
            'tahap' => $tahap,
            'detail' => ['alat' => $hasil['detail'] ?? []],
        ];
    }
}