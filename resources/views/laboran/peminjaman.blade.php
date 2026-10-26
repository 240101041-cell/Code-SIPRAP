@extends('layouts.laboran')

@section('title', 'Status Peminjaman & Serah-Terima | SIPRAP')
@section('breadcrumb', 'Laboran / Status Peminjaman')

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
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Status Peminjaman</h3>
        <p class="text-secondary small m-0">Pantau peminjaman aktif dan lakukan serah-terima kunci/alat kepada peminjam.</p>
    </div>
    <div class="input-group" style="width: 280px;">
        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari no. pengajuan / peminjam..." style="font-size: 0.85rem;">
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- FILTER TABS FIGMA -->
<div class="d-flex gap-1 mb-3">
    <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold">Disetujui</button>
    <button class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary">Dipinjam</button>
    <button class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary">Semua</button>
</div>

<!-- TABEL STATUS PEMINJAMAN FIGMA -->
<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 130px;">No. Pengajuan</th>
                    <th>Peminjam</th>
                    <th>Ruangan / Alat</th>
                    <th>Jadwal</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peminjaman as $item)
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
                    <td class="text-center">
                        @if($item->status === 'Disetujui')
                            <span class="badge-status-pill" style="background: #e0f2fe; color: #0369a1;">Disetujui</span>
                        @elseif($item->status === 'Dipinjam')
                            <span class="badge-status-pill" style="background: #f3e8ff; color: #7e22ce;">Dipinjam</span>
                        @else
                            <span class="badge-status-pill" style="background: #f1f5f9; color: #475569;">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($item->status === 'Disetujui')
                            <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="background-color: #2563eb; font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#modalSerahTerima{{ str_replace('-', '', $item->no_pengajuan) }}">
                                Serah-Terima
                            </button>
                        @else
                            <span class="text-secondary small fw-medium">Dipinjam</span>
                        @endif
                    </td>
                </tr>

                @if($item->status === 'Disetujui')
                <!-- MODAL SERAH TERIMA PERSIS FIGMA -->
                <div class="modal fade" id="modalSerahTerima{{ str_replace('-', '', $item->no_pengajuan) }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                                <div>
                                    <h5 class="fw-bold text-dark m-0">Serah-Terima Kunci & Alat Praktikum</h5>
                                    <small class="text-secondary">{{ $item->no_pengajuan }} · {{ $item->pemohon }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="/laboran/peminjaman/{{ $item->no_pengajuan }}/serah-terima" method="POST">
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
                                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <span class="text-secondary small d-block">Petugas Laboran Penyerah</span>
                                            <strong class="text-dark small d-block">{{ session('nama', 'Rina Wulandari') }}</strong>
                                            <small class="text-muted">Laboratorium Komputer UDB</small>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark small mb-2">Pemeriksaan Kondisi Awal Saat Diserahkan</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm align-middle small mb-0">
                                        <thead>
                                            <tr class="text-secondary" style="border-bottom: 2px solid #f1f5f9;">
                                                <th>Item</th>
                                                <th>Jumlah</th>
                                                <th>Kondisi Fisik</th>
                                                <th>Catatan Awal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold text-dark">Kunci Ruangan Lab</td>
                                                <td>1 buah</td>
                                                <td><span class="badge bg-success-subtle text-success">Lengkap</span></td>
                                                <td class="text-muted">Kunci pintu utama & master switch</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-dark">Peralatan Praktikum</td>
                                                <td>Sesuai pengajuan</td>
                                                <td><span class="badge bg-success-subtle text-success">Berfungsi</span></td>
                                                <td class="text-muted">Unit utuh dan kabel adaptor disertakan</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">Catatan Tambahan Saat Penyerahan</label>
                                    <input type="text" name="catatan" class="form-control rounded-3 small" placeholder="Contoh: Diserahkan langsung ke mahasiswa bersangkutan, kartu identitas dititipkan." value="Peminjam memeriksa dan menyetujui kondisi awal fasilitas.">
                                </div>

                                <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <small class="text-primary fw-medium">Setelah tombol konfirmasi ditekan, status berubah menjadi <strong>Dipinjam</strong> dan waktu mulai peminjaman otomatis tercatat.</small>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 fw-semibold small" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold small" style="background-color: #2563eb;">
                                        <i class="bi bi-check2-circle me-1"></i> Simpan & Ubah ke Dipinjam
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop