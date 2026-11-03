<?php

namespace Tests\Feature;

use App\Services\ValidasiJadwal\ValidasiJadwalPeminjamanService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidasiJadwalPeminjamanServiceTest extends TestCase
{
    private const TGL = '2026-11-01';

    protected function setUp(): void
    {
        parent::setUp();

        // Skema minimal khusus tes (database memori), bukan migration proyek.
        Schema::create('peminjaman', function (Blueprint $t) {
            $t->increments('id_peminjaman');
            $t->unsignedInteger('id_ruangan');
            $t->date('tanggal_peminjaman');
            $t->time('jam_mulai');
            $t->time('jam_selesai');
            $t->string('status_peminjaman');
        });
        Schema::create('penutupan_ruangan', function (Blueprint $t) {
            $t->increments('id_penutupan');
            $t->unsignedInteger('id_ruangan');
            $t->date('tanggal_penutupan');
            $t->time('jam_mulai');
            $t->time('jam_selesai');
        });
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

    private function jenisDenganUnit(int $jumlahUnit): int
    {
        $idJenis = DB::table('jenis_alat')->insertGetId(['nama_jenis' => 'Mikroskop', 'status_jenis' => 'aktif']);
        for ($i = 1; $i <= $jumlahUnit; $i++) {
            DB::table('alat')->insert([
                'id_jenis' => $idJenis,
                'kode_alat' => sprintf('MIK-%02d', $i),
                'kondisi' => 'baik',
                'status_alat' => 'aktif',
            ]);
        }
        return $idJenis;
    }

    private function peminjamanDisetujui(int $idRuangan, string $mulai, string $selesai, array $idAlats = []): int
    {
        $idPem = DB::table('peminjaman')->insertGetId([
            'id_ruangan' => $idRuangan,
            'tanggal_peminjaman' => self::TGL,
            'jam_mulai' => $this->jam($mulai),
            'jam_selesai' => $this->jam($selesai),
            'status_peminjaman' => 'Disetujui',
        ]);
        if ($idAlats !== []) {
            $idJenis = DB::table('alat')->where('id_alat', $idAlats[0])->value('id_jenis');
            $idDetail = DB::table('detail_alat_peminjaman')->insertGetId([
                'id_peminjaman' => $idPem,
                'id_jenis' => $idJenis,
                'jumlah_diminta' => count($idAlats),
            ]);
            foreach ($idAlats as $idAlat) {
                DB::table('unit_alat_peminjaman')->insert(['id_detail' => $idDetail, 'id_alat' => $idAlat]);
            }
        }
        return $idPem;
    }

    private function validasi(string $mulai, string $selesai, int $ruangan, array $alat = [], ?int $idPem = null): array
    {
        return (new ValidasiJadwalPeminjamanService())->validasi(self::TGL, $mulai, $selesai, $ruangan, $alat, $idPem);
    }

    // ---------- tes ----------

    public function test_semua_lolos_valid(): void
    {
        $j = $this->jenisDenganUnit(3);
        $hasil = $this->validasi('09:00', '11:00', 1, [$j => 2]);
        $this->assertTrue($hasil['valid']);
        $this->assertNull($hasil['tahap']);
        $this->assertSame(3, $hasil['detail']['alat'][$j]['tersedia']);
    }

    public function test_hanya_meminjam_ruangan_tanpa_alat_valid(): void
    {
        $this->assertTrue($this->validasi('09:00', '11:00', 1)['valid']);
    }

    public function test_waktu_salah_gagal_di_tahap_waktu(): void
    {
        $hasil = $this->validasi('11:00', '09:00', 1);
        $this->assertFalse($hasil['valid']);
        $this->assertSame('waktu', $hasil['tahap']);
        $this->assertSame('rentang_jam_tidak_valid', $hasil['jenis']);
    }

    public function test_ruangan_bentrok_gagal_di_tahap_ruangan(): void
    {
        $this->peminjamanDisetujui(1, '09:00', '11:00');
        $hasil = $this->validasi('10:00', '12:00', 1);
        $this->assertFalse($hasil['valid']);
        $this->assertSame('ruangan', $hasil['tahap']);
        $this->assertSame('bentrok_peminjaman', $hasil['jenis']);
    }

    public function test_alat_tidak_cukup_gagal_di_tahap_alat(): void
    {
        $j = $this->jenisDenganUnit(2);
        $hasil = $this->validasi('09:00', '11:00', 1, [$j => 3]);
        $this->assertFalse($hasil['valid']);
        $this->assertSame('alat', $hasil['tahap']);
        $this->assertSame('stok_tidak_cukup', $hasil['jenis']);
    }

    public function test_ruangan_diperiksa_sebelum_alat(): void
    {
        $j = $this->jenisDenganUnit(1);
        $this->peminjamanDisetujui(1, '09:00', '11:00');
        $hasil = $this->validasi('10:00', '12:00', 1, [$j => 5]); // ruangan bentrok dan alat kurang
        $this->assertSame('ruangan', $hasil['tahap']);
    }

    public function test_pemeriksaan_ulang_tidak_bentrok_dengan_diri_sendiri(): void
    {
        $j = $this->jenisDenganUnit(3);
        $ids = DB::table('alat')->orderBy('id_alat')->limit(2)->pluck('id_alat')->all();
        $idPem = $this->peminjamanDisetujui(1, '09:00', '11:00', $ids);

        $this->assertTrue($this->validasi('09:00', '11:00', 1, [$j => 3], $idPem)['valid']);
        $this->assertFalse($this->validasi('09:00', '11:00', 1, [$j => 3])['valid']);
    }
}