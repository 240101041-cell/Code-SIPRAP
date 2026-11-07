@extends('layouts.admin')

@section('title', 'Dashboard Monitoring | SIPRAP')
@section('breadcrumb', 'Admin / Dashboard')

@section('content')
<style>
    .card-stat-figma {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .icon-square-box {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.6rem;">Dashboard Monitoring</h3>
        <p class="text-secondary small m-0">Pemakaian ruangan dan alat, status peminjaman, dan sanksi aktif.</p>
    </div>
    <a href="/admin/laporan" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold" style="background-color: #2563eb;">
        <i class="bi bi-file-earmark-text me-1"></i> Lihat Laporan
    </a>
</div>

<!-- 4 KARTU STATISTIK REAL DARI DATABASE -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #eff6ff; color: #2563eb;">
                <i class="bi bi-calendar2-range"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $totalPeminjamanBulanIni ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Peminjaman Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #f3e8ff; color: #7e22ce;">
                <i class="bi bi-box-arrow-up-right"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $sedangDipinjam ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Sedang Dipinjam</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #dcfce7; color: #16a34a;">
                <i class="bi bi-door-open"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $ruanganTerpakai ?? 0 }} / {{ $totalRuangan ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Ruangan Terpakai Hari Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #fee2e2; color: #dc2626;">
                <i class="bi bi-exclamation-octagon"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $sanksiAktif ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Sanksi Aktif</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- TABEL AKTIVITAS REAL TERBARU -->
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark m-0">Aktivitas Peminjaman Terbaru</h6>
                <a href="/admin/laporan" class="text-primary text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle m-0" style="font-size: 0.88rem;">
                    <thead>
                        <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                            <th>ID Pinjam</th>
                            <th>Peminjam</th>
                            <th>Ruangan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aktivitasTerbaru as $item)
                        <tr>
                            <td class="fw-bold text-dark">#PJ-{{ str_pad($item->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $item->peminjam ?? 'Civitas' }}</strong>
                                <small class="text-secondary" style="font-size: 0.72rem;">{{ $item->keperluan ?? '-' }}</small>
                            </td>
                            <td class="fw-semibold text-dark">{{ $item->nama_ruangan ?? 'Laboratorium' }}</td>
                            <td class="text-center">
                                @php
                                    $st = strtolower($item->status_peminjaman ?? ($item->status ?? 'menunggu'));
                                @endphp
                                @if($st === 'disetujui')
                                    <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1">Disetujui</span>
                                @elseif($st === 'dipinjam')
                                    <span class="badge rounded-pill bg-purple-subtle text-primary px-3 py-1">Dipinjam</span>
                                @elseif($st === 'selesai' || $st === 'dikembalikan')
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">Selesai</span>
                                @elseif($st === 'ditolak')
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1">Ditolak</span>
                                @else
                                    <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary small">
                                <i class="bi bi-inbox fs-3 d-block text-muted mb-2"></i>
                                Belum ada aktivitas transaksi peminjaman di database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- REKAP STATUS DISTRIBUSI REAL DARI DATABASE -->
    <div class="col-lg-4">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Status Peminjaman</h6>
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="small text-secondary"><i class="bi bi-circle-fill text-warning me-2" style="font-size: 0.6rem;"></i>Menunggu Persetujuan</span>
                <strong class="text-dark">{{ $ringkasanStatus->menunggu ?? 0 }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="small text-secondary"><i class="bi bi-circle-fill text-info me-2" style="font-size: 0.6rem;"></i>Disetujui</span>
                <strong class="text-dark">{{ $ringkasanStatus->disetujui ?? 0 }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="small text-secondary"><i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.6rem;"></i>Dipinjam</span>
                <strong class="text-dark">{{ $ringkasanStatus->dipinjam ?? 0 }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="small text-secondary"><i class="bi bi-circle-fill text-success me-2" style="font-size: 0.6rem;"></i>Dikembalikan / Selesai</span>
                <strong class="text-dark">{{ $ringkasanStatus->selesai ?? 0 }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="small text-secondary"><i class="bi bi-circle-fill text-danger me-2" style="font-size: 0.6rem;"></i>Ditolak / Dibatalkan</span>
                <strong class="text-dark">{{ $ringkasanStatus->ditolak ?? 0 }}</strong>
            </div>
        </div>
    </div>
</div>
@stop