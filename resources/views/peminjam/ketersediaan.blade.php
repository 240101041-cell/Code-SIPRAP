@extends('layouts.peminjam')

@section('title', 'Cek Ketersediaan | SIPRAP')
@section('breadcrumb', 'Peminjam / Cek Ketersediaan')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .badge-status-pill {
        border-radius: 50px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Cek Ketersediaan Fasilitas</h3>
        <p class="text-secondary small m-0">Informasi ketersediaan ruangan laboratorium dan peralatan praktikum terkini.</p>
    </div>
    <a href="/peminjam/pengajuan" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold" style="background-color: #2563eb;">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Peminjaman
    </a>
</div>

<!-- TABEL DAFTAR RUANGAN LABORATORIUM -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Katalog Ruangan Laboratorium</h6>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Total: {{ $ruangan->count() }} Ruangan</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th>Nama Laboratorium</th>
                    <th>Lokasi</th>
                    <th class="text-center">Kapasitas</th>
                    <th>Fasilitas Penunjang</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangan as $r)
                <tr>
                    <td class="fw-bold text-dark">{{ $r->nama_ruangan }}</td>
                    <td class="text-secondary">{{ $r->lokasi ?? '-' }}</td>
                    <td class="text-center fw-semibold">{{ $r->kapasitas ?? 30 }} orang</td>
                    <td class="text-secondary small">{{ $r->fasilitas ?? 'Fasilitas standar praktikum' }}</td>
                    <td class="text-center">
                        @php
                            $statusR = $r->status_ruangan ?? $r->status ?? 'tersedia';
                        @endphp
                        @if(strtolower($statusR) === 'tersedia')
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Tersedia</span>
                        @else
                            <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">Terpakai</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="/peminjam/pengajuan" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="background-color: #2563eb; font-size: 0.8rem;">
                            Ajukan
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-secondary small">
                        <i class="bi bi-door-closed fs-3 d-block text-muted mb-2"></i>
                        Belum ada data ruangan di database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- TABEL DAFTAR PERALATAN PRAKTIKUM -->
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Katalog Alat Praktikum Tersedia</h6>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Total: {{ $alat->count() }} Jenis Alat</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th>Nama Alat</th>
                    <th class="text-center">Sisa Stok Unit</th>
                    <th class="text-center">Kondisi Fisik</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $a)
                <tr>
                    <td class="fw-semibold text-dark">{{ $a->nama_alat }}</td>
                    <td class="text-center fw-bold text-primary">{{ $a->stok ?? $a->jumlah ?? $a->stok_alat ?? 0 }} unit</td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">
                            {{ ucfirst($a->kondisi ?? 'Baik') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-secondary small">
                        <i class="bi bi-box-seam fs-3 d-block text-muted mb-2"></i>
                        Belum ada data peralatan praktikum di database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop