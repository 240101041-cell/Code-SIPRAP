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
        padding: 5px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-block;
    }
    .search-input-box {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 7px 14px 7px 36px;
        font-size: 0.88rem;
        width: 280px;
    }
    .btn-action-custom {
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0 12px;
        transition: all 0.2s ease;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Data Ruangan Laboratorium</h3>
        <p class="text-secondary small m-0">Kelola inventaris laboratorium, daya tampung, serta ketersediaan fasilitas pendukung praktikum.</p>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <!-- PENCARIAN RUANGAN -->
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
            <input type="text" id="searchInput" class="form-control search-input-box" placeholder="Cari nama ruangan..." value="{{ $search ?? '' }}">
        </div>

        <!-- TOMBOL MODAL TAMBAH -->
        <button class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahRuangan">
            <i class="bi bi-plus-lg"></i> Tambah Ruangan
        </button>
    </div>
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
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="ruanganTable">
            <thead>
                <tr class="text-secondary" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 220px; padding: 12px 16px;">Nama Ruangan</th>
                    <th style="width: 160px;">Lokasi Gedung</th>
                    <th class="text-center" style="width: 120px;">Kapasitas</th>
                    <th>Fasilitas Penunjang</th>
                    <th class="text-center" style="width: 130px;">Status</th>
                    <th class="text-center" style="width: 130px; padding-right: 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangan as $item)
                <tr class="ruangan-row" style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 14px 16px;">
                        <strong class="text-dark d-block nama-val">{{ $item->nama_ruangan }}</strong>
                        <small class="text-muted" style="font-size: 0.72rem;">ID: {{ $item->kode_ruangan ?? ('LAB-0' . $item->id_ruangan) }}</small>
                    </td>
                    <td class="text-secondary lokasi-val">{{ $item->lokasi ?? '-' }}</td>
                    <td class="text-center fw-semibold text-dark">{{ $item->kapasitas }} orang</td>
                    <td>
                        <span class="text-secondary small d-inline-block text-truncate" style="max-width: 320px;">
                            {{ $item->fasilitas ?? 'Fasilitas standar lab' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php
                            $st = strtolower($item->status ?? ($item->status_ruangan ?? 'tersedia'));
                        @endphp
                        @if($st === 'tersedia' || $st === 'aktif')
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Tersedia</span>
                        @else
                            <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">Terpakai</span>
                        @endif
                    </td>
                    <td class="text-center" style="padding-right: 16px;">
                        <div class="d-inline-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-action-custom" data-bs-toggle="modal" data-bs-target="#modalEditRuangan{{ $item->id_ruangan }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                        </div>

                        <!-- MODAL EDIT RUANGAN -->
                        <div class="modal fade text-start" id="modalEditRuangan{{ $item->id_ruangan }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold text-dark m-0">Edit Data Laboratorium</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="/admin/ruangan/{{ $item->id_ruangan }}/update" method="POST">
                                        @csrf
                                        <div class="row g-2 mb-2">
                                            <div class="col-8">
                                                <label class="form-label small fw-bold text-dark mb-1">Nama Laboratorium</label>
                                                <input type="text" name="nama_ruangan" class="form-control rounded-3 small" value="{{ $item->nama_ruangan }}" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold text-dark mb-1">Kode Ruangan</label>
                                                <input type="text" name="kode_ruangan" class="form-control rounded-3 small" value="{{ $item->kode_ruangan ?? ('LAB-0' . $item->id_ruangan) }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-dark mb-1">Lokasi Gedung / Lt</label>
                                                <input type="text" name="lokasi" class="form-control rounded-3 small" value="{{ $item->lokasi ?? 'Gedung B' }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-dark mb-1">Kapasitas (Orang)</label>
                                                <input type="number" name="kapasitas" class="form-control rounded-3 small" value="{{ $item->kapasitas }}" min="1" required>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold text-dark mb-1">Status Ketersediaan</label>
                                            <select name="status" class="form-select rounded-3 small">
                                                <option value="tersedia" {{ $st === 'tersedia' || $st === 'aktif' ? 'selected' : '' }}>Tersedia</option>
                                                <option value="terpakai" {{ $st === 'terpakai' ? 'selected' : '' }}>Terpakai</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Fasilitas Penunjang</label>
                                            <textarea name="fasilitas" class="form-control rounded-3 small" rows="2">{{ $item->fasilitas }}</textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary small">
                        <i class="bi bi-door-closed fs-2 d-block text-muted mb-2"></i>
                        Belum ada data ruangan laboratorium ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH RUANGAN -->
<div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold text-dark m-0">Tambah Laboratorium Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/ruangan/tambah" method="POST">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-8">
                        <label class="form-label small fw-bold text-dark mb-1">Nama Laboratorium</label>
                        <input type="text" name="nama_ruangan" class="form-control rounded-3 small" placeholder="Contoh: Lab Jaringan 2" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold text-dark mb-1">Kode Ruangan</label>
                        <input type="text" name="kode_ruangan" class="form-control rounded-3 small" placeholder="LAB-06">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Lokasi Gedung / Lt</label>
                        <input type="text" name="lokasi" class="form-control rounded-3 small" placeholder="Gedung B Lt. 3">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="form-control rounded-3 small" value="30" min="1" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Fasilitas Penunjang</label>
                    <textarea name="fasilitas" class="form-control rounded-3 small" rows="2" placeholder="Contoh: 30 PC Core i7, Switch Hub, AC, Proyektor..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan ke Database</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JAVASCRIPT LIVE FILTER -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ruanganTable tbody tr.ruangan-row');

        rows.forEach(row => {
            const nama = row.querySelector('.nama-val')?.textContent.toLowerCase() || '';
            const lokasi = row.querySelector('.lokasi-val')?.textContent.toLowerCase() || '';

            if (nama.includes(filter) || lokasi.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@stop