@extends('layouts.laboran')

@section('title', 'Pengembalian & Riwayat Fasilitas | SIPRAP')
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
    .btn-tab-pilihan {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-tab-pilihan.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pengembalian Fasilitas & Riwayat</h3>
        <p class="text-secondary small m-0">Verifikasi fisik pengembalian dan pemantauan arsip transaksi peminjaman selesai.</p>
    </div>
    <div class="input-group" style="width: 280px;">
        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
        <input type="text" id="searchKembali" class="form-control border-start-0 ps-0" placeholder="Cari ID / peminjam..." style="font-size: 0.85rem;">
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- TAB SWITCHER: AKTIF VS RIWAYAT SELESAI -->
<div class="d-flex gap-2 mb-3">
    <a href="/laboran/pengembalian?tab=aktif" class="btn-tab-pilihan {{ ($tab ?? 'aktif') === 'aktif' ? 'active' : '' }}">
        <i class="bi bi-clock-history me-1"></i> Perlu Diperiksa (Aktif)
    </a>
    <a href="/laboran/pengembalian?tab=riwayat" class="btn-tab-pilihan {{ ($tab ?? '') === 'riwayat' ? 'active' : '' }}">
        <i class="bi bi-archive me-1"></i> Riwayat Selesai (Arsip)
    </a>
</div>

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="tableKembali">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 130px;">ID Pinjam</th>
                    <th>Peminjam</th>
                    <th>Ruangan / Fasilitas</th>
                    <th>Waktu Pengembalian</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengembalian as $item)
                @php
                    $idPinjam = $item->id_peminjaman;
                    $namaPemohon = $item->pemohon ?? ($item->peminjam ?? '-');
                    $ruangAlat = $item->nama_ruangan ?? 'Laboratorium';
                    $waktuSelesai = $item->tanggal_selesai ?? ($item->updated_at ?? '-');
                    $st = strtolower($item->status_peminjaman ?? ($item->status ?? ''));
                @endphp
                <tr class="kembali-row">
                    <td class="fw-bold text-dark row-id">#PJ-{{ str_pad($idPinjam, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <span class="d-block fw-semibold text-dark row-name">{{ $namaPemohon }}</span>
                        <small class="text-secondary" style="font-size: 0.72rem;">{{ $item->keperluan ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="d-block fw-semibold text-dark">{{ $ruangAlat }}</span>
                    </td>
                    <td class="text-secondary small">{{ $waktuSelesai }}</td>
                    <td class="text-center">
                        @if($st === 'selesai' || $st === 'dikembalikan')
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">
                                <i class="bi bi-check2-circle me-1"></i>Selesai
                            </span>
                        @else
                            <span class="badge-status-pill" style="background: #fef3c7; color: #b45309;">
                                Perlu Cek
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($st === 'dipinjam')
                            <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="background-color: #2563eb; font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#modalCek{{ $idPinjam }}">
                                Periksa
                            </button>
                        @else
                            <span class="text-muted small fw-medium">Tersimpan</span>
                        @endif
                    </td>
                </tr>

                @if($st === 'dipinjam')
                <!-- MODAL PEMERIKSAAN PENGEMBALIAN -->
                <div class="modal fade text-start" id="modalCek{{ $idPinjam }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                                <div>
                                    <h5 class="fw-bold text-dark m-0">Pemeriksaan Pengembalian Fasilitas</h5>
                                    <small class="text-secondary">#PJ-{{ str_pad($idPinjam, 4, '0', STR_PAD_LEFT) }} · {{ $namaPemohon }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="/laboran/pengembalian/{{ $idPinjam }}/selesai" method="POST">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <span class="text-secondary small d-block">Ruangan / Fasilitas</span>
                                            <strong class="text-dark small d-block">{{ $ruangAlat }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <span class="text-secondary small d-block">Petugas Pemeriksa</span>
                                            <strong class="text-dark small d-block">{{ session('nama', 'Laboran') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-dark mb-1">Catatan Kondisi Akhir Pengembalian</label>
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
                @endif
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary small">
                        <i class="bi bi-inbox fs-2 d-block text-muted mb-2"></i>
                        {{ ($tab ?? 'aktif') === 'riwayat' ? 'Belum ada riwayat pengembalian fasilitas yang selesai.' : 'Tidak ada antrean pengembalian fasilitas yang sedang aktif.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('searchKembali').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#tableKembali tbody tr.kembali-row').forEach(row => {
            const id = row.querySelector('.row-id')?.textContent.toLowerCase() || '';
            const name = row.querySelector('.row-name')?.textContent.toLowerCase() || '';
            row.style.display = (id.includes(filter) || name.includes(filter)) ? '' : 'none';
        });
    });
</script>
@stop