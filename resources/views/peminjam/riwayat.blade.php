@extends('layouts.peminjam')

@section('title', 'Status & Riwayat Pengajuan | SIPRAP')
@section('breadcrumb', 'Peminjam / Status Pengajuan')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .search-input-box {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 7px 14px 7px 36px;
        font-size: 0.88rem;
        width: 260px;
    }
    .search-input-box:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .badge-status-pill {
        border-radius: 50px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-block;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Status & Riwayat Pengajuan</h3>
        <p class="text-secondary small m-0">Pantau seluruh proses verifikasi, peminjaman aktif, hingga arsip selesai.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Pencarian Instan Riwayat -->
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
            <input type="text" id="searchRiwayat" class="form-control search-input-box" placeholder="Cari ruangan / ID...">
        </div>
        <a href="/peminjam/pengajuan" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #2563eb;">
            <i class="bi bi-plus-lg"></i> Ajukan Peminjaman
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>{{ session('error') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="tabelRiwayat">
            <thead>
                <tr class="text-secondary" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 120px;">ID Pinjam</th>
                    <th style="width: 220px;">Ruangan Diajukan</th>
                    <th style="width: 170px;">Waktu Mulai</th>
                    <th style="width: 170px;">Waktu Selesai</th>
                    <th>Keperluan / Agenda</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 100px; padding-right: 12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                @php
                    $st = strtolower($item->status_peminjaman ?? ($item->status ?? 'menunggu'));
                    
                    // Format waktu praktikum
                    $mulai = $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y, H:i') : '-';
                    $selesai = $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y, H:i') : '-';
                @endphp
                <tr class="riwayat-row" style="border-bottom: 1px solid #f8fafc;">
                    <td class="fw-bold text-dark item-id">
                        #PJ-{{ str_pad($item->id_peminjaman, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>
                        <strong class="text-dark d-block item-ruang">{{ $item->nama_ruangan ?? 'Laboratorium' }}</strong>
                    </td>
                    <td class="text-secondary">{{ $mulai }}</td>
                    <td class="text-secondary">{{ $selesai }}</td>
                    <td class="text-secondary">
                        <span class="d-inline-block text-truncate" style="max-width: 260px;" title="{{ $item->keperluan }}">
                            {{ $item->keperluan ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($st === 'disetujui')
                            <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1 fw-semibold">Disetujui</span>
                        @elseif($st === 'dipinjam')
                            <span class="badge rounded-pill bg-purple-subtle text-primary px-3 py-1 fw-semibold">Dipinjam</span>
                        @elseif($st === 'selesai' || $st === 'dikembalikan')
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1 fw-semibold">Selesai</span>
                        @elseif($st === 'ditolak' || $st === 'dibatalkan')
                            <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1 fw-semibold">Ditolak</span>
                        @else
                            <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1 fw-semibold">Menunggu</span>
                        @endif
                    </td>
                    <td class="text-end" style="padding-right: 12px;">
                        <a href="/peminjam/detail/{{ $item->id_peminjaman }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                            Detail
                        </a>
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

<script>
    // Live filter pencarian instan pada tabel riwayat
    document.getElementById('searchRiwayat').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tabelRiwayat tbody tr.riwayat-row');

        rows.forEach(row => {
            const idPinjam = row.querySelector('.item-id')?.textContent.toLowerCase() || '';
            const ruang = row.querySelector('.item-ruang')?.textContent.toLowerCase() || '';

            if (idPinjam.includes(filter) || ruang.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@stop