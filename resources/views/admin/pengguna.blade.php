@extends('layouts.admin')

@section('title', 'Kelola Pengguna | SIPRAP')
@section('breadcrumb', 'Admin / Kelola Pengguna')

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
        width: 280px;
    }
    /* Style Tombol Aksi Rapi & Seragam */
    .btn-action-custom {
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-action-icon {
        width: 32px;
        padding: 0;
    }
    .btn-action-status {
        padding: 0 12px;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Kelola Pengguna</h3>
        <p class="text-secondary small m-0">Atur akun akses sistem SIPRAP untuk Peminjam, Laboran, dan Admin.</p>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
            <input type="text" id="searchInput" class="form-control search-input-box" placeholder="Cari nama / username..." value="{{ $search ?? '' }}">
        </div>

        <button class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
            <i class="bi bi-plus-lg"></i> Tambah Akun
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>{{ session('error') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-custom p-3">
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="userTable">
            <thead>
                <tr class="text-secondary" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 220px; padding: 12px 16px;">Nama Pengguna</th>
                    <th style="width: 140px;">Username</th>
                    <th>Email</th>
                    <th class="text-center" style="width: 140px;">Peran (Role)</th>
                    <th class="text-center" style="width: 110px;">Status</th>
                    <th class="text-center" style="width: 190px; padding-right: 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="user-row" style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 14px 16px;">
                        <strong class="text-dark nama-val">{{ $u->nama }}</strong>
                    </td>
                    <td>
                        <span class="text-primary username-val fw-semibold" style="font-family: monospace; font-size: 0.85rem;">{{ $u->username }}</span>
                    </td>
                    <td class="text-secondary email-val">{{ $u->email }}</td>
                    <td class="text-center">
                        @if($u->role === 'admin')
                            <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1 fw-semibold">Kepala / Admin</span>
                        @elseif($u->role === 'laboran')
                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-1 fw-semibold">Laboran</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-1 fw-semibold">Peminjam</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(($u->status_akun ?? 'aktif') === 'aktif')
                            <span class="badge-status-pill" style="background: #dcfce7; color: #15803d;">Aktif</span>
                        @else
                            <span class="badge-status-pill" style="background: #fee2e2; color: #dc2626;">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center" style="padding-right: 16px;">
                        <div class="d-inline-flex align-items-center justify-content-center gap-2">
                            <!-- 1. Tombol Edit -->
                            <button type="button" class="btn btn-outline-primary btn-action-custom btn-action-icon" title="Edit Akun" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $u->id_user }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- 2. Tombol Toggle Status -->
                            <form action="/admin/pengguna/{{ $u->id_user }}/status" method="POST" class="d-inline m-0 p-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-action-custom btn-action-status" title="Ubah Status">
                                    {{ ($u->status_akun ?? 'aktif') === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <!-- 3. Tombol Hapus / Disabled placeholder agar sejajar -->
                            @if($u->id_user != session('user_id'))
                                <button type="button" class="btn btn-outline-danger btn-action-custom btn-action-icon" title="Hapus Akun" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $u->id_user }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-action-custom btn-action-icon disabled opacity-25" title="Akun Anda Sendiri" style="cursor: not-allowed;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>

                        <!-- MODAL EDIT PENGGUNA -->
                        <div class="modal fade text-start" id="modalEdit{{ $u->id_user }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold text-dark m-0">Edit Data Pengguna</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="/admin/pengguna/{{ $u->id_user }}/update" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control rounded-3 small" value="{{ $u->nama }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold text-dark mb-1">Username (ID Login)</label>
                                            <input type="text" class="form-control rounded-3 small bg-light" value="{{ $u->username }}" readonly disabled>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold text-dark mb-1">Email</label>
                                            <input type="email" name="email" class="form-control rounded-3 small" value="{{ $u->email }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold text-dark mb-1">Peran Akses (Role)</label>
                                            <select name="role" class="form-select rounded-3 small">
                                                <option value="peminjam" {{ $u->role === 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                                                <option value="laboran" {{ $u->role === 'laboran' ? 'selected' : '' }}>Laboran</option>
                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Kepala / Admin</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                                            <input type="text" name="password" class="form-control rounded-3 small" placeholder="Ketik sandi baru bila ingin direset...">
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL KONFIRMASI HAPUS -->
                        @if($u->id_user != session('user_id'))
                        <div class="modal fade text-start" id="modalHapus{{ $u->id_user }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0 p-3 text-center">
                                    <div class="text-danger mb-2"><i class="bi bi-trash fs-1"></i></div>
                                    <h6 class="fw-bold text-dark mb-1">Hapus Pengguna?</h6>
                                    <p class="text-secondary small mb-3">Akun <strong>{{ $u->nama }}</strong> akan dihapus permanen dari sistem.</p>
                                    <form action="/admin/pengguna/{{ $u->id_user }}/hapus" method="POST">
                                        @csrf
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-3 px-3 py-1 small">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary small">
                        <i class="bi bi-people fs-2 d-block text-muted mb-2"></i>
                        Tidak ada data pengguna ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH AKUN -->
<div class="modal fade" id="modalTambahAkun" tabindex="-1" aria-hidden="true">
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
                    <input type="text" name="nama" class="form-control rounded-3 small" placeholder="Contoh: Dosen / Mahasiswa" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Username</label>
                        <input type="text" name="username" class="form-control rounded-3 small" placeholder="contoh.nama" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Peran (Role)</label>
                        <select name="role" class="form-select rounded-3 small" required>
                            <option value="peminjam">Peminjam</option>
                            <option value="laboran">Laboran</option>
                            <option value="admin">Kepala / Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Email</label>
                    <input type="email" name="email" class="form-control rounded-3 small" placeholder="user@udb.ac.id" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Password</label>
                    <input type="password" name="password" class="form-control rounded-3 small" placeholder="Minimal 4 karakter" required>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userTable tbody tr.user-row');

        rows.forEach(row => {
            const nama = row.querySelector('.nama-val')?.textContent.toLowerCase() || '';
            const user = row.querySelector('.username-val')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.email-val')?.textContent.toLowerCase() || '';

            if (nama.includes(filter) || user.includes(filter) || email.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@stop