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

<div class="mb-4">
    <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Profil Saya</h3>
    <p class="text-secondary small m-0">Informasi akun dan hak akses operasional laboratorium.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom p-4 text-center">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #e0f2fe; color: #0284c7;" class="d-flex align-items-center justify-content-center mx-auto mb-3 fs-2 fw-bold">
                RW
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ session('nama', 'Rina Wulandari') }}</h5>
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small mb-3">Laboran / Teknisi Lab</span>
            <p class="text-secondary small m-0">Fakultas Ilmu Komputer<br>Universitas Duta Bangsa Surakarta</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Rincian Informasi Pengguna</h6>
            <div class="row g-3 small mb-4">
                <div class="col-md-6">
                    <span class="text-secondary d-block">Nama Lengkap</span>
                    <strong class="text-dark">{{ session('nama', 'Rina Wulandari') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">NIP / ID Petugas</span>
                    <strong class="text-dark">199408122022012004</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Username</span>
                    <strong class="text-dark">{{ session('username', 'rina.lab') }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Email Resmi</span>
                    <strong class="text-dark">rina.wulandari@staff.udb.ac.id</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">No. Telepon / WhatsApp</span>
                    <strong class="text-dark">0813-9876-5432</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Unit Tugas</span>
                    <strong class="text-dark">Laboratorium Komputer & Jaringan (Gedung B)</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@stop