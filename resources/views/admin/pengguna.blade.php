@extends('layouts.admin')

@section('title', 'Kelola Pengguna | SIPRAP')
@section('breadcrumb', 'Admin / Kelola Pengguna')

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
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Kelola Pengguna</h3>
        <p class="text-secondary small m-0">Atur akun akses sistem SIPRAP untuk Peminjam, Laboran, dan Admin.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group" style="width: 260px;">
            <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama / username..." style="font-size: 0.85rem;">
        </div>
        <button class="btn btn-primary btn-sm px-3 rounded-3 fw-semibold" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="bi bi-plus-lg me-1"></i> Tambah Akun
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Peran (Role)</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <strong class="text-dark d-block">{{ $user->nama }}</strong>
                    </td>
                    <td class="text-secondary"><code>{{ $user->username }}</code></td>
                    <td class="text-secondary">{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Kepala / Admin</span>
                        @elseif($user->role === 'laboran')
                            <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill">Laboran</span>
                        @else
                            <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">Peminjam</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Aktif</span>
                    </td>
                    <td class="text-end">
                        <form action="/admin/pengguna/{{ $user->id_user }}/status" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                Nonaktifkan
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold text-dark m-0">Tambah Akun Pengguna Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/pengguna/tambah" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control rounded-3 small" placeholder="Nama lengkap..." required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Username</label>
                    <input type="text" name="username" class="form-control rounded-3 small" placeholder="Username login..." required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Email</label>
                    <input type="email" name="email" class="form-control rounded-3 small" placeholder="email@udb.ac.id" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Password Awal</label>
                    <input type="password" name="password" class="form-control rounded-3 small" placeholder="Password default..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Peran (Role)</label>
                    <select name="role" class="form-select rounded-3 small">
                        <option value="peminjam">Peminjam (Mahasiswa / Dosen)</option>
                        <option value="laboran">Laboran / Teknisi</option>
                        <option value="admin">Kepala Laboratorium / Admin</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop