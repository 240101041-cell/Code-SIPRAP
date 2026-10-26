@extends('layouts.laboran')

@section('title', 'Dashboard Laboran | SIPRAP')
@section('breadcrumb', 'Laboran / Dashboard')

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

<div class="mb-4">
    <h3 class="fw-bold text-dark m-0" style="font-size: 1.6rem;">Dashboard Laboran</h3>
    <p class="text-secondary small m-0">Ringkasan aktivitas operasional laboratorium dan permohonan peminjaman.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #fef3c7; color: #b45309;"><i class="bi bi-clipboard"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->menunggu_verifikasi ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Menunggu Verifikasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #e0f2fe; color: #0284c7;"><i class="bi bi-hand-index"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->siap_serah_terima ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Siap Serah-Terima</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #f3e8ff; color: #7e22ce;"><i class="bi bi-box"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->sedang_dipinjam ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Sedang Dipinjam</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #fee2e2; color: #dc2626;"><i class="bi bi-clock"></i></div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->jatuh_tempo_hari_ini ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Jatuh Tempo Hari Ini</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark m-0">Antrian Verifikasi Masuk</h6>
                <a href="/laboran/verifikasi" class="text-primary text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            @forelse($antrian_verifikasi as $item)
            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark d-block small">#PJ-{{ str_pad($item->id_peminjaman, 4, '0', STR_PAD_LEFT) }} · {{ $item->nama_ruangan ?? 'Lab' }}</strong>
                    <small class="text-secondary" style="font-size: 0.72rem;">{{ $item->peminjam }} · {{ $item->keperluan }}</small>
                </div>
                <a href="/laboran/verifikasi/{{ $item->id_peminjaman }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem; background-color: #2563eb;">Periksa</a>
            </div>
            @empty
            <div class="text-center py-4 text-secondary small">
                <i class="bi bi-check2-all fs-3 d-block text-success mb-2"></i>
                Tidak ada antrian pengajuan baru yang menunggu verifikasi.
            </div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Pengembalian Hari Ini</h6>
            @forelse($pengembalian_hari_ini as $ret)
            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark d-block small">{{ $ret->nama_ruangan }}</strong>
                    <small class="text-secondary" style="font-size: 0.72rem;">{{ $ret->peminjam }}</small>
                </div>
                <a href="/laboran/pengembalian" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">Proses</a>
            </div>
            @empty
            <div class="text-center py-4 text-secondary small">
                <i class="bi bi-calendar2-check fs-3 d-block text-muted mb-2"></i>
                Tidak ada jadwal pengembalian untuk hari ini.
            </div>
            @endforelse
        </div>
    </div>
</div>
@stop