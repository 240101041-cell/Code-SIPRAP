@extends('layouts.admin')

@section('title', 'Laporan & Sanksi Keterlambatan | SIPRAP')
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
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Laporan & Rekapitulasi</h3>
        <p class="text-secondary small m-0">Laporan riwayat transaksi peminjaman laboratorium serta penegakan sanksi tata tertib.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm px-3 rounded-3 fw-semibold" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Rekap
        </button>
        <button class="btn btn-success btn-sm px-3 rounded-3 fw-semibold">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </button>
    </div>
</div>

<!-- TABEL REKAPITULASI PEMINJAMAN -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Rekap Riwayat Peminjaman Laboratorium</h6>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Periode: Oktober 2026</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 140px;">No. Pengajuan</th>
                    <th>Nama Peminjam</th>
                    <th>Ruangan Diajukan</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Keperluan Praktikum</th>
                    <th class="text-center" style="width: 150px;">Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $row)
                <tr>
                    <td class="fw-bold text-dark">{{ $row->id_peminjaman }}</td>
                    <td class="fw-semibold text-dark">{{ $row->peminjam }}</td>
                    <td>{{ $row->ruangan }}</td>
                    <td class="text-secondary">{{ $row->tanggal }}</td>
                    <td class="text-secondary">{{ $row->keperluan }}</td>
                    <td class="text-center">
                        @if($row->status === 'Disetujui')
                            <span class="badge-status-pill" style="background: #e0f2fe; color: #0369a1;">Disetujui</span>
                        @elseif(str_contains($row->status, 'Terlambat'))
                            <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">Selesai (Terlambat)</span>
                        @else
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Selesai</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- TABEL DAFTAR SANKSI / PELANGGARAN AKTIF -->
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold text-danger m-0"><i class="bi bi-exclamation-octagon me-1"></i> Daftar Sanksi Tata Tertib Aktif</h6>
            <small class="text-secondary" style="font-size: 0.75rem;">Mahasiswa yang terkena pembekuan hak peminjaman sementara.</small>
        </div>
        <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill small fw-semibold">2 Sanksi Berlaku</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th>Mahasiswa / NIM</th>
                    <th>Tindakan Pelanggaran</th>
                    <th>Keterangan Masalah</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                    <th class="text-end" style="width: 130px;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sanksi as $s)
                <tr>
                    <td>
                        <strong class="text-dark d-block">{{ $s->nama }}</strong>
                        <small class="text-muted" style="font-size: 0.72rem;">NIM: {{ $s->nim }}</small>
                    </td>
                    <td class="fw-semibold text-danger">{{ $s->jenis_sanksi }}</td>
                    <td class="text-secondary">{{ $s->keterangan }}</td>
                    <td class="text-center">
                        <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">Aktif</span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-outline-success btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                            Pulihkan Akun
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop