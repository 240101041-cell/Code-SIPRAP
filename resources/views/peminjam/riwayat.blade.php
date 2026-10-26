@extends('adminlte::page')

@section('title', 'Riwayat Peminjaman Fasilitas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="fw-bold text-dark m-0" style="font-size: 1.6rem;">Riwayat & Status Pengajuan</h1>
            <p class="text-secondary small m-0">Pantau proses verifikasi, serah-terima kunci, dan status pengembalian fasilitas.</p>
        </div>
        <a href="{{ url('/peminjam/pengajuan') }}" class="btn btn-primary rounded-pill px-3 py-2 shadow-sm font-weight-bold">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Peminjaman Baru
        </a>
    </div>
@stop

@section('content')
<style>
    .card-modern {
        border: none !important;
        border-radius: 14px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
    }
    .table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        vertical-align: middle;
        padding: 14px 16px;
    }
    .table tbody td {
        vertical-align: middle;
        color: #334155;
        font-size: 0.92rem;
        border-top: 1px solid #f1f5f9;
        padding: 14px 16px;
    }
    .badge-soft-warning { background-color: #fef3c7; color: #b45309; }
    .badge-soft-success { background-color: #dcfce7; color: #15803d; }
    .badge-soft-primary { background-color: #e0e7ff; color: #4338ca; }
    .badge-soft-danger  { background-color: #fee2e2; color: #b91c1c; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }
</style>

<div class="card card-modern p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead>
                <tr>
                    <th style="width: 130px;">Kode Booking</th>
                    <th>Jadwal & Waktu</th>
                    <th>Ruangan / Lab</th>
                    <th>Tujuan Penggunaan</th>
                    <th class="text-center" style="width: 170px;">Status</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                <tr>
                    <td class="fw-bold text-primary">#{{ $item->id_peminjaman }}</td>
                    <td>
                        <span class="d-block fw-semibold text-dark">
                            <i class="bi bi-calendar-event text-secondary me-1"></i> {{ date('d M Y', strtotime($item->tanggal_peminjaman)) }}
                        </span>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>08:00 - 11:30 WIB</small>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">{{ $item->ruangan }}</span>
                    </td>
                    <td>
                        <span class="text-secondary">{{ $item->keperluan }}</span>
                    </td>
                    <td class="text-center">
                        @if($item->status_peminjaman == 'Menunggu Persetujuan')
                            <span class="badge rounded-pill badge-soft-warning px-3 py-2 fw-semibold">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu
                            </span>
                        @elseif($item->status_peminjaman == 'Disetujui')
                            <span class="badge rounded-pill badge-soft-success px-3 py-2 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Disetujui
                            </span>
                        @elseif($item->status_peminjaman == 'Dipinjam')
                            <span class="badge rounded-pill badge-soft-primary px-3 py-2 fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Sedang Dipakai
                            </span>
                        @elseif($item->status_peminjaman == 'Ditolak')
                            <span class="badge rounded-pill badge-soft-danger px-3 py-2 fw-semibold">
                                <i class="bi bi-x-circle me-1"></i> Ditolak
                            </span>
                        @else
                            <span class="badge rounded-pill badge-soft-secondary px-3 py-2 fw-semibold">
                                <i class="bi bi-archive me-1"></i> Selesai
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 border shadow-xs" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $loop->index }}">
                            <i class="bi bi-eye text-primary"></i> Detail
                        </button>

                        <!-- MODAL DETAIL PEMINJAMAN -->
                        <div class="modal fade" id="modalDetail{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header border-bottom border-light">
                                        <h6 class="modal-title fw-bold text-dark">Rincian Booking #{{ $item->id_peminjaman }}</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <small class="text-muted d-block font-weight-bold">Ruangan Terpilih</small>
                                            <span class="text-dark fw-semibold">{{ $item->ruangan }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block font-weight-bold">Alat Praktikum Tambahan</small>
                                            <ul class="list-unstyled mb-0 mt-1 small text-dark">
                                                <li><i class="bi bi-check2 text-success me-1"></i> Proyektor Epson (1 Unit)</li>
                                                <li><i class="bi bi-check2 text-success me-1"></i> Kabel LAN Tester (2 Unit)</li>
                                            </ul>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted d-block font-weight-bold">Lampiran Berkas</small>
                                            <a href="#" class="btn btn-outline-primary btn-sm rounded-pill mt-1">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh Proposal Kegiatan
                                            </a>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block font-weight-bold">Catatan Verifikasi Laboran</small>
                                            <p class="small text-secondary mb-0 bg-light p-2 rounded-3 mt-1">
                                                Harap mengambil kunci laboratorium dan memeriksa kondisi kabel konektor maksimal 15 menit sebelum sesi praktikum dimulai.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top border-light">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4 d-block mb-2 text-secondary"></i>
                        Belum ada riwayat permohonan peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop