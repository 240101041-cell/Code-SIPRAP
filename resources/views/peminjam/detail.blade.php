@extends('layouts.peminjam')

@section('title', 'Detail Pengajuan Peminjaman | SIPRAP')
@section('breadcrumb', 'Peminjam / Detail')

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
        padding: 6px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Detail Pengajuan</h3>
        <!-- Kode format ID Peminjaman aman dari error -->
        <p class="text-secondary small m-0">
            Nomor Pengajuan: <strong class="text-dark">#PJ-{{ str_pad($detail->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/peminjam/riwayat" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold" style="font-size: 0.85rem;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @php
            $st = strtolower($detail->status_peminjaman ?? ($detail->status ?? 'menunggu'));
        @endphp
        @if($st === 'menunggu')
        <button type="button" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBatal" style="font-size: 0.85rem;">
            <i class="bi bi-x-circle me-1"></i> Batalkan Pengajuan
        </button>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <!-- KOLOM KIRI: STATUS & INFORMASI RUANGAN -->
    <div class="col-lg-8">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark m-0">Informasi Ruangan & Waktu</h6>
                <div>
                    @if($st === 'disetujui')
                        <span class="badge rounded-pill bg-info-subtle text-info px-3 py-1 fw-semibold">Disetujui</span>
                    @elseif($st === 'dipinjam')
                        <span class="badge rounded-pill bg-purple-subtle text-primary px-3 py-1 fw-semibold">Sedang Dipinjam</span>
                    @elseif($st === 'selesai' || $st === 'dikembalikan')
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1 fw-semibold">Selesai Dikembalikan</span>
                    @elseif($st === 'ditolak' || $st === 'dibatalkan')
                        <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1 fw-semibold">Ditolak / Dibatalkan</span>
                    @else
                        <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1 fw-semibold">Menunggu Persetujuan</span>
                    @endif
                </div>
            </div>

            <div class="row g-3 small">
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Laboratorium yang Diajukan</span>
                    <strong class="text-dark fs-6">{{ $detail->nama_ruangan ?? 'Laboratorium Komputer' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Nama Pemohon</span>
                    <strong class="text-dark fs-6">{{ $detail->pemohon ?? session('nama') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Waktu Mulai Praktikum</span>
                    <span class="text-dark fw-semibold">
                        {{ $detail->tanggal_mulai ?? ($detail->tanggal_peminjaman ?? $detail->created_at) }}
                    </span>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Waktu Selesai Praktikum</span>
                    <span class="text-dark fw-semibold">
                        {{ $detail->tanggal_selesai ?? '-' }}
                    </span>
                </div>
                <div class="col-12 mt-3">
                    <span class="text-secondary d-block mb-1">Keperluan / Deskripsi Praktikum</span>
                    <div class="p-3 rounded-3 bg-light text-dark">
                        {{ $detail->keperluan ?? '-' }}
                    </div>
                </div>

                @if($st === 'ditolak' && ($detail->catatan_penolakan || $detail->alasan_penolakan))
                <div class="col-12 mt-3">
                    <span class="text-danger d-block mb-1 fw-semibold"><i class="bi bi-info-circle me-1"></i> Alasan Penolakan</span>
                    <div class="p-3 rounded-3 bg-danger-subtle text-danger">
                        {{ $detail->catatan_penolakan ?? $detail->alasan_penolakan }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- DAFTAR ALAT PRAKTIKUM YANG DIMINTA -->
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Daftar Peralatan Penunjang Praktikum</h6>
            <div class="table-responsive">
                <table class="table align-middle m-0" style="font-size: 0.88rem;">
                    <thead>
                        <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                            <th>Nama Peralatan</th>
                            <th class="text-center" style="width: 140px;">Jumlah Pinjam</th>
                            <th class="text-center" style="width: 140px;">Status Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alat as $item)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $item->nama_alat ?? 'Unit Peralatan' }}</strong>
                            </td>
                            <td class="text-center fw-semibold text-dark">
                                {{ $item->jumlah ?? ($item->jumlah_alat ?? 1) }} unit
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill small">Tersedia</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-secondary small">
                                <i class="bi bi-box-seam fs-3 d-block text-muted mb-2"></i>
                                Tidak ada peralatan tambahan yang dipinjam pada sesi ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: PANDUAN PENGAMBILAN & SERAH TERIMA -->
    <div class="col-lg-4">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Ketentuan Pengambilan</h6>
            <div class="p-3 rounded-3 mb-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                <small class="text-primary d-block fw-semibold mb-1">
                    <i class="bi bi-info-circle me-1"></i> Pengambilan Kunci & Alat
                </small>
                <small class="text-secondary" style="font-size: 0.75rem;">
                    Setelah status berubah menjadi <strong>Disetujui</strong>, peminjam dapat mengambil kunci dan alat di ruang laboran 15 menit sebelum kegiatan dimulai[cite: 20].
                </small>
            </div>
            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <small class="text-dark d-block fw-semibold mb-1">
                    <i class="bi bi-clipboard-check me-1"></i> Pemeriksaan Kondisi
                </small>
                <small class="text-secondary" style="font-size: 0.75rem;">
                    Petugas laboran akan mencatat kondisi fasilitas pada saat serah-terima dan pengembalian[cite: 9].
                </small>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BATALKAN PENGAJUAN (F4-06 / F5-06) -->
<div class="modal fade" id="modalBatal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 p-3 text-center">
            <div class="text-danger mb-2"><i class="bi bi-exclamation-circle fs-1"></i></div>
            <h6 class="fw-bold text-dark mb-1">Batalkan Pengajuan?</h6>
            <p class="text-secondary small mb-3">Pengajuan peminjaman ini akan dibatalkan dari sistem laboratorium[cite: 9].</p>
            <form action="/peminjam/pengajuan/{{ $detail->id_peminjaman }}/batal" method="POST">
                @csrf
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-3 py-1 small">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop