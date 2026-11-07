@extends('layouts.admin')

@section('title', 'Laporan & Rekapitulasi | SIPRAP')
@section('breadcrumb', 'Admin / Laporan')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .badge-status-pill {
        border-radius: 50px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    /* KOP DOKUMEN CETAK (Hanya muncul saat print) */
    .print-header {
        display: none;
    }
    .print-signature {
        display: none;
    }

    /* CSS KHUSUS PRINT / CETAK RESMI */
    @media print {
        aside, .sidebar, nav, .btn, .breadcrumb, header, .no-print {
            display: none !important;
        }

        body, .content-wrapper, main {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .card-custom {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .print-header {
            display: block !important;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .print-signature {
            display: flex !important;
            justify-content: flex-end;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .table-responsive {
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        table th, table td {
            border: 1px solid #cbd5e1 !important;
            padding: 8px 10px !important;
            font-size: 11pt !important;
        }

        thead th {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<!-- KOP RESMI SURAT LAPORAN (HANYA AKTIF SAAT CETAK / SAVE PDF) -->
<div class="print-header">
    <h4 class="fw-bold m-0" style="letter-spacing: 0.5px;">UNIVERSITAS DUTA BANGSA SURAKARTA</h4>
    <h5 class="fw-semibold m-0" style="color: #334155;">UPT LABORATORIUM & PUSAT KOMPUTER</h5>
    <p class="small text-muted m-0">Jl. Bhayangkara No. 55, Surakarta | Sistem Informasi Peminjaman Lab (SIPRAP)</p>
    <div style="margin-top: 8px; font-size: 0.95rem; font-weight: bold; text-decoration: underline;">
        REKAPITULASI PEMINJAMAN RUANGAN & ALAT PRAKTIKUM
    </div>
</div>

<!-- HEADER DI TAMPILAN WEB -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 no-print">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Laporan & Rekapitulasi</h3>
        <p class="text-secondary small m-0">Laporan riwayat transaksi peminjaman laboratorium dan fasilitas praktikum.</p>
    </div>
    <div class="d-flex gap-2">
        <!-- TOMBOL UNDUH EXCEL (.CSV) -->
        <a href="/admin/laporan/ekspor-excel" class="btn btn-outline-success btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1">
            <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
        </a>
        <!-- TOMBOL CETAK LAPORAN RESMI -->
        <button class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #2563eb;" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak Laporan Resmi
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2 no-print">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- TABEL REKAPITULASI PEMINJAMAN -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Daftar Transaksi Peminjaman Laboratorium</h6>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small no-print">Total: {{ $laporan->count() }} Data</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 120px;">ID Pinjam</th>
                    <th>Nama Peminjam</th>
                    <th>Ruangan Diajukan</th>
                    <th>Waktu / Tanggal</th>
                    <th>Keperluan</th>
                    <th class="text-center" style="width: 130px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $row)
                <tr>
                    <td class="fw-bold text-dark">#PJ-{{ str_pad($row->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-semibold text-dark">{{ $row->peminjam ?? 'Civitas' }}</td>
                    <td>{{ $row->nama_ruangan ?? ($row->ruangan ?? 'Laboratorium') }}</td>
                    <td class="text-secondary">
                        {{ $row->tanggal_mulai ?? ($row->tanggal_peminjaman ?? ($row->tanggal ?? $row->created_at)) }}
                    </td>
                    <td class="text-secondary">{{ $row->keperluan ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            $st = strtolower($row->status_peminjaman ?? ($row->status ?? 'menunggu'));
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
                    <td colspan="6" class="text-center py-5 text-secondary small">
                        <i class="bi bi-file-earmark-x fs-2 d-block text-muted mb-2 no-print"></i>
                        Belum ada riwayat transaksi peminjaman di database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- BLOK TANDA TANGAN RESMI (MUNCUL SAAT CETAK / PDF) -->
<div class="print-signature">
    <div style="text-align: center; width: 220px;">
        <p class="m-0" style="font-size: 11pt;">Surakarta, {{ date('d F Y') }}</p>
        <p class="m-0" style="font-size: 11pt;">Kepala Laboratorium UDB,</p>
        <div style="height: 60px;"></div>
        <p class="m-0 fw-bold" style="font-size: 11pt; text-decoration: underline;">Budi Santoso, M.Kom.</p>
        <p class="m-0 text-muted" style="font-size: 9pt;">NIP. 19820514 200812 1 002</p>
    </div>
</div>
@stop