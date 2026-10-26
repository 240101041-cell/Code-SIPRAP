@extends('layouts.peminjam')

@section('title', 'Dashboard Peminjam | SIPRAP')
@section('breadcrumb', 'Peminjam / Dashboard')

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
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Dashboard</h3>
        <p class="text-secondary small m-0">Selamat datang, <strong>{{ session('nama') }}</strong>. Berikut ringkasan aktivitas peminjaman Anda.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/peminjam/ketersediaan" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3 fw-semibold">
            <i class="bi bi-calendar3 me-1"></i> Cek Ketersediaan
        </a>
        <a href="/peminjam/pengajuan" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold" style="background-color: #2563eb;">
            <i class="bi bi-plus-lg me-1"></i> Ajukan Peminjaman
        </a>
    </div>
</div>

<!-- 4 KARTU STATISTIK DARI DATABASE -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #fef3c7; color: #b45309;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->menunggu ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Menunggu Persetujuan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->disetujui ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Disetujui (Siap Ambil)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #f3e8ff; color: #7e22ce;">
                <i class="bi bi-box"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->dipinjam ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Sedang Dipinjam</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat-figma">
            <div class="icon-square-box" style="background: #dcfce7; color: #16a34a;">
                <i class="bi bi-journal-check"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark m-0">{{ $statistik->total_pinjam ?? 0 }}</h3>
                <small class="text-secondary" style="font-size: 0.78rem;">Total Riwayat Pengajuan</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark m-0">Peminjaman Berjalan</h6>
                <a href="/peminjam/riwayat" class="text-primary text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle m-0" style="font-size: 0.88rem;">
                    <thead>
                        <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                            <th>Ruangan</th>
                            <th>Jadwal Praktikum</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman_aktif as $p)
                        <tr>
                            <td>
                                <strong class="text-dark d-block">{{ $p->nama_ruangan ?? 'Ruangan Lab' }}</strong>
                                <small class="text-secondary" style="font-size: 0.72rem;">{{ $p->keperluan }}</small>
                            </td>
                            <td class="text-secondary">{{ $p->tanggal_mulai }} s/d {{ $p->tanggal_selesai }}</td>
                            <td class="text-center">
                                @if($p->status === 'disetujui')
                                    <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1">Disetujui</span>
                                @elseif($p->status === 'dipinjam')
                                    <span class="badge rounded-pill bg-purple-subtle text-primary px-3 py-1">Dipinjam</span>
                                @else
                                    <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="/peminjam/detail/{{ $p->id_peminjaman }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary small">
                                <i class="bi bi-inbox fs-3 d-block text-muted mb-2"></i>
                                Belum ada peminjaman yang sedang aktif saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Informasi Tata Tertib</h6>
            <div class="p-3 rounded-3 mb-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                <small class="text-primary d-block fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i> Pengambilan Kunci</small>
                <small class="text-secondary" style="font-size: 0.75rem;">Pengambilan kunci lab dilayani di Ruang Laboran maksimal 15 menit sebelum praktikum dimulai.</small>
            </div>
            <div class="p-3 rounded-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                <small class="text-danger d-block fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Sanksi Keterlambatan</small>
                <small class="text-secondary" style="font-size: 0.75rem;">Keterlambatan pengembalian lebih dari 30 menit akan memicu sanksi pembekuan izin peminjaman.</small>
            </div>
        </div>
    </div>
</div>
@stop