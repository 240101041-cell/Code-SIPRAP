<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaboranController extends Controller
{
    // Helper: Deteksi kolom status tabel peminjaman
    private function getStatusPeminjamanColumn()
    {
        if (Schema::hasColumn('peminjaman', 'status_peminjaman')) {
            return 'status_peminjaman';
        } elseif (Schema::hasColumn('peminjaman', 'status')) {
            return 'status';
        } elseif (Schema::hasColumn('peminjaman', 'status_pengajuan')) {
            return 'status_pengajuan';
        }
        return 'status';
    }

    // Helper: Deteksi kolom tanggal selesai
    private function getTanggalSelesaiColumn()
    {
        if (Schema::hasColumn('peminjaman', 'tanggal_selesai')) {
            return 'tanggal_selesai';
        } elseif (Schema::hasColumn('peminjaman', 'tanggal_kembali')) {
            return 'tanggal_kembali';
        }
        return null;
    }

    // Helper: Deteksi kolom stok alat
    private function getStokAlatColumn()
    {
        if (Schema::hasColumn('alat', 'stok')) {
            return 'stok';
        } elseif (Schema::hasColumn('alat', 'jumlah')) {
            return 'jumlah';
        } elseif (Schema::hasColumn('alat', 'stok_alat')) {
            return 'stok_alat';
        }
        return 'stok';
    }

    // ==========================================
    // 1. DASHBOARD LABORAN
    // ==========================================
    public function dashboard()
    {
        $statusCol = $this->getStatusPeminjamanColumn();
        $tglSelesaiCol = $this->getTanggalSelesaiColumn();

        // Jatuh tempo hari ini khusus untuk peminjaman insidental (bukan kelas reguler)
        $jatuhTempoQuery = DB::table('peminjaman')
            ->where($statusCol, 'dipinjam')
            ->where('keperluan', 'NOT LIKE', '[REGULER]%');

        if ($tglSelesaiCol) {
            $jatuhTempoQuery->whereDate($tglSelesaiCol, now()->toDateString());
        }

        // Filter: Siap Serah-Terima dan Sedang Dipinjam HANYA untuk peminjam mahasiswa/insidental
        $statistik = (object)[
            'menunggu_verifikasi'  => DB::table('peminjaman')
                ->where($statusCol, 'menunggu')
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->count(),
            'siap_serah_terima'    => DB::table('peminjaman')
                ->where($statusCol, 'disetujui')
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->count(),
            'sedang_dipinjam'      => DB::table('peminjaman')
                ->where($statusCol, 'dipinjam')
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->count(),
            'jatuh_tempo_hari_ini' => $jatuhTempoQuery->count()
        ];

        $antrian_verifikasi = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where("peminjaman.{$statusCol}", 'menunggu')
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
            ->latest('peminjaman.created_at')
            ->limit(5)
            ->get();

        $pengembalianQuery = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where("peminjaman.{$statusCol}", 'dipinjam')
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%');

        if ($tglSelesaiCol) {
            $pengembalianQuery->whereDate("peminjaman.{$tglSelesaiCol}", now()->toDateString());
        }

        $pengembalian_hari_ini = $pengembalianQuery
            ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
            ->get();

        return view('laboran.dashboard', compact('statistik', 'antrian_verifikasi', 'pengembalian_hari_ini'));
    }

    // ==========================================
    // 2. VERIFIKASI PENGAJUAN (F7)
    // ==========================================
    public function verifikasi()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        $antrian = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where("peminjaman.{$statusCol}", 'menunggu')
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'asc')
            ->get();

        return view('laboran.verifikasi', compact('antrian'));
    }

    // Detail Verifikasi (Inline Blade Render: Kebal dari error "View not found")
    public function detailVerifikasi($id)
    {
        $detail = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_peminjaman', $id)
            ->select('peminjaman.*', 'user.nama as pemohon', 'user.email', 'ruangan.nama_ruangan', 'ruangan.fasilitas')
            ->first();

        if (!$detail) {
            return redirect('/laboran/verifikasi')->with('warning', 'Data pengajuan tidak ditemukan.');
        }

        $alat = [];
        if (Schema::hasTable('detail_peminjaman')) {
            $alat = DB::table('detail_peminjaman')
                ->leftJoin('alat', 'detail_peminjaman.id_alat', '=', 'alat.id_alat')
                ->where('detail_peminjaman.id_peminjaman', $id)
                ->select('detail_peminjaman.*', 'alat.nama_alat', 'alat.kondisi')
                ->get();
        }

        $html = '
        @extends("layouts.laboran")

        @section("title", "Periksa Pengajuan | SIPRAP")
        @section("breadcrumb", "Laboran / Verifikasi / Periksa")

        @section("content")
        <style>
            .card-custom {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            }
        </style>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0" style="font-size: 1.55rem;">Pemeriksaan Pengajuan #PJ-' . str_pad($detail->id_peminjaman, 4, '0', STR_PAD_LEFT) . '</h3>
                <p class="text-secondary small m-0">Periksa detail permohonan sebelum memberi keputusan persetujuan atau penolakan.</p>
            </div>
            <a href="/laboran/verifikasi" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold small">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark mb-3">Informasi Permohonan</h6>
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <span class="text-secondary d-block">Nama Pemohon</span>
                            <strong class="text-dark">{{ $detail->pemohon ?? "-" }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-secondary d-block">Email Pemohon</span>
                            <strong class="text-dark">{{ $detail->email ?? "-" }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-secondary d-block">Ruangan Laboratorium</span>
                            <strong class="text-dark">{{ $detail->nama_ruangan ?? "-" }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-secondary d-block">Keperluan Kegiatan</span>
                            <strong class="text-dark">{{ $detail->keperluan ?? "-" }}</strong>
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
                                    <th class="text-center" style="width: 120px;">Jumlah</th>
                                    <th class="text-center" style="width: 140px;">Kondisi Terakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alat as $item)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $item->nama_alat }}</td>
                                    <td class="text-center">{{ $item->jumlah ?? 1 }} unit</td>
                                    <td class="text-center text-success"><i class="bi bi-check-circle me-1"></i>{{ ucfirst($item->kondisi ?? "Baik") }}</td>
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
        ';

        $alatCollection = collect($alat);
        return response(
            Blade::render($html, [
                'detail' => $detail,
                'alat'   => $alatCollection
            ])
        );
    }

    // Eksekusi Persetujuan / Penolakan
    public function prosesVerifikasi(Request $request, $id)
    {
        $statusCol = $this->getStatusPeminjamanColumn();
        $aksi = $request->input('aksi');
        $alasan = $request->input('alasan');

        if ($aksi === 'setujui') {
            DB::table('peminjaman')->where('id_peminjaman', $id)->update([
                $statusCol          => 'disetujui',
                'status_peminjaman' => 'disetujui',
                'updated_at'        => now()
            ]);

            return redirect('/laboran/verifikasi')->with('success', "Pengajuan #PJ-" . str_pad($id, 4, '0', STR_PAD_LEFT) . " berhasil disetujui.");
        } else {
            $updateData = [
                $statusCol          => 'ditolak',
                'status_peminjaman' => 'ditolak',
                'updated_at'        => now()
            ];

            if (Schema::hasColumn('peminjaman', 'catatan_penolakan')) {
                $updateData['catatan_penolakan'] = $alasan ?? 'Tidak memenuhi syarat praktikum.';
            }
            if (Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $updateData['alasan_penolakan'] = $alasan ?? 'Tidak memenuhi syarat praktikum.';
            }

            DB::table('peminjaman')->where('id_peminjaman', $id)->update($updateData);

            return redirect('/laboran/verifikasi')->with('warning', "Pengajuan #PJ-" . str_pad($id, 4, '0', STR_PAD_LEFT) . " telah ditolak.");
        }
    }

    // ==========================================
    // 3. STATUS PEMINJAMAN & SERAH-TERIMA (F8)
    // ==========================================
    public function peminjaman()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        // Hanya tampilkan peminjaman insidental (bukan kelas reguler)
        $peminjaman = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->whereIn("peminjaman.{$statusCol}", ['disetujui', 'dipinjam'])
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'desc')
            ->get();

        return view('laboran.peminjaman', compact('peminjaman'));
    }

    public function prosesSerahTerima(Request $request, $id)
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        DB::table('peminjaman')->where('id_peminjaman', $id)->update([
            $statusCol          => 'dipinjam',
            'status_peminjaman' => 'dipinjam',
            'updated_at'        => now()
        ]);

        return redirect('/laboran/peminjaman')->with('success', "Fasilitas untuk pengajuan #PJ-" . str_pad($id, 4, '0', STR_PAD_LEFT) . " berhasil diserahterimakan (Status: Dipinjam).");
    }

    // ==========================================
    // 4. PENGEMBALIAN FASILITAS (F9) & RIWAYAT
    // ==========================================
    public function pengembalian(Request $request)
    {
        $statusCol = $this->getStatusPeminjamanColumn();
        $tab = $request->query('tab', 'aktif'); // 'aktif' atau 'riwayat'

        if ($tab === 'riwayat') {
            // Tampilkan transaksi peminjaman yang sudah selesai / dikembalikan
            $pengembalian = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->whereIn("peminjaman.{$statusCol}", ['selesai', 'dikembalikan'])
                ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
                ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
                ->orderBy('peminjaman.updated_at', 'desc')
                ->get();
        } else {
            // Tampilkan transaksi yang sedang berjalan & menunggu serah-kembali
            $pengembalian = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where("peminjaman.{$statusCol}", 'dipinjam')
                ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
                ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
                ->orderBy('peminjaman.updated_at', 'desc')
                ->get();
        }

        return view('laboran.pengembalian', compact('pengembalian', 'tab'));
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        DB::table('peminjaman')->where('id_peminjaman', $id)->update([
            $statusCol          => 'selesai',
            'status_peminjaman' => 'selesai',
            'updated_at'        => now()
        ]);

        return redirect('/laboran/pengembalian')->with('success', "Pemeriksaan fasilitas #PJ-" . str_pad($id, 4, '0', STR_PAD_LEFT) . " selesai. Transaksi peminjaman resmi ditutup.");
    }

    // ==========================================
    // 5. INVENTARIS PERALATAN (F10)
    // ==========================================
    public function peralatan()
    {
        $alat = DB::table('alat')->orderBy('id_alat', 'asc')->get();
        return view('laboran.peralatan', compact('alat'));
    }

    public function tambahPeralatan(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:100',
            'stok'      => 'required|numeric|min:1',
            'kondisi'   => 'nullable|string'
        ]);

        $stokCol = $this->getStokAlatColumn();

        $dataAlat = [
            'nama_alat'  => $request->nama_alat,
            $stokCol     => $request->stok,
            'kondisi'    => $request->kondisi ?? 'baik',
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('alat')->insert($dataAlat);

        return redirect('/laboran/peralatan')->with('success', 'Data alat praktikum baru berhasil ditambahkan.');
    }

    public function updatePeralatan(Request $request, $id)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:100',
            'stok'      => 'required|numeric|min:0',
            'kondisi'   => 'required|string'
        ]);

        $stokCol = $this->getStokAlatColumn();

        DB::table('alat')->where('id_alat', $id)->update([
            'nama_alat'  => $request->nama_alat,
            $stokCol     => $request->stok,
            'kondisi'    => $request->kondisi,
            'updated_at' => now()
        ]);

        return redirect('/laboran/peralatan')->with('success', "Informasi alat praktikum berhasil diperbarui.");
    }

    // ==========================================
    // 6. JADWAL PENGGUNAAN LABORATORIUM
    // ==========================================
    public function jadwal()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        // Pada kalender/tabel jadwal laboratorium, SEMUA sesi (reguler + insidental) tetap ditampilkan
        $jadwal = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->whereIn("peminjaman.{$statusCol}", ['disetujui', 'dipinjam'])
            ->select('peminjaman.*', 'ruangan.nama_ruangan', 'user.nama as pemohon')
            ->orderBy('peminjaman.tanggal_mulai', 'asc')
            ->get();

        $ruangan = DB::table('ruangan')->orderBy('id_ruangan', 'asc')->get();

        return view('laboran.jadwal', compact('jadwal', 'ruangan'));
    }

    // Tambah Jadwal Reguler (Langsung Disetujui/Mengunci Lab)
    public function tambahJadwalReguler(Request $request)
    {
        $request->validate([
            'id_ruangan'  => 'required',
            'keperluan'   => 'required|string|max:255',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $jamMulai = date('H:i:s', strtotime($request->jam_mulai));
        $jamSelesai = date('H:i:s', strtotime($request->jam_selesai));

        if ($jamSelesai <= $jamMulai) {
            return back()->with('error', 'Jam selesai praktikum harus lebih besar dari jam mulai.')->withInput();
        }

        $waktuMulai = $request->tanggal . ' ' . $jamMulai;
        $waktuSelesai = $request->tanggal . ' ' . $jamSelesai;

        // Cek Bentrok Jadwal di Ruangan yang Sama
        $bentrok = DB::table('peminjaman')
            ->where('id_ruangan', $request->id_ruangan)
            ->whereIn('status_peminjaman', ['disetujui', 'dipinjam'])
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->where('tanggal_mulai', '<', $waktuSelesai)
                  ->where('tanggal_selesai', '>', $waktuMulai);
            })
            ->first();

        if ($bentrok) {
            return back()->with('error', 'Ruangan lab sudah memiliki agenda lain pada jam tersebut!')->withInput();
        }

        $statusCol = $this->getStatusPeminjamanColumn();

        DB::table('peminjaman')->insert([
            'id_user'           => session('user_id'),
            'id_ruangan'        => $request->id_ruangan,
            'tanggal_mulai'     => $waktuMulai,
            'tanggal_selesai'   => $waktuSelesai,
            'keperluan'         => '[REGULER] ' . $request->keperluan,
            $statusCol          => 'disetujui',
            'status_peminjaman' => 'disetujui',
            'created_at'        => now(),
            'updated_at'        => now()
        ]);

        return redirect('/laboran/jadwal')->with('success', 'Jadwal praktikum reguler berhasil ditambahkan dan ruangan resmi terkunci.');
    }

    // ==========================================
    // 7. PROFIL PRIBADI LABORAN
    // ==========================================
    public function profil()
    {
        $user = DB::table('user')->where('id_user', session('user_id'))->first();
        return view('laboran.profil', compact('user'));
    }
}