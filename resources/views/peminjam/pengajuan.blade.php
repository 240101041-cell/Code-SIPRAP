@extends('adminlte::page')

@section('title', 'Form Pengajuan Peminjaman')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="fw-bold text-dark m-0" style="font-size: 1.6rem;">Pengajuan Fasilitas</h1>
            <p class="text-secondary small m-0">Reservasi ruangan kelas, laboratorium, dan peminjaman alat praktikum.</p>
        </div>
        <a href="{{ url('/peminjam/riwayat') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
            <i class="bi bi-clock-history me-1"></i> Riwayat Pengajuan
        </a>
    </div>
@stop

@section('content')
<style>
    .card-modern {
        border: none !important;
        border-radius: 14px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
    }
    .table-custom thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <form action="{{ url('/peminjam/ajukan') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- BLOK 1: DATA WAKTU & RUANGAN -->
            <div class="card card-modern mb-4">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-calendar2-range me-2"></i>1. Waktu & Lokasi Penggunaan</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Peminjaman <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_peminjaman" class="form-control rounded-3" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Mulai Sesi <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam Selesai Sesi <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control rounded-3" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pilih Ruangan / Laboratorium <span class="text-danger">*</span></label>
                            <select name="id_ruangan" class="form-select rounded-3" required>
                                <option value="" selected disabled>-- Pilih Ruangan / Lab --</option>
                                @foreach($ruangan as $r)
                                    <option value="{{ $r->id_ruangan }}">
                                        {{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas }} orang)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tujuan / Keperluan Praktikum <span class="text-danger">*</span></label>
                            <input type="text" name="keperluan" class="form-control rounded-3" placeholder="Contoh: Praktikum Jaringan Komputer Sistem Informasi" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOK 2: DATA ALAT PRAKTIKUM (DYNAMIC ROW) -->
            <div class="card card-modern mb-4">
                <div class="card-header bg-white border-bottom border-light py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-tools me-2"></i>2. Peralatan Praktikum yang Dibutuhkan</h6>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btn-tambah-alat">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Alat
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom align-middle m-0" id="tabel-alat">
                            <thead>
                                <tr>
                                    <th style="min-width: 250px;">Nama Peralatan</th>
                                    <th style="width: 140px;" class="text-center">Jumlah Unit</th>
                                    <th style="min-width: 250px;">Catatan Kebutuhan Awal</th>
                                    <th style="width: 80px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="baris-alat">
                                <tr>
                                    <td>
                                        <select name="alat[0][id_alat]" class="form-select rounded-3" required>
                                            <option value="" selected disabled>-- Pilih Alat --</option>
                                            @foreach($alat as $a)
                                                <option value="{{ $a->id_alat }}">
                                                    {{ $a->nama_alat }} (Tersedia: {{ $a->stok_total - $a->stok_rusak }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="alat[0][jumlah_diminta]" class="form-control rounded-3 text-center" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="text" name="alat[0][catatan_kondisi_awal]" class="form-control rounded-3" placeholder="Opsional (misal: kabel daya 5m)">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-3 hapus-baris" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BLOK 3: LAMPIRAN BERKAS -->
            <div class="card card-modern mb-4">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-file-earmark-arrow-up me-2"></i>3. Dokumen Pendukung</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Unggah Surat Izin / Proposal Praktikum <span class="text-danger">*</span></label>
                            <input type="file" name="dokumen_proposal" class="form-control rounded-3" accept=".pdf,.doc,.docx" required>
                            <small class="text-muted d-block mt-1">Format dokumen: PDF, DOC, atau DOCX (Maksimal 5 MB).</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <button type="reset" class="btn btn-light rounded-pill px-4">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                    <i class="bi bi-send-fill me-1"></i> Kirim Permohonan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    let barisIndex = 1;
    const daftarPilihanAlat = `
        <option value="" selected disabled>-- Pilih Alat --</option>
        @foreach($alat as $a)
            <option value="{{ $a->id_alat }}">{{ $a->nama_alat }} (Tersedia: {{ $a->stok_total - $a->stok_rusak }})</option>
        @endforeach
    `;

    document.getElementById('btn-tambah-alat').addEventListener('click', function() {
        const tabelBody = document.getElementById('baris-alat');
        const trBaru = document.createElement('tr');
        trBaru.innerHTML = `
            <td>
                <select name="alat[${barisIndex}][id_alat]" class="form-select rounded-3" required>
                    ${daftarPilihanAlat}
                </select>
            </td>
            <td>
                <input type="number" name="alat[${barisIndex}][jumlah_diminta]" class="form-control rounded-3 text-center" min="1" value="1" required>
            </td>
            <td>
                <input type="text" name="alat[${barisIndex}][catatan_kondisi_awal]" class="form-control rounded-3" placeholder="Opsional">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 hapus-baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tabelBody.appendChild(trBaru);
        barisIndex++;
    });

    document.getElementById('baris-alat').addEventListener('click', function(e) {
        if (e.target.closest('.hapus-baris')) {
            const baris = e.target.closest('tr');
            if (document.querySelectorAll('#baris-alat tr').length > 1) {
                baris.remove();
            }
        }
    });
</script>
@stop