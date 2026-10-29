<?php

namespace Tests\Feature;

use App\Services\ValidasiJadwal\CekBentrokRuanganService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CekBentrokRuanganServiceTest extends TestCase
{
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
    }

    private function pinjam(string $status, string $mulai, string $selesai, string $tanggal = '2026-11-01', int $ruangan = 1): int
    {
        return DB::table('peminjaman')->insertGetId([
            'id_ruangan' => $ruangan,
            'tanggal_peminjaman' => $tanggal,
            'jam_mulai' => date('H:i:s', strtotime($mulai)),
            'jam_selesai' => date('H:i:s', strtotime($selesai)),
            'status_peminjaman' => $status,
        ]);
    }

    private function cek(string $mulai, string $selesai, ?int $abaikan = null): array
    {
        return (new CekBentrokRuanganService())->cek(1, '2026-11-01', $mulai, $selesai, $abaikan);
    }

    public function test_ruangan_kosong_valid(): void
    {
        $this->assertTrue($this->cek('10:00', '12:00')['valid']);
    }

    public function test_bentrok_dengan_disetujui(): void
    {
        $this->pinjam('Disetujui', '09:00', '11:00');
        $hasil = $this->cek('10:00', '12:00');
        $this->assertFalse($hasil['valid']);
        $this->assertSame('bentrok_peminjaman', $hasil['jenis']);
    }

    public function test_bentrok_dengan_dipinjam(): void
    {
        $this->pinjam('Dipinjam', '09:00', '11:00');
        $this->assertFalse($this->cek('10:00', '12:00')['valid']);
    }

    public function test_menunggu_persetujuan_tidak_mengunci(): void
    {
        $this->pinjam('Menunggu Persetujuan', '09:00', '11:00');
        $this->assertTrue($this->cek('10:00', '12:00')['valid']);
    }

    public function test_ditolak_dan_dibatalkan_tidak_mengunci(): void
    {
        $this->pinjam('Ditolak', '09:00', '11:00');
        $this->pinjam('Dibatalkan', '09:00', '11:00');
        $this->assertTrue($this->cek('10:00', '12:00')['valid']);
    }

    public function test_bersentuhan_tidak_bentrok(): void
    {
        $this->pinjam('Disetujui', '09:00', '11:00');
        $this->assertTrue($this->cek('11:00', '13:00')['valid']);
        $this->assertTrue($this->cek('07:00', '09:00')['valid']);
    }

    public function test_tanggal_atau_ruangan_lain_tidak_bentrok(): void
    {
        $this->pinjam('Disetujui', '09:00', '11:00', '2026-11-02');
        $this->pinjam('Disetujui', '09:00', '11:00', '2026-11-01', 2);
        $this->assertTrue($this->cek('10:00', '12:00')['valid']);
    }

    public function test_bentrok_dengan_penutupan(): void
    {
        DB::table('penutupan_ruangan')->insert([
            'id_ruangan' => 1,
            'tanggal_penutupan' => '2026-11-01',
            'jam_mulai' => '13:00:00', 'jam_selesai' => '15:00:00',
        ]);
        $hasil = $this->cek('14:00', '16:00');
        $this->assertFalse($hasil['valid']);
        $this->assertSame('bentrok_penutupan', $hasil['jenis']);
        $this->assertTrue($this->cek('15:00', '16:00')['valid']);
    }

    public function test_pemeriksaan_ulang_tidak_bentrok_dengan_diri_sendiri(): void
    {
        $id = $this->pinjam('Disetujui', '09:00', '11:00');
        $this->assertTrue($this->cek('09:00', '11:00', $id)['valid']);
    }
}