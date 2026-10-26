<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return null;
    }

    // Helper: Deteksi kolom tanggal selesai / kembali
    private function getTanggalSelesaiColumn()
    {
        if (Schema::hasColumn('peminjaman', 'tanggal_selesai')) {
            return 'tanggal_selesai';
        } elseif (Schema::hasColumn('peminjaman', 'tanggal_kembali')) {
            return 'tanggal_kembali';
        } elseif (Schema::hasColumn('peminjaman', 'tgl_kembali')) {
            return 'tgl_kembali';
        }
        return null;
    }

    // Helper: Deteksi kolom stok tabel alat
    private function getStokAlatColumn()
    {
        if (Schema::hasColumn('alat', 'stok')) {
            return 'stok';
        } elseif (Schema::hasColumn('alat', 'jumlah')) {
            return 'jumlah';
        } elseif (Schema::hasColumn('alat', 'stok_alat')) {
            return 'stok_alat';
        } elseif (Schema::hasColumn('alat', 'jumlah_alat')) {
            return 'jumlah_alat';
        }
        return null;
    }

    // Dashboard Laboran
    public function dashboard()
    {
        $statusCol = $this->getStatusPeminjamanColumn();
        $tglSelesaiCol = $this->getTanggalSelesaiColumn();

        if ($statusCol) {
            $jatuhTempoQuery = DB::table('peminjaman')->where($statusCol, 'dipinjam');
            if ($tglSelesaiCol) {
                $jatuhTempoQuery->whereDate($tglSelesaiCol, now()->toDateString());
            }

            $statistik = (object)[
                'menunggu_verifikasi'  => DB::table('peminjaman')->where($statusCol, 'menunggu')->count(),
                'siap_serah_terima'    => DB::table('peminjaman')->where($statusCol, 'disetujui')->count(),
                'sedang_dipinjam'      => DB::table('peminjaman')->where($statusCol, 'dipinjam')->count(),
                'jatuh_tempo_hari_ini' => $jatuhTempoQuery->count()
            ];

            $antrian_verifikasi = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where("peminjaman.{$statusCol}", 'menunggu')
                ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
                ->latest('peminjaman.created_at')
                ->limit(5)
                ->get();

            $pengembalianQuery = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where("peminjaman.{$statusCol}", 'dipinjam');

            if ($tglSelesaiCol) {
                $pengembalianQuery->whereDate("peminjaman.{$tglSelesaiCol}", now()->toDateString());
            }

            $pengembalian_hari_ini = $pengembalianQuery
                ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
                ->get();
        } else {
            $statistik = (object)[
                'menunggu_verifikasi'  => 0,
                'siap_serah_terima'    => 0,
                'sedang_dipinjam'      => 0,
                'jatuh_tempo_hari_ini' => 0
            ];
            $antrian_verifikasi = collect();
            $pengembalian_hari_ini = collect();
        }

        return view('laboran.dashboard', compact('statistik', 'antrian_verifikasi', 'pengembalian_hari_ini'));
    }

    // Antrean Verifikasi
    public function verifikasi()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        if ($statusCol) {
            $antrian = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where("peminjaman.{$statusCol}", 'menunggu')
                ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
                ->orderBy('peminjaman.created_at', 'asc')
                ->get();
        } else {
            $antrian = collect();
        }

        return view('laboran.verifikasi', compact('antrian'));
    }

    // Detail Verifikasi
    public function detailVerifikasi($id)
    {
        $detail = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_peminjaman', $id)
            ->select('peminjaman.*', 'user.nama as pemohon', 'user.email', 'ruangan.nama_ruangan', 'ruangan.fasilitas')
            ->first();

        $alat = [];
        if (Schema::hasTable('detail_peminjaman')) {
            $alat = DB::table('detail_peminjaman')
                ->leftJoin('alat', 'detail_peminjaman.id_alat', '=', 'alat.id_alat')
                ->where('detail_peminjaman.id_peminjaman', $id)
                ->select('detail_peminjaman.*', 'alat.nama_alat')
                ->get();
        }

        return view('laboran.detail_verifikasi', compact('detail', 'alat'));
    }

    // Eksekusi Persetujuan / Penolakan
    public function prosesVerifikasi(Request $request, $id)
    {
        $statusCol = $this->getStatusPeminjamanColumn() ?? 'status';
        $aksi = $request->input('aksi');
        $alasan = $request->input('alasan');

        if ($aksi === 'setujui') {
            DB::table('peminjaman')->where('id_peminjaman', $id)->update([
                $statusCol   => 'disetujui',
                'updated_at' => now()
            ]);
            return redirect('/laboran/verifikasi')->with('success', "Pengajuan ID #$id berhasil disetujui.");
        } else {
            $updateData = [
                $statusCol   => 'ditolak',
                'updated_at' => now()
            ];

            if (Schema::hasColumn('peminjaman', 'catatan_penolakan')) {
                $updateData['catatan_penolakan'] = $alasan;
            } elseif (Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $updateData['alasan_penolakan'] = $alasan;
            }

            DB::table('peminjaman')->where('id_peminjaman', $id)->update($updateData);

            return redirect('/laboran/verifikasi')->with('warning', "Pengajuan ID #$id ditolak. Alasan: $alasan");
        }
    }

    // Status Peminjaman (Serah-Terima)
    public function peminjaman()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        if ($statusCol) {
            $peminjaman = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->whereIn("peminjaman.{$statusCol}", ['disetujui', 'dipinjam'])
                ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
                ->get();
        } else {
            $peminjaman = collect();
        }

        return view('laboran.peminjaman', compact('peminjaman'));
    }

    // Eksekusi Serah-Terima
    public function prosesSerahTerima(Request $request, $id)
    {
        $statusCol = $this->getStatusPeminjamanColumn() ?? 'status';

        DB::table('peminjaman')->where('id_peminjaman', $id)->update([
            $statusCol   => 'dipinjam',
            'updated_at' => now()
        ]);

        return redirect('/laboran/peminjaman')->with('success', "Kunci dan fasilitas untuk ID #$id berhasil diserahkan (Status: Dipinjam).");
    }

    // Pengembalian Fasilitas
    public function pengembalian()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        if ($statusCol) {
            $pengembalian = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where("peminjaman.{$statusCol}", 'dipinjam')
                ->select('peminjaman.*', 'user.nama as pemohon', 'ruangan.nama_ruangan')
                ->get();
        } else {
            $pengembalian = collect();
        }

        return view('laboran.pengembalian', compact('pengembalian'));
    }

    // Eksekusi Pengembalian Selesai
    public function prosesPengembalian(Request $request, $id)
    {
        $statusCol = $this->getStatusPeminjamanColumn() ?? 'status';

        DB::table('peminjaman')->where('id_peminjaman', $id)->update([
            $statusCol   => 'selesai',
            'updated_at' => now()
        ]);

        return redirect('/laboran/pengembalian')->with('success', "Pemeriksaan fasilitas ID #$id selesai. Status pengembalian telah ditutup.");
    }

    // Data Peralatan
    public function peralatan()
    {
        $alat = DB::table('alat')->get();
        return view('laboran.peralatan', compact('alat'));
    }

    public function tambahPeralatan(Request $request)
    {
        $stokCol = $this->getStokAlatColumn() ?? 'stok';

        $dataAlat = [
            'nama_alat'  => $request->nama_alat,
            $stokCol     => $request->stok ?? 1,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if (Schema::hasColumn('alat', 'kondisi')) {
            $dataAlat['kondisi'] = $request->kondisi ?? 'baik';
        }

        DB::table('alat')->insert($dataAlat);

        return redirect('/laboran/peralatan')->with('success', 'Data alat praktikum baru berhasil ditambahkan.');
    }

    public function updatePeralatan(Request $request, $id)
    {
        $stokCol = $this->getStokAlatColumn() ?? 'stok';

        $updateData = [
            'nama_alat'  => $request->nama_alat,
            $stokCol     => $request->stok,
            'updated_at' => now()
        ];

        if (Schema::hasColumn('alat', 'kondisi')) {
            $updateData['kondisi'] = $request->kondisi;
        }

        DB::table('alat')->where('id_alat', $id)->update($updateData);

        return redirect('/laboran/peralatan')->with('success', "Data alat berhasil diperbarui.");
    }

    // Jadwal
    public function jadwal()
    {
        $statusCol = $this->getStatusPeminjamanColumn();

        if ($statusCol) {
            $jadwal = DB::table('peminjaman')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->whereIn("peminjaman.{$statusCol}", ['disetujui', 'dipinjam'])
                ->select('peminjaman.*', 'ruangan.nama_ruangan', 'user.nama as pemohon')
                ->get();
        } else {
            $jadwal = collect();
        }

        return view('laboran.jadwal', compact('jadwal'));
    }

    // Profil Laboran
    public function profil()
    {
        $user = DB::table('user')->where('id_user', session('user_id'))->first();
        return view('laboran.profil', compact('user'));
    }
}