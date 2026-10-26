@extends('layouts.laboran')

@section('title', 'Periksa Pengajuan | SIPRAP')
@section('breadcrumb', 'Laboran / Verifikasi / Periksa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pemeriksaan Pengajuan #PJ-{{ str_pad($detail->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</h3>
        <p class="text-secondary small m-0">Periksa detail permohonan sebelum memberi keputusan persetujuan atau penolakan.</p>
    </div>
    <a href="/laboran/verifikasi" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold small">Kembali</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3">Informasi Permohonan</h6>
            <div class="row g-3 small">
                <div class="col-md-6">
                    <span class="text-secondary d-block">Nama Pemohon</span>
                    <strong class="text-dark">{{ $detail->pemohon }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Email Pemohon</span>
                    <strong class="text-dark">{{ $detail->email ?? '-' }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Ruangan Laboratorium</span>
                    <strong class="text-dark">{{ $detail->nama_ruangan }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Keperluan Kegiatan</span>
                    <strong class="text-dark">{{ $detail->keperluan }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Waktu Mulai</span>
                    <strong class="text-dark">{{ $detail->tanggal_mulai }}</strong>
                </div>
                <div class="col-md-6">
                    <span class="text-secondary d-block">Waktu Selesai</span>
                    <strong class="text-dark">{{ $detail->tanggal_selesai }}</strong>
                </div>
            </div>
        </div>

        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">Item Alat yang Diajukan</h6>
            <div class="table-responsive">
                <table class="table align-middle m-0 small">
                    <thead>
                        <tr class="text-secondary" style="border-bottom: 2px solid #f1f5f9;">
                            <th>Nama Alat</th>
                            <th>Jumlah</th>
                            <th>Kondisi Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alat as $item)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $item->nama_alat }}</td>
                            <td>{{ $item->jumlah ?? 1 }} unit</td>
                            <td class="text-success"><i class="bi bi-check-circle me-1"></i>{{ ucfirst($item->kondisi ?? 'Baik') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-3">Pengajuan ini hanya meminjam ruangan (tanpa alat tambahan).</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4 text-center">
            <h6 class="fw-bold text-dark mb-2">Keputusan Laboran</h6>
            <p class="text-secondary small mb-4">Tentukan persetujuan untuk pengajuan ini.</p>

            <form action="/laboran/verifikasi/{{ $detail->id_peminjaman }}/proses" method="POST" class="d-grid gap-2">
                @csrf
                <button type="submit" name="aksi" value="setujui" class="btn text-white py-2 fw-semibold rounded-3" style="background-color: #16a34a;">
                    <i class="bi bi-check2-circle me-1"></i> Setujui Pengajuan
                </button>
                
                <div class="border-top my-3"></div>

                <div class="text-start mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Alasan jika ditolak:</label>
                    <textarea name="alasan" class="form-control rounded-3 small" rows="2" placeholder="Tulis alasan penolakan..."></textarea>
                </div>
                <button type="submit" name="aksi" value="tolak" class="btn btn-outline-danger py-2 fw-semibold rounded-3 small">
                    <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                </button>
            </form>
        </div>
    </div>
</div>
@stop