@extends('layouts.laboran')

@section('title', 'Jadwal Pemakaian Laboratorium | SIPRAP')
@section('breadcrumb', 'Laboran / Jadwal')

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
    .search-input-box {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 7px 14px 7px 36px;
        font-size: 0.88rem;
        width: 240px;
    }
    .slot-badge-btn {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #475569;
        cursor: pointer;
    }
    .slot-badge-btn:hover {
        background: #eff6ff;
        border-color: #2563eb;
        color: #2563eb;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Jadwal Pemakaian Laboratorium</h3>
        <p class="text-secondary small m-0">Agenda perkuliahan praktikum reguler dan peminjaman insidental yang aktif.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
            <input type="text" id="searchJadwal" class="form-control search-input-box" placeholder="Cari lab / mata kuliah...">
        </div>
        <!-- Tombol Tambah Jadwal Reguler -->
        <button class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-flex align-items-center gap-1" style="background-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
            <i class="bi bi-plus-lg"></i> Tambah Jadwal Reguler
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

<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark m-0">Daftar Jadwal Penggunaan Lab Terkunci</h6>
        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-1 rounded-pill small">
            Total: {{ $jadwal->count() }} Sesi Terjadwal
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle m-0" style="font-size: 0.88rem;" id="tableJadwal">
            <thead>
                <tr class="text-secondary" style="font-size: 0.78rem; border-bottom: 2px solid #f1f5f9;">
                    <th style="width: 220px;">Waktu & Tanggal</th>
                    <th>Ruangan Lab</th>
                    <th>Mata Kuliah / Kegiatan</th>
                    <th>Dosen / Penanggung Jawab</th>
                    <th class="text-center" style="width: 140px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $j)
                <tr class="jadwal-row">
                    <td class="fw-bold text-dark">
                        <i class="bi bi-clock me-1 text-primary"></i>
                        {{ $j->tanggal_mulai ?? '-' }}
                        <small class="text-secondary d-block fw-normal">s/d {{ $j->tanggal_selesai ?? '-' }}</small>
                    </td>
                    <td class="fw-semibold text-dark lab-name">{{ $j->nama_ruangan ?? 'Laboratorium' }}</td>
                    <td class="agenda-name">{{ $j->keperluan ?? '-' }}</td>
                    <td class="text-secondary">{{ $j->pemohon ?? 'Jadwal Kuliah Reguler' }}</td>
                    <td class="text-center">
                        @php
                            $statusVal = strtolower($j->status_peminjaman ?? ($j->status ?? ''));
                        @endphp
                        @if($statusVal === 'dipinjam')
                            <span class="badge-status-pill" style="background: #f3e8ff; color: #7e22ce;">Sedang Berlangsung</span>
                        @elseif($statusVal === 'disetujui')
                            <span class="badge-status-pill" style="background: #e0f2fe; color: #0369a1;">Terjadwal (Terkunci)</span>
                        @else
                            <span class="badge-status-pill" style="background: #f1f5f9; color: #475569;">{{ ucfirst($statusVal) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-secondary small">
                        <i class="bi bi-calendar-x fs-2 d-block text-muted mb-2"></i>
                        Belum ada jadwal praktikum reguler atau peminjaman aktif.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH JADWAL REGULER -->
<div class="modal fade text-start" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold text-dark m-0">Tambah Jadwal Praktikum Reguler</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/laboran/jadwal/tambah" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Ruangan Laboratorium</label>
                    <select name="id_ruangan" class="form-select rounded-3 small" required>
                        <option value="">-- Pilih Laboratorium --</option>
                        @foreach($ruangan as $r)
                            <option value="{{ $r->id_ruangan }}">{{ $r->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Mata Kuliah / Kelas Praktikum</label>
                    <input type="text" name="keperluan" class="form-control rounded-3 small" placeholder="Contoh: Praktikum Jaringan Komputer - TI 3A" required>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Tanggal Praktikum</label>
                        <input type="date" name="tanggal" class="form-control rounded-3 small" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="regJamMulai" class="form-control rounded-3 small" value="08:00" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="regJamSelesai" class="form-control rounded-3 small" value="10:00" required>
                    </div>
                </div>

                <!-- Slot Cepat Reguler -->
                <div class="mb-3">
                    <small class="text-secondary d-block mb-1" style="font-size: 0.72rem;">Pilih jam sesi perkuliahan:</small>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="slot-badge-btn" onclick="setSlotModal('08:00', '10:00')">Sesi 1 (08-10)</button>
                        <button type="button" class="slot-badge-btn" onclick="setSlotModal('10:00', '12:00')">Sesi 2 (10-12)</button>
                        <button type="button" class="slot-badge-btn" onclick="setSlotModal('13:00', '15:00')">Sesi 3 (13-15)</button>
                        <button type="button" class="slot-badge-btn" onclick="setSlotModal('15:30', '17:30')">Sesi 4 (15:30-17:30)</button>
                    </div>
                </div>

                <div class="p-2 rounded-3 mb-3 bg-light text-secondary small" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle text-primary me-1"></i> Jadwal ini langsung berstatus <strong>Disetujui</strong> dan secara otomatis mengunci lab dari peminjaman mahasiswa.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-1 small" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-1 small" style="background-color: #2563eb;">Simpan & Kunci Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setSlotModal(mulai, selesai) {
        document.getElementById('regJamMulai').value = mulai;
        document.getElementById('regJamSelesai').value = selesai;
    }

    document.getElementById('searchJadwal').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#tableJadwal tbody tr.jadwal-row').forEach(row => {
            const lab = row.querySelector('.lab-name')?.textContent.toLowerCase() || '';
            const agenda = row.querySelector('.agenda-name')?.textContent.toLowerCase() || '';
            row.style.display = (lab.includes(filter) || agenda.includes(filter)) ? '' : 'none';
        });
    });
</script>
@stop