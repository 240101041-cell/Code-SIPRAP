<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPRAP - Laboran / Teknisi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; overflow-x: hidden; }

        .sidebar {
            width: 260px; min-height: 100vh;
            background-color: #0b1f44; color: #ffffff;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 24px 16px; z-index: 1000;
        }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 0 8px 16px 8px; }
        .badge-logo {
            width: 40px; height: 40px;
            background: #ffffff; color: #0b1f44;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.9rem;
        }
        .nav-link-siprap {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; color: #94a3b8;
            font-weight: 600; font-size: 0.9rem;
            text-decoration: none; border-radius: 12px;
            transition: all 0.2s; margin-bottom: 4px;
        }
        .nav-link-siprap:hover { color: #ffffff; background: rgba(255, 255, 255, 0.06); }
        .nav-link-siprap.active { background-color: #2563eb !important; color: #ffffff !important; }

        .main-wrapper { margin-left: 260px; min-height: 100vh; padding: 32px 40px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .user-pill { display: flex; align-items: center; gap: 12px; }
        .avatar-circle {
            width: 40px; height: 40px; border-radius: 50%;
            background: #e0f2fe; color: #0284c7;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
        }
        .card-custom {
            background: #ffffff; border: 1px solid #e2e8f0;
            border-radius: 16px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="badge-logo">UDB</div>
                <div>
                    <h6 class="m-0 fw-bold text-white">SIPRAP</h6>
                    <small style="color: #64748b; font-size: 0.75rem;">Peminjaman Lab</small>
                </div>
            </div>
            <div class="px-2 mb-3">
                <span class="badge rounded-pill px-3 py-1" style="background: rgba(255,255,255,0.08); color: #cbd5e1; font-size: 0.75rem;">
                    Laboran / Teknisi
                </span>
            </div>

            <nav class="mt-3">
                <a href="/laboran/dashboard" class="nav-link-siprap {{ Request::is('laboran/dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <a href="/laboran/verifikasi" class="nav-link-siprap {{ Request::is('laboran/verifikasi*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Verifikasi Pengajuan
                </a>
                <a href="/laboran/peminjaman" class="nav-link-siprap {{ Request::is('laboran/peminjaman*') ? 'active' : '' }}">
                    <i class="bi bi-hand-index"></i> Status Peminjaman
                </a>
                <a href="/laboran/pengembalian" class="nav-link-siprap {{ Request::is('laboran/pengembalian*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-counterclockwise"></i> Pengembalian
                </a>
                <a href="/laboran/peralatan" class="nav-link-siprap {{ Request::is('laboran/peralatan*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Data Peralatan
                </a>
                <a href="/laboran/jadwal" class="nav-link-siprap {{ Request::is('laboran/jadwal*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Jadwal
                </a>
                <a href="/laboran/profil" class="nav-link-siprap {{ Request::is('laboran/profil*') ? 'active' : '' }}">
                    <i class="bi bi-person"></i> Profil
                </a>
            </nav>
        </div>

        <div>
            <a href="/logout" class="nav-link-siprap text-danger">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="topbar">
            <div>
                <small class="text-secondary">@yield('breadcrumb', 'Laboran / Dashboard')</small>
            </div>
            <div class="user-pill">
                <div class="avatar-circle">RW</div>
                <div>
                    <h6 class="m-0 fw-bold text-dark" style="font-size: 0.88rem;">{{ session('nama', 'Rina Wulandari') }}</h6>
                    <small class="text-secondary" style="font-size: 0.75rem;">Laboran / Teknisi</small>
                </div>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>