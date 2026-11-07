@extends('layouts.peminjam')

@section('title', 'Pusat Notifikasi | SIPRAP')
@section('breadcrumb', 'Peminjam / Notifikasi')

@section('content')
<style>
    .card-notif {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .card-notif:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .icon-notif-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
</style>

<div class="mb-4">
    <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pusat Notifikasi</h3>
    <p class="text-secondary small m-0">Pembaruan status verifikasi, persetujuan, dan pengingat peminjaman fasilitas Anda.</p>
</div>

<div class="d-flex flex-column gap-3">
    @forelse($notifikasi as $n)
        @php
            $st = strtolower($n->status_peminjaman ?? ($n->status ?? 'menunggu'));
            $waktu = \Carbon\Carbon::parse($n->updated_at ?? $n->created_at)->translatedFormat('d M Y, H:i') . ' WIB';
        @endphp

        <a href="/peminjam/detail/{{ $n->id_peminjaman }}" class="card-notif">
            <div class="d-flex align-items-center gap-3">
                @if($st === 'disetujui')
                    <div class="icon-notif-circle" style="background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0 mb-1">Pengajuan #PJ-{{ str_pad($n->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Telah Disetujui</h6>
                        <p class="text-secondary small m-0">Permohonan untuk <strong>{{ $n->nama_ruangan ?? 'Laboratorium' }}</strong> siap digunakan. Silakan ambil kunci ke Laboran.</p>
                    </div>
                @elseif($st === 'dipinjam')
                    <div class="icon-notif-circle" style="background: #f3e8ff; color: #7e22ce;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0 mb-1">Fasilitas #PJ-{{ str_pad($n->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Sedang Digunakan</h6>
                        <p class="text-secondary small m-0">Kunci/alat untuk <strong>{{ $n->nama_ruangan ?? 'Laboratorium' }}</strong> telah diserahterimakan oleh petugas.</p>
                    </div>
                @elseif($st === 'ditolak')
                    <div class="icon-notif-circle" style="background: #fee2e2; color: #dc2626;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0 mb-1">Pengajuan #PJ-{{ str_pad($n->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Ditolak / Dibatalkan</h6>
                        <p class="text-secondary small m-0">Pengajuan untuk <strong>{{ $n->nama_ruangan ?? 'Laboratorium' }}</strong> tidak dapat diproses.</p>
                    </div>
                @elseif($st === 'selesai' || $st === 'dikembalikan')
                    <div class="icon-notif-circle" style="background: #dcfce7; color: #16a34a;">
                        <i class="bi bi-check-all"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0 mb-1">Peminjaman #PJ-{{ str_pad($n->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Telah Selesai</h6>
                        <p class="text-secondary small m-0">Fasilitas <strong>{{ $n->nama_ruangan ?? 'Laboratorium' }}</strong> telah dikembalikan dengan tertib.</p>
                    </div>
                @else
                    <div class="icon-notif-circle" style="background: #fef3c7; color: #b45309;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0 mb-1">Pengajuan #PJ-{{ str_pad($n->id_peminjaman, 4, '0', STR_PAD_LEFT) }} Menunggu Verifikasi</h6>
                        <p class="text-secondary small m-0">Permohonan untuk <strong>{{ $n->nama_ruangan ?? 'Laboratorium' }}</strong> sedang menunggu evaluasi petugas Laboran.</p>
                    </div>
                @endif
            </div>

            <div class="text-end flex-shrink-0">
                <small class="text-secondary d-block" style="font-size: 0.78rem;">{{ $waktu }}</small>
                <span class="text-primary small fw-semibold">Lihat Detail <i class="bi bi-chevron-right"></i></span>
            </div>
        </a>
    @empty
        <div class="card p-5 text-center border-0 rounded-4 shadow-sm">
            <i class="bi bi-bell-slash fs-1 text-muted mb-2"></i>
            <h6 class="fw-bold text-dark">Belum Ada Notifikasi</h6>
            <p class="text-secondary small m-0">Semua aktivitas dan respon pengajuan Anda akan dicatat di sini.</p>
        </div>
    @endforelse
</div>
@stop