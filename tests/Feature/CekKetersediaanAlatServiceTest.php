<?php

namespace Tests\Feature;

use App\Services\ValidasiJadwal\CekKetersediaanAlatService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CekKetersediaanAlatServiceTest extends TestCase
{
    private const TGL = '2026-11-01';

    protected function setUp(): void
    {
        parent::setUp();

        // Skema minimal khusus tes (database memori), bukan migration proyek.
        Schema::create('jenis_alat', function (Blueprint $t) {
            $t->increments('id_jenis');
            $t->string('nama_jenis');
            $t->string('status_jenis');
        });
        Schema::create('alat', function (Blueprint $t) {
            $t->increments('id_alat');
            $t->unsignedInteger('id_jenis');
            $t->string('kode_alat');
            $t->string('kondisi');
            $t->string('status_alat');
        });
        Schema::create('peminjaman', function (Blueprint $t) {
            $t->increments('id_peminjaman');
            $t->date('tanggal_peminjaman');
            $t->time('jam_mulai');
            $t->time('jam_selesai');
            $t->string('status_peminjaman');
        });
        Schema::create('detail_alat_peminjaman', function (Blueprint $t) {
            $t->increments('id_detail');
            $t->unsignedInteger('id_peminjaman');
            $t->unsignedInteger('id_jenis');
            $t->unsignedInteger('jumlah_diminta');
        });
        Schema::create('unit_alat_peminjaman', function (Blueprint $t) {
            $t->increments('id_unit_peminjaman');
            $t->unsignedInteger('id_detail');
            $t->unsignedInteger('id_alat');
        });
        Schema::create('pembekuan_alat', function (Blueprint $t) {
            $t->increments('id_pembekuan');
            $t->unsignedInteger('id_alat');
            $t->date('tanggal_pembekuan');
            $t->time('jam_mulai');
            $t->time('jam_selesai');
        });
    }

    // ---------- helper ----------

    private function jam(string $j): string
    {
        return date('H:i:s', strtotime($j));
    }

    private function jenis(string $nama = 'Mikroskop', string $status = 'aktif'): int
    {
        return DB::table('jenis_alat')->insertGetId(['nama_jenis' => $nama, 'status_jenis' => $status]);
    }

    private function unit(int $idJenis, string $kode, string $kondisi = 'baik', string $status = 'aktif'): int
    {
        return DB::table('alat')->insertGetId([
            'id_jenis' => $idJenis,
            'kode_alat' => $kode,
            'kondisi' => $kondisi,
            'status_alat' => $status,
        ]);
    }

    private function units(int $idJenis, int $n): array
    {
        $ids = [];
        for ($i = 1; $i <= $n; $i++) {
            $ids[] = $this->unit($idJenis, sprintf('MIK-%02d', $i));
        }
        return $ids;
    }

    private function alokasi(array $idAlats, string $status, string $mulai, string $selesai): int
    {
        $idPem = DB::table('peminjaman')->insertGetId([
            'tanggal_peminjaman' => self::TGL,
            'jam_mulai' => $this->jam($mulai),
            'jam_selesai' => $this->jam($selesai),
            'status_peminjaman' => $status,
        ]);
        $idJenis = DB::table('alat')->where('id_alat', $idAlats[0])->value('id_jenis');
        $idDetail = DB::table('detail_alat_peminjaman')->insertGetId([
            'id_peminjaman' => $idPem,
            'id_jenis' => $idJenis,
            'jumlah_diminta' => count($idAlats),
        ]);
        foreach ($idAlats as $idAlat) {
            DB::table('unit_alat_peminjaman')->insert(['id_detail' => $idDetail, 'id_alat' => $idAlat]);
        }
        return $idPem;
    }

    private function bekukan(int $idAlat, string $mulai, string $selesai): void
    {
        DB::table('pembekuan_alat')->insert([
            'id_alat' => $idAlat,
            'tanggal_pembekuan' => self::TGL,
            'jam_mulai' => $this->jam($mulai),
            'jam_selesai' => $this->jam($selesai),
        ]);
    }

    private function cek(int $idJenis, int|string $jumlah, string $mulai, string $selesai, ?int $abaikan = null): array
    {
        return (new CekKetersediaanAlatService())->cek(self::TGL, $mulai, $selesai, [$idJenis => $jumlah], $abaikan);
    }

    // ---------- tes ----------

    public function test_kebutuhan_lebih_kecil_dan_sama_dengan_stok_valid(): void
    {
        $j = $this->jenis();
        $this->units($j, 3);
        $this->assertTrue($this->cek($j, 2, '09:00', '11:00')['valid']);
        $this->assertTrue($this->cek($j, 3, '09:00', '11:00')['valid']);
    }

    public function test_kebutuhan_melebihi_stok_tidak_valid(): void
    {
        $j = $this->jenis();
        $this->units($j, 3);
        $hasil = $this->cek($j, 4, '09:00', '11:00');
        $this->assertFalse($hasil['valid']);
        $this->assertSame('stok_tidak_cukup', $hasil['jenis']);
    }

    public function test_unit_rusak_hilang_dan_nonaktif_tidak_dihitung(): void
    {
        $j = $this->jenis();
        $this->unit($j, 'MIK-01');
        $this->unit($j, 'MIK-02', 'rusak');
        $this->unit($j, 'MIK-03', 'hilang');
        $this->unit($j, 'MIK-04', 'baik', 'nonaktif');

        $this->assertTrue($this->cek($j, 1, '09:00', '11:00')['valid']);
        $this->assertFalse($this->cek($j, 2, '09:00', '11:00')['valid']);
    }

    public function test_alokasi_yang_tumpang_tindih_mengurangi_unit_tersedia(): void
    {
        $j = $this->jenis();
        [$u1, $u2] = $this->units($j, 3);
        $this->alokasi([$u1, $u2], 'Disetujui', '09:00', '11:00');

        $this->assertFalse($this->cek($j, 2, '10:00', '12:00')['valid']);
        $this->assertTrue($this->cek($j, 1, '10:00', '12:00')['valid']); // unit lain sejenis tetap bisa
    }

    public function test_status_dipinjam_juga_mengunci(): void
    {
        $j = $this->jenis();
        $ids = $this->units($j, 2);
        $this->alokasi($ids, 'Dipinjam', '09:00', '11:00');
        $this->assertFalse($this->cek($j, 1, '10:00', '12:00')['valid']);
    }

    public function test_jadwal_bersentuhan_tidak_mengurangi(): void
    {
        $j = $this->jenis();
        $ids = $this->units($j, 3);
        $this->alokasi($ids, 'Disetujui', '09:00', '11:00');

        $this->assertTrue($this->cek($j, 3, '11:00', '13:00')['valid']);
        $this->assertTrue($this->cek($j, 3, '07:00', '09:00')['valid']);
    }

    public function test_status_selesai_atau_batal_tidak_mengunci(): void
    {
        $j = $this->jenis();
        $ids = $this->units($j, 3);
        $this->alokasi($ids, 'Dikembalikan', '09:00', '11:00');
        $this->alokasi($ids, 'Dibatalkan', '09:00', '11:00');
        $this->alokasi($ids, 'Ditolak', '09:00', '11:00');

        $this->assertTrue($this->cek($j, 3, '10:00', '12:00')['valid']);
    }

    public function test_pembekuan_unit_mengurangi_hanya_pada_rentangnya(): void
    {
        $j = $this->jenis();
        [$u1] = $this->units($j, 3);
        $this->bekukan($u1, '09:00', '11:00');

        $this->assertFalse($this->cek($j, 3, '10:00', '12:00')['valid']);
        $this->assertTrue($this->cek($j, 3, '11:00', '13:00')['valid']);
    }

    public function test_unit_yang_dialokasikan_dan_dibekukan_tidak_dihitung_dua_kali(): void
    {
        $j = $this->jenis();
        [$u1] = $this->units($j, 3);
        $this->alokasi([$u1], 'Disetujui', '09:00', '11:00');
        $this->bekukan($u1, '09:00', '11:00');

        $hasil = $this->cek($j, 3, '10:00', '12:00');
        $this->assertFalse($hasil['valid']);
        $this->assertSame(2, $hasil['detail'][$j]['tersedia']); // 3 unit - 1 unit, bukan - 2
        $this->assertTrue($this->cek($j, 2, '10:00', '12:00')['valid']);
    }

    public function test_pemeriksaan_ulang_tidak_menghitung_alokasi_sendiri(): void
    {
        $j = $this->jenis();
        [$u1, $u2] = $this->units($j, 3);
        $idPem = $this->alokasi([$u1, $u2], 'Disetujui', '09:00', '11:00');

        $this->assertTrue($this->cek($j, 3, '09:00', '11:00', $idPem)['valid']);
        $this->assertFalse($this->cek($j, 3, '09:00', '11:00')['valid']);
    }

    public function test_jumlah_nol_atau_negatif_tidak_valid(): void
    {
        $j = $this->jenis();
        $this->units($j, 3);
        $this->assertSame('jumlah_tidak_valid', $this->cek($j, 0, '09:00', '11:00')['jenis']);
        $this->assertSame('jumlah_tidak_valid', $this->cek($j, -1, '09:00', '11:00')['jenis']);
    }

    public function test_jenis_tidak_ada_atau_nonaktif_tidak_valid(): void
    {
        $nonaktif = $this->jenis('Osiloskop', 'nonaktif');
        $this->unit($nonaktif, 'OSI-01');

        $this->assertSame('jenis_tidak_valid', $this->cek($nonaktif, 1, '09:00', '11:00')['jenis']);
        $this->assertSame('jenis_tidak_valid', $this->cek(999, 1, '09:00', '11:00')['jenis']);
    }

    public function test_daftar_unit_tersedia_tidak_memuat_unit_yang_terpakai(): void
    {
        $j = $this->jenis();
        [$u1, $u2, $u3] = $this->units($j, 3);
        $this->alokasi([$u1], 'Disetujui', '09:00', '11:00');

        $daftar = (new CekKetersediaanAlatService())->daftarUnitTersedia($j, self::TGL, '10:00', '12:00');
        $this->assertEquals([$u2, $u3], $daftar);
    }
}