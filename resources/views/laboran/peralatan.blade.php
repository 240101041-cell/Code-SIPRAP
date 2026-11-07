@extends('layouts.laboran')

@section('title', 'Inventaris Peralatan | SIPRAP')
@section('breadcrumb', 'Laboran / Data Peralatan')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Inventaris Alat Praktikum</h3>
        <p class="text-secondary small m-0">Data ketersediaan, stok, dan kondisi fisik alat laboratorium.</p>
    </div>
    <button class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahAlat">
        <i class="bi bi-plus-lg me-1"></i> Tambah Alat
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 110px;">ID Alat</th>
                    <th>Nama Alat Praktikum</th>
                    <th class="text-center" style="width: 140px;">Stok Unit</th>
                    <th class="text-center" style="width: 140px;">Kondisi</th>
                    <th class="text-end" style="width: 120px; padding-right: 12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $item)
                @php
                    $stok = $item->stok ?? ($item->stok_alat ?? 0);
                    $kondisi = strtolower($item->kondisi ?? 'baik');
                @endphp
                <tr>
                    <td class="fw-bold text-dark">#ALT-{{ str_pad($item->id_alat, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-semibold text-dark">{{ $item->nama_alat }}</td>
                    <td class="text-center fw-bold text-primary">{{ $stok }} unit</td>
                    <td class="text-center">
                        @if($kondisi === 'baik')
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">Baik</span>
                        @else
                            <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1">Perlu Cek / Rusak</span>
                        @endif
                    </td>
                    <td class="text-end" style="padding-right: 12px;">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#modalEditAlat{{ $item->id_alat }}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                    </td>
                </tr>

                <!-- MODAL EDIT ALAT -->
                <div class="modal fade text-start" id="modalEditAlat{{ $item->id_alat }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <h6 class="fw-bold text-dark m-0">Edit Peralatan Praktikum</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/laboran/peralatan/{{ $item->id_alat }}/update" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">Nama Alat</label>
                                    <input type="text" name="nama_alat" class="form-control rounded-3 small" value="{{ $item->nama_alat }}" required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-dark mb-1">Stok Unit</label>
                                        <input type="number" name="stok" class="form-control rounded-3 small" value="{{ $stok }}" min="0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-dark mb-1">Kondisi Fisik</label>
                                        <select name="kondisi" class="form-select rounded-3 small">
                                            <option value="baik" {{ $kondisi === 'baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="rusak" {{ $kondisi !== 'baik' ? 'selected' : '' }}>Rusak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-secondary small">
                        <i class="bi bi-box-seam fs-2 d-block text-muted mb-2"></i>
                        Belum ada data peralatan praktikum di database.
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