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

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Status Peminjaman</h3>
        <p class="text-secondary small m-0">Pantau peminjaman aktif dan lakukan serah-terima kunci/alat kepada peminjam.</p>
    </div>
    <div class="input-group" style="width: 280px;">
        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
        <input type="text" id="searchPinjam" class="form-control border-start-0 ps-0" placeholder="Cari ID / peminjam..." style="font-size: 0.85rem;">
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
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="tablePinjam">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 130px;">ID Pinjam</th>
                    <th>Peminjam</th>
                    <th>Ruangan / Alat</th>
                    <th>Jadwal Praktikum</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                    <th class="text-end" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $item)
                @php
                    $idPinjam = $item->id_peminjaman;
                    $st = strtolower($item->status_peminjaman ?? ($item->status ?? ''));
                    $namaPemohon = $item->pemohon ?? ($item->peminjam ?? '-');
                    $ruangAlat = $item->nama_ruangan ?? ($item->ruangan_alat ?? 'Laboratorium');
                    $jadwalStr = $item->tanggal_mulai ? ($item->tanggal_mulai . ' s/d ' . $item->tanggal_selesai) : ($item->jadwal ?? '-');
                @endphp
                <tr class="pinjam-row">
                    <td class="fw-bold text-dark row-id">#PJ-{{ str_pad($idPinjam, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <span class="d-block fw-semibold text-dark row-name">{{ $namaPemohon }}</span>
                        <small class="text-secondary" style="font-size: 0.72rem;">User ID: #{{ $item->id_user ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="d-block fw-semibold text-dark">{{ $ruangAlat }}</span>
                    </td>
                    <td class="text-secondary small">{{ $jadwalStr }}</td>
                    <td class="text-center">
                        @if($st === 'disetujui')
                            <span class="badge-status-pill" style="background: #e0f2fe; color: #0369a1;">Disetujui</span>
                        @elseif($st === 'dipinjam')
                            <span class="badge-status-pill" style="background: #f3e8ff; color: #7e22ce;">Dipinjam</span>
                        @else
                            <span class="badge-status-pill" style="background: #f1f5f9; color: #475569;">{{ ucfirst($st) }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($st === 'disetujui')
                            <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold" style="background-color: #2563eb; font-size: 0.82rem;" data-bs-toggle="modal" data-bs-target="#modalSerahTerima{{ $idPinjam }}">
                                Serah-Terima
                            </button>
                        @else
                            <span class="text-secondary small fw-medium">Sedang Dipinjam</span>
                        @endif
                    </td>
                </tr>

                @if($st === 'disetujui')
                <!-- MODAL SERAH TERIMA DINAMIS -->
                <div class="modal fade text-start" id="modalSerahTerima{{ $idPinjam }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                                <div>
                                    <h5 class="fw-bold text-dark m-0">Serah-Terima Kunci & Alat Praktikum</h5>
                                    <small class="text-secondary">#PJ-{{ str_pad($idPinjam, 4, '0', STR_PAD_LEFT) }} · {{ $namaPemohon }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="/laboran/peminjaman/{{ $idPinjam }}/serah-terima" method="POST">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <span class="text-secondary small d-block">Ruangan / Fasilitas</span>
                                            <strong class="text-dark small d-block">{{ $ruangAlat }}</strong>
                                            <small class="text-muted">{{ $jadwalStr }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <span class="text-secondary small d-block">Petugas Laboran Penyerah</span>
                                            <strong class="text-dark small d-block">{{ session('nama', 'Laboran') }}</strong>
                                            <small class="text-muted">Laboratorium UDB</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">Catatan Tambahan Saat Penyerahan</label>
                                    <input type="text" name="catatan" class="form-control rounded-3 small" placeholder="Contoh: Diserahkan langsung ke mahasiswa, kondisi alat lengkap dan baik." value="Peminjam memeriksa dan menyetujui kondisi awal fasilitas.">
                                </div>

                                <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <small class="text-primary fw-medium">Setelah tombol konfirmasi ditekan, status berubah menjadi <strong>Dipinjam</strong>.</small>
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
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary small">
                        Tidak ada data peminjaman yang sedang aktif.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('searchPinjam').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#tablePinjam tbody tr.pinjam-row').forEach(row => {
            const id = row.querySelector('.row-id')?.textContent.toLowerCase() || '';
            const name = row.querySelector('.row-name')?.textContent.toLowerCase() || '';
            row.style.display = (id.includes(filter) || name.includes(filter)) ? '' : 'none';
        });
    });
</script>
@stop