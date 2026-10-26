@extends('layouts.peminjam')

@section('title', 'Notifikasi | SIPRAP')
@section('breadcrumb', 'Peminjam / Notifikasi')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .notif-item {
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }
    .notif-item:last-child {
        border-bottom: none;
    }
    .notif-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pusat Notifikasi</h3>
        <p class="text-secondary small m-0">Pembaruan status verifikasi, persetujuan, dan pengingat peminjaman fasilitas Anda.</p>
    </div>
</div>

<div class="card-custom">
    @forelse($notifikasi as $notif)
    <div class="notif-item">
        @if($notif->status === 'disetujui')
            <div class="notif-icon bg-success-subtle text-success">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark small">Pengajuan #PJ-{{ str_pad($notif->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Disetujui</strong>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $notif->updated_at ?? $notif->created_at }}</small>
                </div>
                <p class="text-secondary small m-0">
                    Permohonan untuk <strong>{{ $notif->nama_ruangan }}</strong> telah disetujui oleh Laboran. Silakan lakukan serah-terima kunci sesuai jadwal.
                </p>
            </div>
        @elseif($notif->status === 'ditolak')
            <div class="notif-icon bg-danger-subtle text-danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark small">Pengajuan #PJ-{{ str_pad($notif->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Ditolak</strong>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $notif->updated_at ?? $notif->created_at }}</small>
                </div>
                <p class="text-secondary small m-0">
                    Permohonan untuk <strong>{{ $notif->nama_ruangan }}</strong> ditolak. Alasan: <em>"{{ $notif->catatan_penolakan ?? 'Jadwal bertabrakan / syarat belum lengkap' }}"</em>.
                </p>
            </div>
        @elseif($notif->status === 'dipinjam')
            <div class="notif-icon bg-info-subtle text-primary">
                <i class="bi bi-key"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark small">Kunci & Fasilitas Sedang Dipinjam</strong>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $notif->updated_at ?? $notif->created_at }}</small>
                </div>
                <p class="text-secondary small m-0">
                    Fasilitas <strong>{{ $notif->nama_ruangan }}</strong> telah diserahterimakan. Harap kembalikan tepat waktu sebelum jadwal praktikum berakhir.
                </p>
            </div>
        @else
            <div class="notif-icon bg-warning-subtle text-warning">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark small">Pengajuan #PJ-{{ str_pad($notif->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Menunggu Verifikasi</strong>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $notif->created_at }}</small>
                </div>
                <p class="text-secondary small m-0">
                    Permohonan untuk <strong>{{ $notif->nama_ruangan }}</strong> sedang menunggu evaluasi petugas Laboran.
                </p>
            </div>
        @endif
    </div>
    @empty
    <div class="text-center py-5 text-secondary small">
        <i class="bi bi-bell-slash fs-2 d-block text-muted mb-2"></i>
        Belum ada notifikasi pembaruan untuk akun Anda.
    </div>
    @endforelse
</div>
@stop