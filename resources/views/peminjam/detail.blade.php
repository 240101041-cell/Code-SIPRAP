@extends('layouts.peminjam')

@section('title', 'Detail Pengajuan | SIPRAP')
@section('breadcrumb', 'Peminjam / Status Pengajuan / Detail')

@section('content')
<style>
    .card-detail-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .timeline-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Detail Pengajuan</h3>
        <p class="text-secondary small m-0">{{ $detail->no_pengajuan }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/peminjam/riwayat" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold" style="font-size: 0.85rem;">
            Kembali
        </a>
        <button type="button" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBatal" style="font-size: 0.85rem;">
            Batalkan Pengajuan
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- KOLOM KIRI: RINCIAN INFORMASI -->
    <div class="col-lg-8">
        <div class="card-detail-box mb-4">
            <h6 class="fw-bold text-dark mb-3">Informasi Peminjaman</h6>
            <div class="row g-3 small mb-3">
                <div class="col-md-3">
                    <span class="text-secondary d-block">No. Pengajuan</span>
                    <strong class="text-dark">{{ $detail->no_pengajuan }}</strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary d-block">Diajukan</span>
                    <strong class="text-dark">{{ $detail->waktu_pengajuan }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Peminjam</span>
                    <strong class="text-dark">{{ $detail->pemohon }}</strong>
                </div>
                <div class="col-md-3">
                    <span class="text-secondary d-block">Ruangan</span>
                    <strong class="text-dark">{{ $detail->ruangan }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="text-secondary d-block">Jadwal</span>
                    <strong class="text-dark">{{ $detail->jadwal }}</strong>
                </div>
                <div class="col-md-5">
                    <span class="text-secondary d-block">Keperluan</span>
                    <strong class="text-dark">{{ $detail->keperluan }}</strong>
                </div>
            </div>
            <div class="p-2 px-3 rounded-2 small text-secondary" style="background: #f8fafc; border: 1px solid #edf2f7;">
                {{ $detail->fasilitas_ruangan }}
            </div>
        </div>

        <!-- TABEL BARANG YANG DIPINJAM -->
        <div class="card-detail-box mb-4">
            <h6 class="fw-bold text-dark mb-3">Item yang Dipinjam</h6>
            <div class="table-responsive">
                <table class="table align-middle m-0 small">
                    <thead>
                        <tr class="text-secondary" style="font-size: 0.75rem; border-bottom: 2px solid #f1f5f9;">
                            <th>Item</th>
                            <th>Jumlah</th>
                            <th>Kondisi Awal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detail->alat as $item)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $item->nama }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td class="text-secondary">{{ $item->kondisi_awal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BERKAS DOKUMEN -->
        <div class="card-detail-box">
            <h6 class="fw-bold text-dark mb-3">Dokumen Pendukung</h6>
            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #2563eb;" class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <div>
                        <span class="d-block fw-bold text-dark small">{{ $detail->dokumen }}</span>
                        <small class="text-muted" style="font-size: 0.72rem;">Proposal pengajuan peminjaman laboratorium</small>
                    </div>
                </div>
                <a href="#" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                    Lihat
                </a>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: STATUS & LINIMASA -->
    <div class="col-lg-4">
        <div class="card-detail-box">
            <h6 class="fw-bold text-dark mb-3">Status Pengajuan</h6>

            <!-- Timeline Step 1: Diajukan -->
            <div class="d-flex gap-3 mb-4 position-relative">
                <div class="timeline-dot bg-success text-white"><i class="bi bi-check"></i></div>
                <div>
                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.85rem;">Diajukan</h6>
                    <small class="text-secondary" style="font-size: 0.72rem;">23 Okt 2026 · 09.10</small>
                </div>
            </div>

            <!-- Timeline Step 2: Verifikasi Laboran -->
            <div class="d-flex gap-3 mb-4">
                <div class="timeline-dot bg-warning text-dark"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.85rem;">Verifikasi Laboran</h6>
                    <small class="text-warning fw-semibold d-block" style="font-size: 0.72rem;">Menunggu keputusan</small>
                </div>
            </div>

            <!-- Timeline Step 3: Keputusan -->
            <div class="d-flex gap-3 mb-4 opacity-50">
                <div class="timeline-dot bg-light border text-secondary"><i class="bi bi-circle"></i></div>
                <div>
                    <h6 class="fw-semibold text-dark m-0" style="font-size: 0.85rem;">Keputusan</h6>
                    <small class="text-secondary" style="font-size: 0.72rem;">Disetujui / Ditolak</small>
                </div>
            </div>

            <!-- Timeline Step 4: Dipinjam -->
            <div class="d-flex gap-3 mb-4 opacity-50">
                <div class="timeline-dot bg-light border text-secondary"><i class="bi bi-circle"></i></div>
                <div>
                    <h6 class="fw-semibold text-dark m-0" style="font-size: 0.85rem;">Dipinjam</h6>
                    <small class="text-secondary" style="font-size: 0.72rem;">Serah-terima kunci & alat</small>
                </div>
            </div>

            <!-- Timeline Step 5: Dikembalikan -->
            <div class="d-flex gap-3 opacity-50">
                <div class="timeline-dot bg-light border text-secondary"><i class="bi bi-circle"></i></div>
                <div>
                    <h6 class="fw-semibold text-dark m-0" style="font-size: 0.85rem;">Dikembalikan</h6>
                    <small class="text-secondary" style="font-size: 0.72rem;">Pemeriksaan kondisi akhir</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BATALKAN PENGAJUAN (PERSIS FIGMA) -->
<div class="modal fade" id="modalBatal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content rounded-4 border-0 p-4 text-center">
            <div style="width: 54px; height: 54px; border-radius: 50%; background: #fee2e2; color: #dc2626;" class="d-flex align-items-center justify-content-center mx-auto mb-3 fs-4">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Batalkan Pengajuan {{ $detail->no_pengajuan }}?</h5>
            <p class="text-secondary small mb-4">Pengajuan hanya dapat dibatalkan selama belum diverifikasi laboran. Status akan berubah menjadi Dibatalkan.</p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-light rounded-3 px-3 py-2 fw-semibold w-50" data-bs-dismiss="modal">Tidak, kembali</button>
                <button type="button" class="btn btn-danger rounded-3 px-3 py-2 fw-semibold w-50">Ya, Batalkan</button>
            </div>
        </div>
    </div>
</div>
@stop