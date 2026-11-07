@extends('layouts.laboran')

@section('title', 'Profil Laboran | SIPRAP')
@section('breadcrumb', 'Laboran / Profil')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Profil Laboran</h3>
        <p class="text-secondary small m-0">Informasi kredensial dan hak akses operasional laboratorium.</p>
    </div>
    <!-- Tombol Ubah Profil Mandiri F1-04 -->
    <button class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalUbahProfil">
        <i class="bi bi-pencil-square"></i> Ubah Profil
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom p-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
            <div style="width: 86px; height: 86px; border-radius: 50%; background: #e0f2fe; color: #0284c7;" class="d-flex align-items-center justify-content-center mb-3 fs-2 fw-bold">
                {{ strtoupper(substr($user->nama ?? session('nama', 'Rina Wulandari'), 0, 2)) }}
            </div>
            <h5 class="fw-bold text-dark mb-1 text-capitalize">{{ $user->nama ?? session('nama', 'Rina Wulandari') }}</h5>
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small mb-3 fw-semibold">
                Laboran / Teknisi Laboratorium
            </span>
            <p class="text-secondary small m-0">Fakultas Ilmu Komputer</p>
            <p class="text-secondary small m-0">Universitas Duta Bangsa Surakarta</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">Rincian Akun Pengguna</h6>
            <div class="row g-4 small">
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Nama Lengkap</span>
                    <strong class="text-dark fs-6">{{ $user->nama ?? session('nama') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">ID Pengguna</span>
                    <strong class="text-dark fs-6">#USR-00{{ $user->id_user ?? session('user_id', 2) }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Username Login</span>
                    <code class="text-primary fs-6">{{ $user->username ?? session('username') }}</code>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Email Resmi</span>
                    <strong class="text-dark fs-6">{{ $user->email ?? '-' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Status Akun</span>
                    <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>{{ ucfirst($user->status_akun ?? 'Aktif') }}
                    </span>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block mb-1">Unit Wewenang</span>
                    <strong class="text-dark fs-6">Laboratorium Praktikum UDB</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL UBAH PROFIL LABORAN -->
<div class="modal fade" id="modalUbahProfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold text-dark m-0">Ubah Data Profil</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/peminjam/profil/update" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control rounded-3 small" value="{{ $user->nama ?? session('nama') }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Username</label>
                    <input type="text" class="form-control rounded-3 small bg-light" value="{{ $user->username ?? session('username') }}" readonly disabled>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Email</label>
                    <input type="email" name="email" class="form-control rounded-3 small" value="{{ $user->email ?? '' }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control rounded-3 small" placeholder="Ketik kata sandi baru...">
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop