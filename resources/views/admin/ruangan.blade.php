@extends('layouts.admin')

@section('title', 'Data Ruangan Laboratorium | SIPRAP')
@section('breadcrumb', 'Admin / Data Ruangan')

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
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Data Ruangan Laboratorium</h3>
        <p class="text-secondary small m-0">Kelola inventaris laboratorium, daya tampung, serta ketersediaan fasilitas pendukung praktikum.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group" style="width: 260px;">
            <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama ruangan..." style="font-size: 0.85rem;">
        </div>
        <button class="btn btn-primary btn-sm px-3 rounded-3 fw-semibold" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahRuangan">
            <i class="bi bi-plus-lg me-1"></i> Tambah Ruangan
        </button>
    </div>
</div>

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 180px;">Nama Ruangan</th>
                    <th style="width: 140px;">Lokasi Gedung</th>
                    <th class="text-center" style="width: 110px;">Kapasitas</th>
                    <th>Fasilitas Penunjang</th>
                    <th class="text-center" style="width: 130px;">Status</th>
                    <th class="text-end" style="width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ruangan as $item)
                <tr>
                    <td>
                        <strong class="text-dark d-block">{{ $item->nama_ruangan }}</strong>
                        <small class="text-muted" style="font-size: 0.72rem;">ID: LAB-0{{ $item->id_ruangan }}</small>
                    </td>
                    <td class="text-secondary">{{ $item->lokasi }}</td>
                    <td class="text-center fw-semibold text-dark">{{ $item->kapasitas }} orang</td>
                    <td>
                        <span class="text-secondary small d-inline-block text-truncate" style="max-width: 320px;">
                            {{ $item->fasilitas }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($item->status === 'Tersedia')
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Tersedia</span>
                        @else
                            <span class="badge-status-pill" style="background: #fef3c7; color: #b45309;">Terpakai</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                            Edit
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH RUANGAN -->
<div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold text-dark m-0">Tambah Ruangan Laboratorium</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Laboratorium</label>
                    <input type="text" name="nama_ruangan" class="form-control rounded-3 small" placeholder="Contoh: Lab Rekayasa Perangkat Lunak" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Lokasi Gedung / Lantai</label>
                        <input type="text" name="lokasi" class="form-control rounded-3 small" placeholder="Contoh: Gedung B Lt. 3" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="form-control rounded-3 small" value="30" min="1" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Fasilitas Utama</label>
                    <textarea name="fasilitas" class="form-control rounded-3 small" rows="2" placeholder="Contoh: 30 PC Core i7, Switch Hub, Full AC, Proyektor..." required></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop