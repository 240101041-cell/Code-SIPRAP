@extends('layouts.laboran')

@section('title', 'Jadwal Pemakaian Laboratorium | SIPRAP')
@section('breadcrumb', 'Laboran / Jadwal')

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
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Jadwal Pemakaian Laboratorium</h3>
        <p class="text-secondary small m-0">Agenda peminjaman laboratorium yang telah disetujui dan sedang berlangsung.</p>
    </div>
</div>

<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Daftar Jadwal Penggunaan Lab</h6>
        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-1 rounded-pill small">
            Total: {{ $jadwal->count() }} Sesi Terjadwal
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 220px;">Waktu & Tanggal</th>
                    <th>Ruangan Lab</th>
                    <th>Kegiatan / Kelas / Keperluan</th>
                    <th>Pengguna / Pemohon</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                <tr>
                    <td class="fw-bold text-dark">
                        <i class="bi bi-clock me-1 text-primary"></i>
                        {{ $j->tanggal_mulai ?? $j->tanggal_pinjam ?? '-' }}
                        <small class="text-secondary d-block fw-normal">s/d {{ $j->tanggal_selesai ?? $j->tanggal_kembali ?? '-' }}</small>
                    </td>
                    <td class="fw-semibold text-dark">{{ $j->nama_ruangan ?? 'Laboratorium' }}</td>
                    <td>{{ $j->keperluan ?? '-' }}</td>
                    <td class="text-secondary">{{ $j->pemohon ?? 'Civitas Akademika' }}</td>
                    <td class="text-center">
                        @php
                            $statusVal = strtolower($j->status_peminjaman ?? $j->status ?? '');
                        @endphp
                        @if($statusVal === 'dipinjam')
                            <span class="badge-status-pill" style="background: #f3e8ff; color: #7e22ce;">Sedang Berlangsung</span>
                        @elseif($statusVal === 'disetujui')
                            <span class="badge-status-pill" style="background: #e0f2fe; color: #0369a1;">Terjadwal</span>
                        @else
                            <span class="badge-status-pill" style="background: #f1f5f9; color: #475569;">{{ ucfirst($statusVal) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-secondary small">
                        <i class="bi bi-calendar-x fs-2 d-block text-muted mb-2"></i>
                        Belum ada jadwal praktikum atau peminjaman aktif yang disetujui.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop