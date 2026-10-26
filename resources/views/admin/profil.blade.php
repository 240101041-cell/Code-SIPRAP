@extends('layouts.admin')

@section('title', 'Profil Administrator | SIPRAP')
@section('breadcrumb', 'Admin / Profil')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
</style>

<div class="mb-4">
    <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Profil Administrator</h3>
    <p class="text-secondary small m-0">Informasi kredensial dan hak kontrol master sistem SIPRAP UDB.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom p-4 text-center">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #fee2e2; color: #dc2626;" class="d-flex align-items-center justify-content-center mx-auto mb-3 fs-2 fw-bold">
                BS
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ session('nama', 'Budi Santoso') }}</h5>
            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill small mb-3">Kepala Laboratorium & Admin</span>
            <p class="text-secondary small m-0">Fakultas Ilmu Komputer<br>Universitas Duta Bangsa Surakarta</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Rincian Informasi Akun</h6>
            <div class="row g-3 small mb-4">
                <div class="col-md-6">
                    <span class="text-secondary d-block">Nama Lengkap</span>
                    <strong class="text-dark">{{ session('nama', 'Budi Santoso') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">NIP</span>
                    <strong class="text-dark">198205142010121001</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Username</span>
                    <strong class="text-dark">{{ session('username', 'budi.admin') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Email Kedinasan</span>
                    <strong class="text-dark">budi.santoso@udb.ac.id</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Kontak Langsung</span>
                    <strong class="text-dark">0812-1122-3344</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Hak Akses</span>
                    <strong class="text-dark">Super Administrator / Monitoring Penuh</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@stop