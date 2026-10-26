@extends('layouts.laboran')

@section('title', 'Data Peralatan | SIPRAP')
@section('breadcrumb', 'Laboran / Data Peralatan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Inventaris Alat Praktikum</h3>
        <p class="text-secondary small m-0">Data ketersediaan dan stok alat laboratorium praktikum.</p>
    </div>
    <button class="btn btn-primary btn-sm px-3 rounded-3 fw-semibold" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahAlat">
        <i class="bi bi-plus-lg me-1"></i> Tambah Alat
    </button>
</div>

@if(session('success'))
<div class="alert alert-success rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 100px;">ID Alat</th>
                    <th>Nama Alat Praktikum</th>
                    <th class="text-center" style="width: 140px;">Stok Unit</th>
                    <th class="text-center" style="width: 140px;">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $item)
                <tr>
                    <td class="fw-bold text-dark">#ALT-{{ $item->id_alat }}</td>
                    <td class="fw-semibold text-dark">{{ $item->nama_alat }}</td>
                    <td class="text-center fw-bold text-primary">{{ $item->stok }} unit</td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">{{ ucfirst($item->kondisi ?? 'Baik') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-secondary small">
                        <i class="bi bi-box-seam fs-2 d-block text-muted mb-2"></i>
                        Belum ada data peralatan praktikum di database. Silakan klik "Tambah Alat".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH ALAT -->
<div class="modal fade" id="modalTambahAlat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Tambah Alat Praktikum Baru</h6>
            <form action="/laboran/peralatan/tambah" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Alat</label>
                    <input type="text" name="nama_alat" class="form-control rounded-3 small" placeholder="Contoh: Router Cisco 2911" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Jumlah Stok</label>
                        <input type="number" name="stok" class="form-control rounded-3 small" value="1" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Kondisi</label>
                        <select name="kondisi" class="form-select rounded-3 small">
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan ke Database</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop