@extends('layouts.laboran')

@section('title', 'Pengembalian Fasilitas & Pemeriksaan | SIPRAP')
@section('breadcrumb', 'Laboran / Pengembalian')

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
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pengembalian Fasilitas</h3>
        <p class="text-secondary small m-0">Verifikasi pengembalian kunci serta kondisi fisik ruangan dan peralatan praktikum.</p>
    </div>
    <div class="input-group" style="width: 280px;">
        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari no. peminjaman..." style="font-size: 0.85rem;">
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 130px;">No. Pengajuan</th>
                    <th>Peminjam</th>
                    <th>Ruangan / Alat</th>
                    <th>Jadwal Selesai</th>
                    <th>Keterangan Waktu</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengembalian as $item)
                <tr>
                    <td class="fw-bold text-dark">{{ $item->no_pengajuan }}</td>
                    <td>
                        <span class="d-block fw-semibold text-dark">{{ $item->pemohon }}</span>
                        <small class="text-secondary" style="font-size: 0.72rem;">{{ $item->nim ?? 'Mahasiswa' }}</small>
                    </td>
                    <td>
                        <span class="d-block fw-semibold text-dark">{{ $item->ruangan_alat }}</span>
                    </td>
                    <td class="text-secondary">{{ $item->jadwal }}</td>
                    <td>
                        @if(str_contains($item->keterlambatan, 'Terlambat'))
                            <span class="text-danger small fw-semibold"><i class="bi bi-clock-history me-1"></i>{{ $item->keterlambatan }}</span>
                        @else
                            <span class="text-success small fw-semibold"><i class="bi bi-check-circle me-1"></i>{{ $item->keterlambatan }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">
                            Perlu Cek
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="background-color: #2563eb; font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#modalCek{{ str_replace('-', '', $item->no_pengajuan) }}">
                            Periksa
                        </button>
                    </td>
                </tr>

                <!-- MODAL PEMERIKSAAN PENGEMBALIAN -->
                <div class="modal fade" id="modalCek{{ str_replace('-', '', $item->no_pengajuan) }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                                <div>
                                    <h5 class="fw-bold text-dark m-0">Pemeriksaan Pengembalian Fasilitas</h5>
                                    <small class="text-secondary">{{ $item->no_pengajuan }} · {{ $item->pemohon }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="/laboran/pengembalian/{{ $item->no_pengajuan }}/selesai" method="POST">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <span class="text-secondary small d-block">Ruangan / Jadwal</span>
                                            <strong class="text-dark small d-block">{{ $item->ruangan_alat }}</strong>
                                            <small class="text-muted">{{ $item->jadwal }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                                            <span class="text-danger small d-block fw-semibold">Status Waktu Pengembalian</span>
                                            <strong class="text-danger small d-block">{{ $item->keterlambatan }}</strong>
                                            <small class="text-muted">Batas waktu terlewati</small>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark small mb-2">Checklist Kondisi Fisik Fasilitas</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm align-middle small mb-0">
                                        <thead>
                                            <tr class="text-secondary" style="border-bottom: 2px solid #f1f5f9;">
                                                <th>Item</th>
                                                <th>Status Pengembalian</th>
                                                <th>Kondisi Akhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold text-dark">Kunci Ruangan Laboratorium</td>
                                                <td>
                                                    <select name="kondisi_kunci" class="form-select form-select-sm" style="width: 180px;">
                                                        <option value="kembali" selected>Sudah Dikembalikan</option>
                                                        <option value="hilang">Hilang</option>
                                                    </select>
                                                </td>
                                                <td><span class="text-success small"><i class="bi bi-check-circle me-1"></i>Lengkap</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-dark">Peralatan Praktikum</td>
                                                <td>
                                                    <select name="kondisi_alat" class="form-select form-select-sm" style="width: 180px;">
                                                        <option value="lengkap_baik" selected>Lengkap & Berfungsi</option>
                                                        <option value="rusak">Ada Kerusakan</option>
                                                        <option value="hilang">Ada Yang Hilang</option>
                                                    </select>
                                                </td>
                                                <td><span class="text-success small"><i class="bi bi-check-circle me-1"></i>Tidak ada cacat</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-dark">Kebersihan & Kerapihan Ruangan</td>
                                                <td>
                                                    <select name="kebersihan" class="form-select form-select-sm" style="width: 180px;">
                                                        <option value="bersih" selected>Bersih & Rapi</option>
                                                        <option value="kotor">Kotor / Berantakan</option>
                                                    </select>
                                                </td>
                                                <td><span class="text-muted small">Kursi & AC dimatikan</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-dark mb-1">Catatan Pemeriksaan Laboran</label>
                                    <textarea name="catatan_kembali" class="form-control rounded-3 small" rows="2" placeholder="Catatan kondisi fasilitas...">Semua unit alat dicek berfungsi normal, ruangan bersih, kunci telah diterima kembali oleh laboran.</textarea>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 fw-semibold small" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 fw-semibold small">
                                        <i class="bi bi-check2-all me-1"></i> Selesaikan Pengembalian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop