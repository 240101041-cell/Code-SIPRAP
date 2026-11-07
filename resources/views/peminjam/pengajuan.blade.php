@extends('layouts.peminjam')

@section('title', 'Pengajuan Peminjaman | SIPRAP')
@section('breadcrumb', 'Peminjam / Pengajuan Peminjaman')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 9px 13px;
        font-size: 0.88rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .btn-tab-pilihan {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 9px 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-tab-pilihan.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    /* KOTAK SLOT WAKTU VISUAL */
    .slot-box {
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .slot-box.tersedia {
        border-color: #86efac;
        background: #f0fdf4;
    }
    .slot-box.tersedia:hover {
        border-color: #22c55e;
        transform: translateY(-2px);
    }
    .slot-box.terpilih {
        border-color: #2563eb !important;
        background: #eff6ff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }
    .slot-box.terpakai {
        border-color: #fca5a5;
        background: #fef2f2;
        cursor: not-allowed;
        opacity: 0.75;
    }
</style>

<div class="mb-4">
    <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pengajuan Peminjaman</h3>
    <p class="text-secondary small m-0">Pilih ruangan dan tanggal praktikum untuk melihat ketersediaan jam secara langsung.</p>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-4 py-2 px-3 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>{{ session('error') }}</span>
    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="/peminjam/pengajuan/simpan" method="POST">
    @csrf

    <div class="row g-4">
        <!-- FORM KIRI -->
        <div class="col-lg-8">
            <!-- PILIHAN KATEGORI -->
            <div class="card-custom p-4 mb-4">
                <label class="form-label small fw-bold text-dark mb-2">Pilih Kategori Permohonan</label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn-tab-pilihan active" id="btnPaketLengkap" onclick="setPilihan('lengkap')">
                        <i class="bi bi-building-check me-1"></i> Ruangan & Alat Praktikum
                    </button>
                    <button type="button" class="btn-tab-pilihan" id="btnHanyaRuang" onclick="setPilihan('ruang')">
                        <i class="bi bi-door-open me-1"></i> Hanya Ruangan Lab
                    </button>
                    <button type="button" class="btn-tab-pilihan" id="btnHanyaAlat" onclick="setPilihan('alat')">
                        <i class="bi bi-box-seam me-1"></i> Hanya Peralatan
                    </button>
                </div>
            </div>

            <!-- BLOK JADWAL & RUANGAN -->
            <div class="card-custom p-4 mb-4" id="blokRuangan">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-door-open me-1 text-primary"></i> Data Ruangan & Tanggal Praktikum</h6>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-7" id="inputRuanganGroup">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Laboratorium</label>
                        <select name="id_ruangan" id="selectRuangan" class="form-select" onchange="muatSlotKetersediaan()" required>
                            <option value="">-- Pilih Laboratorium Tersedia --</option>
                            @foreach($ruangan as $r)
                                <option value="{{ $r->id_ruangan }}" {{ request('ruangan') == $r->id_ruangan ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas }} orang)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Tanggal Praktikum</label>
                        <input type="date" name="tanggal" id="inputTanggal" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" onchange="muatSlotKetersediaan()" required>
                    </div>
                </div>

                <!-- INDIKATOR VISUAL SLOT KOSONG / TERPAKAINYA -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold text-dark m-0">Ketersediaan Sesi Praktikum (08:00 - 17:00 WIB)</label>
                        <span id="loadingSlot" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                    </div>

                    <div class="row g-2" id="wadahSlotWaktu">
                        <div class="col-12 text-muted small py-2 text-center border rounded-3 bg-light">
                            Silakan pilih ruangan dan tanggal terlebih dahulu untuk memeriksa jam yang kosong.
                        </div>
                    </div>
                </div>

                <!-- INPUT HIDDEN JAM (OTOMATIS TERISI KETIKA KLIK KOTAK SLOT) -->
                <input type="hidden" name="jam_mulai" id="inputJamMulai" required>
                <input type="hidden" name="jam_selesai" id="inputJamSelesai" required>

                <!-- Status Jam Yang Dipilih -->
                <div class="p-2 mb-3 rounded-3 bg-light border d-flex justify-content-between align-items-center" id="boxJamTerpilih" style="display: none !important;">
                    <span class="small text-secondary">Sesi yang Anda pilih:</span>
                    <strong class="text-primary small" id="textJamTerpilih">-</strong>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-dark mb-1">Keperluan Praktikum / Agenda Kegiatan</label>
                    <textarea name="keperluan" class="form-control" rows="3" placeholder="Contoh: Praktikum Pemrograman Web / Tugas Akhir Mandiri..." required></textarea>
                </div>
            </div>

            <!-- BLOK PERALATAN PRAKTIKUM -->
            <div class="card-custom p-4" id="blokAlat">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-tools me-1 text-primary"></i> Peralatan Praktikum Penunjang</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Alat Praktikum</label>
                        <select name="id_alat" id="selectAlat" class="form-select">
                            <option value="">-- Tidak Meminjam Alat Tambahan --</option>
                            @foreach($alat as $a)
                                <option value="{{ $a->id_alat }}" {{ request('alat') == $a->id_alat ? 'selected' : '' }}>
                                    {{ $a->nama_alat }} (Tersedia: {{ $a->stok ?? ($a->stok_alat ?? 1) }} unit)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Jumlah Unit</label>
                        <input type="number" name="jumlah_alat" id="jumlahAlat" class="form-control" value="1" min="1" max="10">
                    </div>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="/peminjam/dashboard" class="btn btn-light rounded-3 px-4 py-2 fw-semibold">Batal</a>
                <button type="submit" id="btnSubmitPengajuan" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold" style="background-color: #2563eb;" disabled>
                    <i class="bi bi-send me-1"></i> Ajukan Peminjaman
                </button>
            </div>
        </div>

        <!-- FORM KANAN -->
        <div class="col-lg-4">
            <div class="card-custom p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3">Data Pemohon</h6>
                <div class="mb-2">
                    <span class="text-secondary small d-block">Nama Lengkap</span>
                    <strong class="text-dark">{{ session('nama', 'Mahasiswa') }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-secondary small d-block">Username / Akun</span>
                    <code class="text-primary fw-semibold">{{ session('username') }}</code>
                </div>
                <div class="p-3 rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <small class="text-secondary" style="font-size: 0.78rem;">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Pilih sesi praktikum yang berstatus <strong class="text-success">Tersedia (Hijau)</strong>. Sesi yang <strong class="text-danger">Terpakai (Merah)</strong> tidak dapat dipilih karena bertabrakan dengan jadwal praktikum reguler/peminjaman lain.
                    </small>
                </div>
            </div>

            <div class="card-custom p-4">
                <h6 class="fw-bold text-dark mb-2">Panduan Warna Sesi</h6>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 14px; height: 14px; background: #22c55e; border-radius: 4px; display: inline-block;"></span>
                        <span><strong>Hijau:</strong> Ruangan Kosong & Siap Dipinjam</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 14px; height: 14px; background: #ef4444; border-radius: 4px; display: inline-block;"></span>
                        <span><strong>Merah:</strong> Terpakai (Kuliah Reguler / Dipinjam)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 14px; height: 14px; background: #2563eb; border-radius: 4px; display: inline-block;"></span>
                        <span><strong>Biru:</strong> Sesi yang Sedang Anda Pilih</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function muatSlotKetersediaan() {
        const idRuangan = document.getElementById('selectRuangan').value;
        const tanggal = document.getElementById('inputTanggal').value;
        const wadah = document.getElementById('wadahSlotWaktu');
        const spinner = document.getElementById('loadingSlot');
        const btnSubmit = document.getElementById('btnSubmitPengajuan');

        // Reset pilihan jam
        document.getElementById('inputJamMulai').value = '';
        document.getElementById('inputJamSelesai').value = '';
        document.getElementById('boxJamTerpilih').style.setProperty('display', 'none', 'important');
        btnSubmit.disabled = true;

        if (!idRuangan || !tanggal) {
            wadah.innerHTML = '<div class="col-12 text-muted small py-2 text-center border rounded-3 bg-light">Silakan pilih ruangan dan tanggal terlebih dahulu.</div>';
            return;
        }

        spinner.classList.remove('d-none');

        fetch(`/peminjam/cek-slot?id_ruangan=${idRuangan}&tanggal=${tanggal}`)
            .then(res => res.json())
            .then(data => {
                spinner.classList.add('d-none');
                wadah.innerHTML = '';

                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const col = document.createElement('div');
                        col.className = 'col-md-6 col-lg-3';

                        if (slot.tersedia) {
                            col.innerHTML = `
                                <div class="slot-box tersedia" onclick="pilihSlot(this, '${slot.mulai}', '${slot.selesai}', '${slot.sesi}')">
                                    <div class="fw-bold text-dark small">${slot.sesi}</div>
                                    <div class="text-primary fw-semibold" style="font-size: 0.8rem;">${slot.mulai} - ${slot.selesai}</div>
                                    <span class="badge bg-success-subtle text-success mt-1" style="font-size: 0.68rem;"><i class="bi bi-check-circle me-1"></i>Tersedia</span>
                                </div>
                            `;
                        } else {
                            col.innerHTML = `
                                <div class="slot-box terpakai" title="${slot.agenda}">
                                    <div class="fw-bold text-secondary small">${slot.sesi}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">${slot.mulai} - ${slot.selesai}</div>
                                    <span class="badge bg-danger-subtle text-danger mt-1" style="font-size: 0.68rem;"><i class="bi bi-x-circle me-1"></i>Terpakai</span>
                                </div>
                            `;
                        }
                        wadah.appendChild(col);
                    });
                }
            })
            .catch(() => {
                spinner.classList.add('d-none');
                wadah.innerHTML = '<div class="col-12 text-danger small py-2 text-center border rounded-3">Gagal memuat ketersediaan jam.</div>';
            });
    }

    function pilihSlot(el, mulai, selesai, sesi) {
        // Hapus tanda terpilih dari slot lain
        document.querySelectorAll('.slot-box').forEach(s => s.classList.remove('terpilih'));

        // Aktifkan slot ini
        el.classList.add('terpilih');

        document.getElementById('inputJamMulai').value = mulai;
        document.getElementById('inputJamSelesai').value = selesai;

        const box = document.getElementById('boxJamTerpilih');
        box.style.removeProperty('display');
        document.getElementById('textJamTerpilih').textContent = `${sesi} (${mulai} - ${selesai} WIB)`;

        // Buka tombol submit
        document.getElementById('btnSubmitPengajuan').disabled = false;
    }

    function setPilihan(tipe) {
        document.getElementById('btnPaketLengkap').classList.remove('active');
        document.getElementById('btnHanyaRuang').classList.remove('active');
        document.getElementById('btnHanyaAlat').classList.remove('active');

        const blokRuangan = document.getElementById('blokRuangan');
        const blokAlat = document.getElementById('blokAlat');
        const selectRuangan = document.getElementById('selectRuangan');
        const selectAlat = document.getElementById('selectAlat');
        const inputRuanganGroup = document.getElementById('inputRuanganGroup');

        if (tipe === 'lengkap') {
            document.getElementById('btnPaketLengkap').classList.add('active');
            blokRuangan.style.display = 'block';
            inputRuanganGroup.style.display = 'block';
            blokAlat.style.display = 'block';
            selectRuangan.required = true;
            selectAlat.required = false;
        } else if (tipe === 'ruang') {
            document.getElementById('btnHanyaRuang').classList.add('active');
            blokRuangan.style.display = 'block';
            inputRuanganGroup.style.display = 'block';
            blokAlat.style.display = 'none';
            selectRuangan.required = true;
            selectAlat.value = '';
            selectAlat.required = false;
        } else if (tipe === 'alat') {
            document.getElementById('btnHanyaAlat').classList.add('active');
            blokRuangan.style.display = 'block';
            inputRuanganGroup.style.display = 'none';
            blokAlat.style.display = 'block';
            selectRuangan.required = false;
            selectAlat.required = true;

            if (!selectRuangan.value) {
                selectRuangan.selectedIndex = 1;
            }
        }
        muatSlotKetersediaan();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('alat') && !urlParams.has('ruangan')) {
            setPilihan('alat');
        } else if (urlParams.has('ruangan')) {
            muatSlotKetersediaan();
        }
    });
</script>
@stop