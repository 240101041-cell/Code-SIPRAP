<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    // Helper: Deteksi kolom status pada tabel peminjaman
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

    // Helper: Deteksi kolom status pada tabel sanksi
    private function getStatusSanksiColumn()
    {
        if (Schema::hasTable('sanksi')) {
            if (Schema::hasColumn('sanksi', 'status_sanksi')) {
                return 'status_sanksi';
            } elseif (Schema::hasColumn('sanksi', 'status')) {
                return 'status';
            }
        }
        return null;
    }

    // Dashboard Admin Real DB
    public function dashboard()
    {
        $statusCol = $this->getStatusPeminjamanColumn();
        $statusSanksiCol = $this->getStatusSanksiColumn();

        $totalPeminjamanBulanIni = DB::table('peminjaman')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalRuangan = DB::table('ruangan')->count();

        if ($statusCol) {
            $sedangDipinjam = DB::table('peminjaman')->where($statusCol, 'dipinjam')->count();
            $ruanganTerpakai = DB::table('peminjaman')->where($statusCol, 'dipinjam')->distinct('id_ruangan')->count('id_ruangan');

            $ringkasanStatus = (object)[
                'menunggu'  => DB::table('peminjaman')->where($statusCol, 'menunggu')->count(),
                'disetujui' => DB::table('peminjaman')->where($statusCol, 'disetujui')->count(),
                'dipinjam'  => DB::table('peminjaman')->where($statusCol, 'dipinjam')->count(),
                'selesai'   => DB::table('peminjaman')->where($statusCol, 'selesai')->count(),
                'ditolak'   => DB::table('peminjaman')->where($statusCol, 'ditolak')->count(),
            ];
        } else {
            $sedangDipinjam = 0;
            $ruanganTerpakai = 0;
            $ringkasanStatus = (object)[
                'menunggu'  => 0,
                'disetujui' => 0,
                'dipinjam'  => 0,
                'selesai'   => 0,
                'ditolak'   => 0,
            ];
        }

        $sanksiAktif = 0;
        if ($statusSanksiCol) {
            $sanksiAktif = DB::table('sanksi')->where($statusSanksiCol, 'aktif')->count();
        } elseif (Schema::hasTable('sanksi')) {
            $sanksiAktif = DB::table('sanksi')->count();
        }

        return view('admin.dashboard', compact(
            'totalPeminjamanBulanIni',
            'sedangDipinjam',
            'totalRuangan',
            'ruanganTerpakai',
            'sanksiAktif',
            'ringkasanStatus'
        ));
    }

    // Kelola Pengguna Real DB
    public function pengguna()
    {
        $users = DB::table('user')->orderBy('id_user', 'asc')->get();
        return view('admin.pengguna', compact('users'));
    }

    public function tambahPengguna(Request $request)
    {
        $request->validate([
            'nama'     => 'required',
            'username' => 'required|unique:user,username',
            'email'    => 'required|email|unique:user,email',
            'password' => 'required',
            'role'     => 'required'
        ]);

        $userData = [
            'nama'        => $request->nama,
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'created_at'  => now(),
            'updated_at'  => now()
        ];

        if (Schema::hasColumn('user', 'status_akun')) {
            $userData['status_akun'] = 'aktif';
        } elseif (Schema::hasColumn('user', 'status')) {
            $userData['status'] = 'aktif';
        }

        DB::table('user')->insert($userData);

        return redirect('/admin/pengguna')->with('success', 'Akun pengguna baru berhasil ditambahkan ke database.');
    }

    public function updateStatusPengguna($id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();
        if ($user) {
            $statusCol = Schema::hasColumn('user', 'status_akun') ? 'status_akun' : (Schema::hasColumn('user', 'status') ? 'status' : null);

            if ($statusCol) {
                $currentStatus = $user->$statusCol ?? 'aktif';
                $newStatus = ($currentStatus === 'aktif') ? 'nonaktif' : 'aktif';

                DB::table('user')->where('id_user', $id)->update([
                    $statusCol   => $newStatus,
                    'updated_at' => now()
                ]);
            }
        }

        return redirect('/admin/pengguna')->with('success', 'Status akun pengguna berhasil diperbarui.');
    }

    // Data Ruangan Real DB
    public function ruangan()
    {
        $ruangan = DB::table('ruangan')->get();
        return view('admin.ruangan', compact('ruangan'));
    }

    // Laporan & Sanksi Real DB
    public function laporan()
    {
        $laporan = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'desc')
            ->get();

        $sanksi = collect();
        if (Schema::hasTable('sanksi')) {
            $sanksi = DB::table('sanksi')
                ->leftJoin('user', 'sanksi.id_user', '=', 'user.id_user')
                ->select('sanksi.*', 'user.nama', 'user.username')
                ->get();
        }

        return view('admin.laporan', compact('laporan', 'sanksi'));
    }

    // Profil Admin Real DB
    public function profil()
    {
        $user = DB::table('user')->where('id_user', session('user_id'))->first();
        return view('admin.profil', compact('user'));
    }
}