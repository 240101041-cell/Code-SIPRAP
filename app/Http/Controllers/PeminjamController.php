<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PeminjamController extends Controller
{
    // Halaman Login
    public function login()
    {
        return view('auth.login');
    }

    // Verifikasi Login
    public function loginPost(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = DB::table('user')->where('username', $request->username)->first();

        if ($user) {
            $isPasswordValid = false;

            if (password_get_info($user->password)['algoName'] !== 'unknown') {
                $isPasswordValid = Hash::check($request->password, $user->password);
            } else {
                $isPasswordValid = ($request->password === $user->password);
            }

            if ($isPasswordValid) {
                session([
                    'user_id' => $user->id_user,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'role' => $user->role,
                    'is_logged_in' => true
                ]);

                if ($user->role === 'laboran') {
                    return redirect('/laboran/dashboard');
                } elseif ($user->role === 'admin') {
                    return redirect('/admin/dashboard');
                } else {
                    return redirect('/peminjam/dashboard');
                }
            }
        }

        return back()->with('error', 'Username atau password salah! Silakan coba lagi.')->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/login');
    }

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

    // Helper: Deteksi kolom stok/jumlah pada tabel alat
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

    // Dashboard Peminjam
    public function dashboard()
    {
        $userId = session('user_id');
        $statusCol = $this->getStatusPeminjamanColumn();

        if ($statusCol) {
            $statistik = (object)[
                'total_pinjam' => DB::table('peminjaman')->where('id_user', $userId)->count(),
                'menunggu'     => DB::table('peminjaman')->where('id_user', $userId)->where($statusCol, 'menunggu')->count(),
                'disetujui'    => DB::table('peminjaman')->where('id_user', $userId)->where($statusCol, 'disetujui')->count(),
                'dipinjam'     => DB::table('peminjaman')->where('id_user', $userId)->where($statusCol, 'dipinjam')->count(),
                'selesai'      => DB::table('peminjaman')->where('id_user', $userId)->where($statusCol, 'selesai')->count(),
            ];

            $peminjaman_aktif = DB::table('peminjaman')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where('peminjaman.id_user', $userId)
                ->whereIn("peminjaman.{$statusCol}", ['menunggu', 'disetujui', 'dipinjam'])
                ->select('peminjaman.*', 'ruangan.nama_ruangan')
                ->get();
        } else {
            $statistik = (object)[
                'total_pinjam' => DB::table('peminjaman')->where('id_user', $userId)->count(),
                'menunggu'     => 0,
                'disetujui'    => 0,
                'dipinjam'     => 0,
                'selesai'      => 0,
            ];

            $peminjaman_aktif = DB::table('peminjaman')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where('peminjaman.id_user', $userId)
                ->select('peminjaman.*', 'ruangan.nama_ruangan')
                ->get();
        }

        return view('peminjam.dashboard', compact('statistik', 'peminjaman_aktif'));
    }

    // Cek Ketersediaan
    public function ketersediaan()
    {
        $ruangan = DB::table('ruangan')->get();
        $alat = DB::table('alat')->get();

        return view('peminjam.ketersediaan', compact('ruangan', 'alat'));
    }

    // Form Pengajuan
    public function pengajuan()
    {
        $statusRuanganCol = Schema::hasColumn('ruangan', 'status_ruangan') ? 'status_ruangan' : (Schema::hasColumn('ruangan', 'status') ? 'status' : null);

        if ($statusRuanganCol) {
            $ruangan = DB::table('ruangan')->where($statusRuanganCol, 'tersedia')->get();
        } else {
            $ruangan = DB::table('ruangan')->get();
        }

        $stokCol = $this->getStokAlatColumn();
        if ($stokCol) {
            $alat = DB::table('alat')->where($stokCol, '>', 0)->get();
        } else {
            $alat = DB::table('alat')->get();
        }

        return view('peminjam.pengajuan', compact('ruangan', 'alat'));
    }

    // Simpan Pengajuan
    public function simpanPengajuan(Request $request)
    {
        $statusCol = $this->getStatusPeminjamanColumn() ?? 'status';

        $dataPeminjaman = [
            'id_user'    => session('user_id'),
            'id_ruangan' => $request->id_ruangan,
            $statusCol   => 'menunggu',
            'created_at' => now(),
            'updated_at' => now()
        ];

        if (Schema::hasColumn('peminjaman', 'tanggal_mulai')) {
            $dataPeminjaman['tanggal_mulai'] = $request->tanggal_mulai;
            $dataPeminjaman['tanggal_selesai'] = $request->tanggal_selesai;
        } elseif (Schema::hasColumn('peminjaman', 'tanggal_pinjam')) {
            $dataPeminjaman['tanggal_pinjam'] = $request->tanggal_mulai;
            $dataPeminjaman['tanggal_kembali'] = $request->tanggal_selesai;
        }

        if (Schema::hasColumn('peminjaman', 'keperluan')) {
            $dataPeminjaman['keperluan'] = $request->keperluan;
        }

        $idPeminjaman = DB::table('peminjaman')->insertGetId($dataPeminjaman);

        if ($request->filled('id_alat') && Schema::hasTable('detail_peminjaman')) {
            $detailData = [
                'id_peminjaman' => $idPeminjaman,
                'id_alat'       => $request->id_alat,
                'created_at'    => now(),
                'updated_at'    => now()
            ];

            if (Schema::hasColumn('detail_peminjaman', 'jumlah')) {
                $detailData['jumlah'] = $request->jumlah_alat ?? 1;
            } elseif (Schema::hasColumn('detail_peminjaman', 'jumlah_alat')) {
                $detailData['jumlah_alat'] = $request->jumlah_alat ?? 1;
            }

            DB::table('detail_peminjaman')->insert($detailData);
        }

        return redirect('/peminjam/riwayat')->with('success', 'Pengajuan peminjaman berhasil dibuat dan sedang menunggu verifikasi.');
    }

    // Riwayat Pengajuan
    public function riwayat()
    {
        $userId = session('user_id');

        $riwayat = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_user', $userId)
            ->select('peminjaman.*', 'ruangan.nama_ruangan')
            ->get();

        return view('peminjam.riwayat', compact('riwayat'));
    }

    // Detail Pengajuan
    public function detail($id)
    {
        $detail = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->where('peminjaman.id_peminjaman', $id)
            ->select('peminjaman.*', 'ruangan.nama_ruangan', 'user.nama as pemohon')
            ->first();

        $alat = [];
        if (Schema::hasTable('detail_peminjaman')) {
            $alat = DB::table('detail_peminjaman')
                ->leftJoin('alat', 'detail_peminjaman.id_alat', '=', 'alat.id_alat')
                ->where('detail_peminjaman.id_peminjaman', $id)
                ->select('detail_peminjaman.*', 'alat.nama_alat')
                ->get();
        }

        return view('peminjam.detail', compact('detail', 'alat'));
    }

    // Pusat Notifikasi
    public function notifikasi()
    {
        $userId = session('user_id');

        $notifikasi = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_user', $userId)
            ->select('peminjaman.*', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.updated_at', 'desc')
            ->get();

        return view('peminjam.notifikasi', compact('notifikasi'));
    }

    // Halaman Profil Saya
    public function profil()
    {
        $userId = session('user_id');
        $user = DB::table('user')->where('id_user', $userId)->first();

        return view('peminjam.profil', compact('user'));
    }
}