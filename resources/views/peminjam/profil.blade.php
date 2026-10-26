@extends('layouts.peminjam')

@section('title', 'Profil Pengguna | SIPRAP')
@section('breadcrumb', 'Peminjam / Profil')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Profil Saya</h3>
    <p class="text-secondary small m-0">Informasi identitas akun peminjam terdaftar.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom p-4 text-center">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #e0e7ff; color: #2563eb;" class="d-flex align-items-center justify-content-center mx-auto mb-3 fs-2 fw-bold">
                {{ strtoupper(substr($user->nama ?? 'U', 0, 2)) }}
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->nama ?? session('nama') }}</h5>
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small mb-3">Peminjam Terdaftar</span>
            <p class="text-secondary small m-0">Universitas Duta Bangsa Surakarta</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Rincian Akun Pengguna</h6>
            <div class="row g-3 small">
                <div class="col-md-6">
                    <span class="text-secondary d-block">Nama Lengkap</span>
                    <strong class="text-dark">{{ $user->nama ?? '-' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Username</span>
                    <strong class="text-dark"><code>{{ $user->username ?? '-' }}</code></strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Email Terdaftar</span>
                    <strong class="text-dark">{{ $user->email ?? '-' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Hak Akses (Role)</span>
                    <strong class="text-dark text-capitalize">{{ $user->role ?? 'peminjam' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Status Akun</span>
                    <strong class="text-success"><i class="bi bi-check-circle me-1"></i>{{ ucfirst($user->status_akun ?? 'Aktif') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@stop