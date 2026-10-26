@extends('layouts.admin')

@section('title', 'Dashboard Monitoring | SIPRAP')
@section('breadcrumb', 'Admin / Dashboard')

@section('content')
<style>
    .card-stat-figma {
        background: #ffffff; border: 1px solid #e2e8f0;
        border-radius: 14px; padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex; align-items: center; gap: 16px;
    }
    .icon-square-box {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.6rem;">Dashboard Monitoring</h3>
        <p class="text-secondary small m-0">Pemakaian ruangan dan alat, status peminjaman, dan sanksi aktif · Oktober 2026.</p>
    </div>
    <button class="btn btn-primary px-3 py-2 rounded-3 fw-semibold" style="background-color: #2563eb; font-size: 0.88rem;">
        <i class="bi bi-file-earmark-text me-1"></i> Lihat Laporan
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #e0f2fe; color: #0284c7;"><i class="bi bi-journal-check"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">128</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Peminjaman Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #f3e8ff; color: #7e22ce;"><i class="bi bi-box"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">14</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Sedang Dipinjam</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #dcfce7; color: #16a34a;"><i class="bi bi-door-open"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">3 / 4</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Ruangan Terpakai Hari Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #fee2e2; color: #dc2626;"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">2</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Sanksi Aktif</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-4">Pemakaian per Minggu</h6>
            <div class="d-flex align-items-end justify-content-between px-4 pb-2" style="height: 180px; border-bottom: 1px solid #e2e8f0;">
                <div class="text-center">
                    <div style="width: 44px; height: 90px; background: #93c5fd; border-radius: 8px 8px 0 0;" class="mx-auto mb-2"></div>
                    <small class="text-secondary" style="font-size: 0.75rem;">Minggu 1</small>
                </div>
                <div class="text-center">
                    <div style="width: 44px; height: 130px; background: #2563eb; border-radius: 8px 8px 0 0;" class="mx-auto mb-2"></div>
                    <small class="text-secondary" style="font-size: 0.75rem;">Minggu 2</small>
                </div>
                <div class="text-center">
                    <div style="width: 44px; height: 110px; background: #2563eb; border-radius: 8px 8px 0 0;" class="mx-auto mb-2"></div>
                    <small class="text-secondary" style="font-size: 0.75rem;">Minggu 3</small>
                </div>
                <div class="text-center">
                    <div style="width: 44px; height: 150px; background: #1d4ed8; border-radius: 8px 8px 0 0;" class="mx-auto mb-2"></div>
                    <small class="text-secondary" style="font-size: 0.75rem;">Minggu 4</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Status Peminjaman</h6>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span class="small text-secondary">Menunggu Persetujuan</span>
                <span class="fw-bold text-dark">4</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span class="small text-secondary">Disetujui</span>
                <span class="fw-bold text-dark">9</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span class="small text-secondary">Dipinjam</span>
                <span class="fw-bold text-dark">14</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span class="small text-secondary">Dikembalikan</span>
                <span class="fw-bold text-dark">96</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2">
                <span class="small text-secondary">Ditolak / Dibatalkan</span>
                <span class="fw-bold text-dark">5</span>
            </div>
        </div>
    </div>
</div>
@stop