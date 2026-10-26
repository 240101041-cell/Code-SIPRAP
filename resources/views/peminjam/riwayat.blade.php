@extends('layouts.peminjam')

@section('title', 'Riwayat Pengajuan | SIPRAP')
@section('breadcrumb', 'Peminjam / Status Pengajuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Status & Riwayat Pengajuan</h3>
        <p class="text-secondary small m-0">Pantau seluruh proses verifikasi, peminjaman aktif, hingga arsip selesai.</p>
    </div>
</div>

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 130px;">ID Pinjam</th>
                    <th>Ruangan</th>
                    <th>Jadwal Mulai</th>
                    <th>Jadwal Selesai</th>
                    <th>Keperluan</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                <tr>
                    <td class="fw-bold text-dark">#PJ-{{ str_pad($item->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-semibold text-dark">{{ $item->nama_ruangan ?? 'Laboratorium' }}</td>
                    <td class="text-secondary">{{ $item->tanggal_mulai }}</td>
                    <td class="text-secondary">{{ $item->tanggal_selesai }}</td>
                    <td class="text-secondary">{{ $item->keperluan }}</td>
                    <td class="text-center">
                        @if($item->status === 'disetujui')
                            <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1">Disetujui</span>
                        @elseif($item->status === 'dipinjam')
                            <span class="badge rounded-pill bg-purple-subtle text-primary px-3 py-1">Dipinjam</span>
                        @elseif($item->status === 'selesai')
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">Selesai</span>
                        @elseif($item->status === 'ditolak')
                            <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1">Ditolak</span>
                        @else
                            <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1">Menunggu</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="/peminjam/detail/{{ $item->id_peminjaman }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-secondary small">
                        <i class="bi bi-journal-x fs-2 d-block text-muted mb-2"></i>
                        Belum ada riwayat pengajuan peminjaman di akun Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop