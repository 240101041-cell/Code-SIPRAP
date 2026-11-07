@extends('layouts.peminjam')

@section('title', 'Cek Ketersediaan Fasilitas | SIPRAP')
@section('breadcrumb', 'Peminjam / Cek Ketersediaan')

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
        width: 260px;
    }
    .btn-action-ajukan {
        height: 32px;
        padding: 0 14px;
        font-size: 0.82rem;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Cek Ketersediaan Fasilitas</h3>
        <p class="text-secondary small m-0">Informasi ketersediaan ruangan laboratorium dan peralatan praktikum terkini.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
            <input type="text" id="searchInput" class="form-control search-input-box" placeholder="Cari lab / nama alat...">
        </div>
        <a href="/peminjam/pengajuan" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #2563eb;">
            <i class="bi bi-plus-lg"></i> Ajukan Peminjaman
        </a>
    </div>
</div>

<!-- 1. KATALOG RUANGAN LABORATORIUM -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Katalog Ruangan Laboratorium</h6>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Total: {{ $ruangan->count() }} Ruangan</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="ruanganTable">
            <thead>
                <tr class="text-secondary" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 200px;">Nama Laboratorium</th>
                    <th style="width: 140px;">Lokasi</th>
                    <th class="text-center" style="width: 110px;">Kapasitas</th>
                    <th>Fasilitas Penunjang</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                    <th class="text-end" style="width: 110px; padding-right: 12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangan as $r)
                @php
                    $st = strtolower($r->status ?? ($r->status_ruangan ?? 'tersedia'));
                @endphp
                <tr class="ruangan-row" style="border-bottom: 1px solid #f8fafc;">
                    <td><strong class="text-dark item-nama">{{ $r->nama_ruangan }}</strong></td>
                    <td class="text-secondary">{{ $r->lokasi ?? '-' }}</td>
                    <td class="text-center fw-semibold text-dark">{{ $r->kapasitas }} orang</td>
                    <td>
                        <span class="text-secondary small d-inline-block text-truncate" style="max-width: 360px;">
                            {{ $r->fasilitas ?? 'Fasilitas praktikum reguler' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($st === 'tersedia' || $st === 'aktif')
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Tersedia</span>
                        @else
                            <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">Terpakai</span>
                        @endif
                    </td>
                    <td class="text-end" style="padding-right: 12px;">
                        @if($st === 'tersedia' || $st === 'aktif')
                            <!-- Membawa parameter id ruangan langsung ke formulir pengajuan -->
                            <a href="/peminjam/pengajuan?ruangan={{ $r->id_ruangan }}" class="btn btn-primary btn-action-ajukan" style="background-color: #2563eb;">
                                Ajukan
                            </a>
                        @else
                            <button class="btn btn-secondary btn-action-ajukan disabled opacity-50" disabled>Penuh</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-secondary small">Belum ada data ruangan laboratorium.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 2. KATALOG ALAT PRAKTIKUM TERSEDIA -->
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Katalog Alat Praktikum Tersedia</h6>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">Total: {{ $alat->count() }} Jenis Alat</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="alatTable">
            <thead>
                <tr class="text-secondary" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9;">
                    <th>Nama Alat</th>
                    <th class="text-center" style="width: 140px;">Sisa Stok Unit</th>
                    <th class="text-center" style="width: 140px;">Kondisi Fisik</th>
                    <th class="text-end" style="width: 110px; padding-right: 12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alat as $a)
                @php
                    $stok = $a->stok ?? ($a->stok_alat ?? ($a->jumlah ?? 0));
                @endphp
                <tr class="alat-row" style="border-bottom: 1px solid #f8fafc;">
                    <td><strong class="text-dark item-nama">{{ $a->nama_alat }}</strong></td>
                    <td class="text-center fw-bold text-primary">{{ $stok }} unit</td>
                    <td class="text-center">
                        <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">
                            {{ ucfirst($a->kondisi ?? 'Baik') }}
                        </span>
                    </td>
                    <td class="text-end" style="padding-right: 12px;">
                        @if($stok > 0)
                            <!-- Tombol ajukan langsung memilih alat yang bersangkutan -->
                            <a href="/peminjam/pengajuan?alat={{ $a->id_alat }}" class="btn btn-outline-primary btn-action-ajukan">
                                Pinjam
                            </a>
                        @else
                            <button class="btn btn-secondary btn-action-ajukan disabled opacity-50" disabled>Habis</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-secondary small">Belum ada data alat praktikum.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Live filter pencarian instan pada kedua tabel (F2-02)
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        
        document.querySelectorAll('#ruanganTable tbody tr.ruangan-row').forEach(row => {
            const nama = row.querySelector('.item-nama')?.textContent.toLowerCase() || '';
            row.style.display = nama.includes(filter) ? '' : 'none';
        });

        document.querySelectorAll('#alatTable tbody tr.alat-row').forEach(row => {
            const nama = row.querySelector('.item-nama')?.textContent.toLowerCase() || '';
            row.style.display = nama.includes(filter) ? '' : 'none';
        });
    });
</script>
@stop