<?php

namespace Tests\Unit;

use App\Services\ValidasiJadwal\ValidasiWaktuService;
use PHPUnit\Framework\TestCase;

class ValidasiWaktuServiceTest extends TestCase
{
    private function svc(): ValidasiWaktuService
    {
        return new ValidasiWaktuService();
    }

    public function test_jam_mulai_lebih_awal_valid(): void
    {
        $this->assertTrue($this->svc()->validasi('2026-11-01', '09:00', '11:00')['valid']);
    }

    public function test_jam_sama_tidak_valid(): void
    {
        $this->assertFalse($this->svc()->validasi('2026-11-01', '09:00', '09:00')['valid']);
    }

    public function test_jam_mulai_lebih_akhir_tidak_valid(): void
    {
        $this->assertFalse($this->svc()->validasi('2026-11-01', '11:00', '09:00')['valid']);
    }

    public function test_lewat_tengah_malam_tidak_valid(): void
    {
        $this->assertFalse($this->svc()->validasi('2026-11-01', '23:00', '00:30')['valid']);
    }

    public function test_bersentuhan_tidak_bentrok(): void
    {
        $this->assertFalse($this->svc()->bertumpangTindih('09:00', '11:00', '11:00', '13:00'));
        $this->assertFalse($this->svc()->bertumpangTindih('13:00', '15:00', '09:00', '13:00'));
    }

    public function test_tumpang_tindih_bentrok(): void
    {
        $this->assertTrue($this->svc()->bertumpangTindih('09:00', '11:00', '10:00', '12:00'));
        $this->assertTrue($this->svc()->bertumpangTindih('09:00', '11:00', '10:59', '12:00'));
    }
}