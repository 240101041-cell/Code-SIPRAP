<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PeminjamController extends Controller
{
    // ==========================================
    // 1. AUTENTIKASI: LOGIN & LOGOUT
    // ==========================================

    // Halaman Form Login
    public function login()
    {
        if (session('is_logged_in')) {
            return $this->redirectRole(session('role'));
        }
        return view('auth.login');
    }

    // Proses Verifikasi Kredensial Login
    public function loginPost(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = DB::table('user')->where('username', $request->username)->first();

        if ($user) {
            // Cek status akun terlebih dahulu
            if (isset($user->status_akun) && $user->status_akun === 'nonaktif') {
                return back()->with('error', 'Akun Anda sedang dinonaktifkan oleh Administrator.')->withInput();
            }

            // Fleksibilitas verifikasi password (Bcrypt / Plain string)
            $isPasswordValid = false;
            if (password_get_info($user->password)['algoName'] !== 'unknown') {
                $isPasswordValid = Hash::check($request->password, $user->password);
            } else {
                $isPasswordValid = ($request->password === $user->password);
            }

            if ($isPasswordValid) {
                session([
                    'user_id'      => $user->id_user,
                    'nama'         => $user->nama,
                    'username'     => $user->username,
                    'role'         => $user->role,
                    'is_logged_in' => true
                ]);

                return $this->redirectRole($user->role);
            }
        }

        return back()->with('error', 'Username atau password salah! Silakan coba lagi.')->withInput();
    }

    // Helper Redirect Berdasarkan Hak Akses (Role)
    private function redirectRole($role)
    {
        if ($role === 'laboran') {
            return redirect('/laboran/dashboard');
        } elseif ($role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/peminjam/dashboard');
    }

    // Logout dan Hapus Sesi
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/login');
    }

    // ==========================================
    // 2. DASHBOARD PEMINJAM
    // ==========================================
    public function dashboard()
    {
        $userId = session('user_id');

        // Statistik ringkasan pengajuan milik pengguna (Kecualikan jadwal reguler)
        $statistik = (object)[
            'total_pinjam' => DB::table('peminjaman')
                ->where('id_user', $userId)
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->count(),
            'menunggu'     => DB::table('peminjaman')
                ->where('id_user', $userId)
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->where('status_peminjaman', 'menunggu')
                ->count(),
            'disetujui'    => DB::table('peminjaman')
                ->where('id_user', $userId)
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->where('status_peminjaman', 'disetujui')
                ->count(),
            'dipinjam'     => DB::table('peminjaman')
                ->where('id_user', $userId)
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->where('status_peminjaman', 'dipinjam')
                ->count(),
            'selesai'      => DB::table('peminjaman')
                ->where('id_user', $userId)
                ->where('keperluan', 'NOT LIKE', '[REGULER]%')
                ->whereIn('status_peminjaman', ['selesai', 'dikembalikan'])
                ->count(),
        ];

        // Daftar peminjaman aktif yang sedang berjalan (Kecualikan jadwal reguler)
        $peminjaman_aktif = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_user', $userId)
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->whereIn('peminjaman.status_peminjaman', ['menunggu', 'disetujui', 'dipinjam'])
            ->select('peminjaman.*', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'desc')
            ->get();

        return view('peminjam.dashboard', compact('statistik', 'peminjaman_aktif'));
    }

    // ==========================================
    // 3. CEK KETERSEDIAAN RUANGAN & ALAT
    // ==========================================
    public function ketersediaan()
    {
        $ruangan = DB::table('ruangan')->orderBy('id_ruangan', 'asc')->get();
        $alat = DB::table('alat')->orderBy('id_alat', 'asc')->get();

        return view('peminjam.ketersediaan', compact('ruangan', 'alat'));
    }

    // ==========================================
    // 4. FORM & PROSES PENGAJUAN (F4 & F5)
    // ==========================================
    public function pengajuan()
    {
        // Hanya ambil ruangan yang berstatus tersedia
        $ruangan = DB::table('ruangan')->where('status', 'tersedia')->get();

        // Ambil alat yang memiliki sisa stok > 0
        $alat = DB::table('alat')->where('stok', '>', 0)->get();

        return view('peminjam.pengajuan', compact('ruangan', 'alat'));
    }

    public function simpanPengajuan(Request $request)
    {
        $request->validate([
            'id_ruangan'   => 'required',
            'tanggal'      => 'required|date',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
            'keperluan'    => 'required|string|max:500',
        ]);

        $tanggal = $request->tanggal;
        $jamMulai = date('H:i:s', strtotime($request->jam_mulai));
        $jamSelesai = date('H:i:s', strtotime($request->jam_selesai));

        // 1. Validasi Jam Operasional Kampus (08:00 - 17:00 WIB)
        if ($jamMulai < '08:00:00' || $jamSelesai > '17:00:00') {
            return back()->with('error', 'Jam peminjaman harus berada dalam jam operasional kampus (08:00 s/d 17:00 WIB).')->withInput();
        }

        // 2. Validasi Urutan Waktu
        if ($jamSelesai <= $jamMulai) {
            return back()->with('error', 'Jam selesai praktikum harus lebih besar dari jam mulai.')->withInput();
        }

        $waktuMulai = $tanggal . ' ' . $jamMulai;
        $waktuSelesai = $tanggal . ' ' . $jamSelesai;

        // 3. Deteksi Bentrok Jadwal Reguler / Peminjaman Lain yang Mengunci Lab
        $bentrok = DB::table('peminjaman')
            ->where('id_ruangan', $request->id_ruangan)
            ->whereIn('status_peminjaman', ['disetujui', 'dipinjam'])
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->where('tanggal_mulai', '<', $waktuSelesai)
                  ->where('tanggal_selesai', '>', $waktuMulai);
            })
            ->first();

        if ($bentrok) {
            return back()->with(
                'error',
                'Laboratorium tidak tersedia pada jam tersebut karena berbenturan dengan agenda lain: ' . $bentrok->keperluan
            )->withInput();
        }

        // 4. Simpan Transaksi Peminjaman Induk
        $idPeminjaman = DB::table('peminjaman')->insertGetId([
            'id_user'           => session('user_id'),
            'id_ruangan'        => $request->id_ruangan,
            'tanggal_mulai'     => $waktuMulai,
            'tanggal_selesai'   => $waktuSelesai,
            'keperluan'         => $request->keperluan,
            'status'            => 'menunggu',
            'status_peminjaman' => 'menunggu',
            'created_at'        => now(),
            'updated_at'        => now()
        ]);

        // 5. Simpan Detail Peralatan Praktikum (Bila meminjam alat)
        if ($request->filled('id_alat') && !empty($request->id_alat)) {
            $jumlah = $request->jumlah_alat ?? 1;

            DB::table('detail_peminjaman')->insert([
                'id_peminjaman' => $idPeminjaman,
                'id_alat'       => $request->id_alat,
                'jumlah'        => $jumlah,
                'jumlah_alat'   => $jumlah,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);
        }

        return redirect('/peminjam/riwayat')->with('success', 'Permohonan peminjaman berhasil dibuat dan masuk antrean verifikasi laboran.');
    }

    // ==========================================
    // 5. RIWAYAT, DETAIL, & PEMBATALAN
    // ==========================================
    public function riwayat()
    {
        $userId = session('user_id');

        // Riwayat pengajuan milik peminjam (Kecualikan jadwal reguler)
        $riwayat = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_user', $userId)
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.created_at', 'desc')
            ->get();

        return view('peminjam.riwayat', compact('riwayat'));
    }

    public function detail($id = null)
    {
        if (!$id) {
            return redirect('/peminjam/riwayat');
        }

        $userId = session('user_id');

        $detail = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->leftJoin('user', 'peminjaman.id_user', '=', 'user.id_user')
            ->where('peminjaman.id_peminjaman', $id)
            ->where('peminjaman.id_user', $userId)
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'ruangan.nama_ruangan', 'user.nama as pemohon')
            ->first();

        if (!$detail) {
            return redirect('/peminjam/riwayat')->with('error', 'Data pengajuan peminjaman tidak ditemukan.');
        }

        // Ambil rincian unit alat praktikum
        $alat = DB::table('detail_peminjaman')
            ->leftJoin('alat', 'detail_peminjaman.id_alat', '=', 'alat.id_alat')
            ->where('detail_peminjaman.id_peminjaman', $id)
            ->select('detail_peminjaman.*', 'alat.nama_alat')
            ->get();

        return view('peminjam.detail', compact('detail', 'alat'));
    }

    // Pembatalan Pengajuan Mandiri oleh Peminjam (F4-06 / F5-06)
    public function batalPengajuan($id)
    {
        $userId = session('user_id');

        $peminjaman = DB::table('peminjaman')
            ->where('id_peminjaman', $id)
            ->where('id_user', $userId)
            ->first();

        if ($peminjaman && ($peminjaman->status_peminjaman === 'menunggu' || $peminjaman->status === 'menunggu')) {
            DB::table('peminjaman')->where('id_peminjaman', $id)->update([
                'status'            => 'ditolak',
                'status_peminjaman' => 'ditolak',
                'alasan_penolakan'  => 'Dibatalkan langsung oleh peminjam.',
                'catatan_penolakan' => 'Dibatalkan langsung oleh peminjam.',
                'updated_at'        => now()
            ]);

            return redirect('/peminjam/riwayat')->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
        }

        return redirect('/peminjam/riwayat')->with('error', 'Pengajuan tidak dapat dibatalkan karena sudah diproses oleh petugas.');
    }

    // ==========================================
    // 6. NOTIFIKASI & PENGELOLAAN PROFIL
    // ==========================================
    public function notifikasi()
    {
        $userId = session('user_id');

        $notifikasi = DB::table('peminjaman')
            ->leftJoin('ruangan', 'peminjaman.id_ruangan', '=', 'ruangan.id_ruangan')
            ->where('peminjaman.id_user', $userId)
            ->where('peminjaman.keperluan', 'NOT LIKE', '[REGULER]%')
            ->select('peminjaman.*', 'ruangan.nama_ruangan')
            ->orderBy('peminjaman.updated_at', 'desc')
            ->get();

        return view('peminjam.notifikasi', compact('notifikasi'));
    }

    public function profil()
    {
        $userId = session('user_id');
        $user = DB::table('user')->where('id_user', $userId)->first();

        return view('peminjam.profil', compact('user'));
    }

    // Simpan Pembaruan Profil Mandiri (F1-04)
    public function updateProfil(Request $request)
    {
        $userId = session('user_id');

        $request->validate([
            'nama'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:user,email,' . $userId . ',id_user'
        ]);

        $updateData = [
            'nama'       => $request->nama,
            'email'      => $request->email,
            'updated_at' => now()
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        DB::table('user')->where('id_user', $userId)->update($updateData);

        // Update nama di sesi login aktif
        session(['nama' => $request->nama]);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    // ==========================================
    // 7. API CEK KETERSEDIAAN SLOT WAKTU (F2 / F3)
    // ==========================================
    public function cekSlotWaktu(Request $request)
    {
        $idRuangan = $request->id_ruangan;
        $tanggal   = $request->tanggal;

        if (!$idRuangan || !$tanggal) {
            return response()->json(['slots' => []]);
        }

        // Definisi slot operasional kampus (08:00 - 17:00 WIB)
        $daftarSlot = [
            ['sesi' => 'Sesi 1', 'mulai' => '08:00', 'selesai' => '10:00'],
            ['sesi' => 'Sesi 2', 'mulai' => '10:00', 'selesai' => '12:00'],
            ['sesi' => 'Sesi 3', 'mulai' => '13:00', 'selesai' => '15:00'],
            ['sesi' => 'Sesi 4', 'mulai' => '15:00', 'selesai' => '17:00'],
        ];

        // Jadwal peminjaman dan jadwal reguler tetap dicek untuk mengunci ketersediaan slot
        $booked = DB::table('peminjaman')
            ->where('id_ruangan', $idRuangan)
            ->whereIn('status_peminjaman', ['disetujui', 'dipinjam'])
            ->whereDate('tanggal_mulai', $tanggal)
            ->get();

        $hasilSlot = [];
        foreach ($daftarSlot as $slot) {
            $waktuSlotMulai   = $tanggal . ' ' . $slot['mulai'] . ':00';
            $waktuSlotSelesai = $tanggal . ' ' . $slot['selesai'] . ':00';

            // Cek apakah slot ini tumpang tindih dengan jadwal di database
            $bentrok = $booked->first(function ($b) use ($waktuSlotMulai, $waktuSlotSelesai) {
                return ($b->tanggal_mulai < $waktuSlotSelesai) && ($b->tanggal_selesai > $waktuSlotMulai);
            });

            $hasilSlot[] = [
                'sesi'      => $slot['sesi'],
                'mulai'     => $slot['mulai'],
                'selesai'   => $slot['selesai'],
                'tersedia'  => $bentrok ? false : true,
                'agenda'    => $bentrok ? $bentrok->keperluan : 'Tersedia'
            ];
        }

        return response()->json(['slots' => $hasilSlot]);
    }
}