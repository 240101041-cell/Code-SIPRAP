@extends('layouts.laboran')

@section('title', 'Verifikasi Pengajuan | SIPRAP')
@section('breadcrumb', 'Laboran / Verifikasi Pengajuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Verifikasi Pengajuan</h3>
        <p class="text-secondary small m-0">Daftar permohonan peminjaman ruangan dan alat yang menunggu persetujuan laboran.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>{{ session('warning') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 120px;">ID Pinjam</th>
                    <th>Pemohon</th>
                    <th>Ruangan</th>
                    <th>Jadwal Mulai</th>
                    <th>Jadwal Selesai</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrian as $item)
                <tr>
                    <td class="fw-bold text-dark">#PJ-{{ str_pad($item->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <strong class="text-dark d-block">{{ $item->pemohon }}</strong>
                        <small class="text-secondary" style="font-size: 0.72rem;">{{ $item->keperluan }}</small>
                    </td>
                    <td class="fw-semibold text-dark">{{ $item->nama_ruangan ?? 'Laboratorium' }}</td>
                    <td class="text-secondary">{{ $item->tanggal_mulai }}</td>
                    <td class="text-secondary">{{ $item->tanggal_selesai }}</td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1">Menunggu</span>
                    </td>
                    <td class="text-end">
                        <a href="/laboran/verifikasi/{{ $item->id_peminjaman }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="background-color: #2563eb; font-size: 0.8rem;">Periksa</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-secondary small">
                        <i class="bi bi-check-circle fs-2 d-block text-success mb-2"></i>
                        Semua antrian pengajuan sudah selesai diverifikasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop