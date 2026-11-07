<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    private function getStatusPeminjamanColumn()
    {
        if (Schema::hasColumn('peminjaman', 'status_peminjaman')) {
            return 'status_peminjaman';
        } elseif (Schema::hasColumn('peminjaman', 'status')) {
            return 'status';
        }
        return 'status';
    }

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

    // ==========================================
    // 1. DASHBOARD ADMIN
    // ==========================================
    public function dashboard()
    {
        $statusCol = $this->getStatusPeminjamanColumn();
        $statusSanksiCol = $this->getStatusSanksiColumn();

        // Total peminjaman bulan ini khusus peminjaman mandiri (bukan kelas reguler)
        $totalPeminjamanBulanIni = DB::table('peminjaman')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('keperluan', 'NOT LIKE', '[REGULER]%')
            ->count();

        $totalRuangan = DB::table('ruangan')->count();
        $totalPengguna = DB::table('user')->count();

        if ($statusCol) {
            $sedangDipinjam = DB::table('peminjaman')
                ->where($statusCol, 'dipinjam')
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->count();

            $ruanganTerpakai = DB::table('peminjaman')
                ->where($statusCol, 'dipinjam')
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->distinct('id_ruangan')
                ->count('id_ruangan');

            $ringkasanStatus = (object)[
                'menunggu'  => DB::table('peminjaman')
                    ->where($statusCol, 'menunggu')
                    ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                    ->count(),
                'disetujui' => DB::table('peminjaman')
                    ->where($statusCol, 'disetujui')
                    ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                    ->count(),
                'dipinjam'  => DB::table('peminjaman')
                    ->where($statusCol, 'dipinjam')
                    ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                    ->count(),
                'selesai'   => DB::table('peminjaman')
                    ->whereIn($statusCol, ['selesai', 'dikembalikan'])
                    ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                    ->count(),
                'ditolak'   => DB::table('peminjaman')
                    ->whereIn($statusCol, ['ditolak', 'dibatalkan'])
                    ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                    ->count(),
            ];

            // Aktivitas terbaru khusus pengajuan mandiri
            $aktivitasTerbaru = DB::table('peminjaman')
                ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
                ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
                ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
                ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
                ->latest('peminjaman.created_at')
                ->limit(5)
                ->get();
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
            $aktivitasTerbaru = collect();
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
            'totalPengguna',
            'sanksiAktif',
            'ringkasanStatus',
            'aktivitasTerbaru'
        ));
    }

    // ==========================================
    // 2. KELOLA PENGGUNA (USER)
    // ==========================================
    public function pengguna(Request $request)
    {
        $search = $request->query('q');

        $users = DB::table('user')
            ->when($search, function ($query, $search) {
                return $query->where('nama', 'like', "%{$search}%")
                             ->orWhere('username', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('id_user', 'asc')
            ->get();

        return view('admin.pengguna', compact('users', 'search'));
    }

    public function tambahPengguna(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:user,username',
            'email'    => 'required|email|max:150|unique:user,email',
            'password' => 'required|min:4',
            'role'     => 'required|in:peminjam,laboran,admin'
        ]);

        DB::table('user')->insert([
            'nama'        => $request->nama,
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => $request->password,
            'role'        => $request->role,
            'status_akun' => 'aktif',
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect('/admin/pengguna')->with('success', 'Akun pengguna baru berhasil ditambahkan.');
    }

    public function updatePengguna(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:user,email,' . $id . ',id_user',
            'role'  => 'required|in:peminjam,laboran,admin'
        ]);

        $updateData = [
            'nama'       => $request->nama,
            'email'      => $request->email,
            'role'       => $request->role,
            'updated_at' => now()
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        DB::table('user')->where('id_user', $id)->update($updateData);

        return redirect('/admin/pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function updateStatusPengguna($id)
    {
        $user = DB::table('user')->where('id_user', $id)->first();
        if ($user) {
            $newStatus = ($user->status_akun === 'aktif') ? 'nonaktif' : 'aktif';

            DB::table('user')->where('id_user', $id)->update([
                'status_akun' => $newStatus,
                'updated_at'  => now()
            ]);
        }

        return redirect('/admin/pengguna')->with('success', 'Status akun berhasil diubah.');
    }

    public function hapusPengguna($id)
    {
        if ($id == session('user_id')) {
            return redirect('/admin/pengguna')->with('error', 'Tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        DB::table('user')->where('id_user', $id)->delete();

        return redirect('/admin/pengguna')->with('success', 'Akun pengguna berhasil dihapus dari sistem.');
    }

    // ==========================================
    // 3. KELOLA DATA RUANGAN
    // ==========================================
    public function ruangan(Request $request)
    {
        $search = $request->query('q');

        $ruangan = DB::table('ruangan')
            ->when($search, function ($query, $search) {
                return $query->where('nama_ruangan', 'like', "%{$search}%")
                             ->orWhere('lokasi', 'like', "%{$search}%")
                             ->orWhere('kode_ruangan', 'like', "%{$search}%");
            })
            ->orderBy('id_ruangan', 'asc')
            ->get();

        return view('admin.ruangan', compact('ruangan', 'search'));
    }

    public function tambahRuangan(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas'    => 'required|numeric|min:1',
            'lokasi'       => 'nullable|string|max:100',
            'fasilitas'    => 'nullable|string'
        ]);

        $count = DB::table('ruangan')->count() + 1;
        $kodeRuangan = $request->kode_ruangan ?: 'LAB-0' . $count;

        DB::table('ruangan')->insert([
            'kode_ruangan'   => $kodeRuangan,
            'nama_ruangan'   => $request->nama_ruangan,
            'kapasitas'      => $request->kapasitas,
            'lokasi'         => $request->lokasi ?? 'Gedung B',
            'fasilitas'      => $request->fasilitas ?? '-',
            'status'         => 'tersedia',
            'status_ruangan' => 'tersedia',
            'created_at'     => now(),
            'updated_at'     => now()
        ]);

        return redirect('/admin/ruangan')->with('success', 'Data laboratorium baru berhasil ditambahkan.');
    }

    public function updateRuangan(Request $request, $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas'    => 'required|numeric|min:1',
            'status'       => 'required|in:tersedia,terpakai'
        ]);

        DB::table('ruangan')->where('id_ruangan', $id)->update([
            'kode_ruangan'   => $request->kode_ruangan,
            'nama_ruangan'   => $request->nama_ruangan,
            'kapasitas'      => $request->kapasitas,
            'lokasi'         => $request->lokasi ?? 'Gedung B',
            'fasilitas'      => $request->fasilitas ?? '-',
            'status'         => $request->status,
            'status_ruangan' => $request->status,
            'updated_at'     => now()
        ]);

        return redirect('/admin/ruangan')->with('success', 'Data ruangan laboratorium berhasil diperbarui.');
    }

    public function hapusRuangan($id)
    {
        DB::table('ruangan')->where('id_ruangan', $id)->delete();
        return redirect('/admin/ruangan')->with('success', 'Data ruangan berhasil dihapus.');
    }

    // ==========================================
    // 4. LAPORAN & SANKSI
    // ==========================================
    public function laporan()
    {
        // Filter: Hanya tampilkan peminjaman insidental mandiri pada laporan rekap
        $laporan = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'desc')
            ->get();

        $sanksi = DB::table('sanksi')
            ->leftJoin('user', 'sanksi.id_user', '=', 'user.id_user')
            ->select('sanksi.*', 'user.nama', 'user.username')
            ->get();

        return view('admin.laporan', compact('laporan', 'sanksi'));
    }

    public function pulihkanSanksi($id)
    {
        DB::table('sanksi')->where('id_sanksi', $id)->update([
            'status'        => 'selesai',
            'status_sanksi' => 'selesai',
            'updated_at'    => now()
        ]);

        return redirect('/admin/laporan')->with('success', 'Sanksi telah dipulihkan.');
    }

    // ==========================================
    // 5. PROFIL ADMIN
    // ==========================================
    public function profil()
    {
        $user = DB::table('user')->where('id_user', session('user_id'))->first();
        return view('admin.profil', compact('user'));
    }

    // ==========================================
    // 6. EKSPOR LAPORAN KE EXCEL (.CSV)
    // ==========================================
    public function eksporExcel()
    {
        // Hanya ekspor data permohonan peminjaman mandiri
        $laporan = DB::table('peminjaman')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'user.nama as peminjam', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'desc')
            ->get();

        $filename = "Laporan_Peminjaman_SIPRAP_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($laporan) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID Peminjaman', 'Nama Peminjam', 'Ruangan', 'Waktu Pinjam', 'Keperluan', 'Status']);

            foreach ($laporan as $row) {
                fputcsv($handle, [
                    '#PJ-' . str_pad($row->id_peminjaman, 4, '0', STR_PAD_LEFT),
                    $row->peminjam ?? 'Civitas',
                    $row->nama_ruangan ?? 'Laboratorium',
                    $row->tanggal_mulai ?? ($row->tanggal_peminjaman ?? $row->created_at),
                    $row->keperluan ?? '-',
                    ucfirst($row->status_peminjaman ?? ($row->status ?? 'menunggu'))
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}