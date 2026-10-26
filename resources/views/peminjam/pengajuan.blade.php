@extends('layouts.peminjam')

@section('title', 'Formulir Pengajuan Peminjaman | SIPRAP')
@section('breadcrumb', 'Peminjam / Pengajuan Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pengajuan Peminjaman</h3>
        <p class="text-secondary small m-0">Ruangan laboratorium dan peralatan praktikum diajukan sekaligus dalam satu formulir.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<form action="/peminjam/pengajuan/simpan" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Data Peminjaman Ruangan</h6>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Pilih Ruangan Laboratorium</label>
                    <select name="id_ruangan" class="form-select rounded-3 small" required>
                        <option value="">-- Pilih Laboratorium Tersedia --</option>
                        @foreach($ruangan as $r)
                            <option value="{{ $r->id_ruangan }}">{{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas }} orang)</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Waktu Mulai Praktikum</label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control rounded-3 small" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Waktu Selesai Praktikum</label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control rounded-3 small" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Keperluan Praktikum / Kegiatan</label>
                    <textarea name="keperluan" class="form-control rounded-3 small" rows="3" placeholder="Jelaskan modul praktikum, mata kuliah, atau agenda kegiatan..." required></textarea>
                </div>
            </div>

            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Peralatan Praktikum Tambahan (Opsional)</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Alat Praktikum</label>
                        <select name="id_alat" class="form-select rounded-3 small">
                            <option value="">-- Tidak Meminjam Alat Tambahan --</option>
                            @foreach($alat as $a)
                                <option value="{{ $a->id_alat }}">{{ $a->nama_alat }} (Sisa Stok: {{ $a->stok }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Jumlah Unit</label>
                        <input type="number" name="jumlah_alat" class="form-control rounded-3 small" value="1" min="1">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="/peminjam/dashboard" class="btn btn-light rounded-3 px-4 py-2 small fw-semibold">Batal</a>
                <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 small fw-semibold" style="background-color: #2563eb;">
                    <i class="bi bi-send me-1"></i> Ajukan Peminjaman
                </button>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom p-4">
                <h6 class="fw-bold text-dark mb-3">Data Pemohon</h6>
                <div class="mb-3">
                    <span class="text-secondary small d-block">Nama Lengkap</span>
                    <strong class="text-dark small">{{ session('nama') }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-secondary small d-block">Username / Akun</span>
                    <strong class="text-dark small"><code>{{ session('username') }}</code></strong>
                </div>
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <small class="text-secondary d-block" style="font-size: 0.75rem;">
                        Pengajuan Anda akan masuk ke antrean verifikasi petugas Laboran sebelum jadwal resmi dikunci.
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>
@stop