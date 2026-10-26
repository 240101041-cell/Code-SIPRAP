<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPRAP - Peminjaman Lab')</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; overflow-x: hidden; }

        /* SIDEBAR NAVY GELAP FIGMA #0b1f44 */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #0b1f44;
            color: #ffffff;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 16px;
            z-index: 1000;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 16px 8px;
        }
        .badge-logo {
            width: 40px; height: 40px;
            background: #ffffff; color: #0b1f44;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.9rem;
        }
        .nav-link-siprap {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.2s;
            margin-bottom: 4px;
        }
        .nav-link-siprap:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }
        .nav-link-siprap.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
        }

        /* MAIN WRAPPER */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            padding: 32px 40px;
        }

        /* TOPBAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .avatar-circle {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #e0e7ff; color: #2563eb;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
        }

        /* CARD & ELEMENT STYLES */
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        .btn-primary-siprap {
            background-color: #2563eb;
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
        }
        .btn-primary-siprap:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR PERSIS FIGMA SILSILA -->
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
                    {{ session('role_label', 'Peminjam · Mahasiswa') }}
                </span>
            </div>

            <nav class="mt-3">
                <a href="/peminjam/dashboard" class="nav-link-siprap {{ Request::is('peminjam/dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="/peminjam/ketersediaan" class="nav-link-siprap {{ Request::is('peminjam/ketersediaan*') ? 'active' : '' }}">
                    <i class="bi bi-calendar3"></i> Cek Ketersediaan
                </a>
                <a href="/peminjam/pengajuan" class="nav-link-siprap {{ Request::is('peminjam/pengajuan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-plus"></i> Pengajuan Peminjaman
                </a>
                <a href="/peminjam/riwayat" class="nav-link-siprap {{ Request::is('peminjam/riwayat*') || Request::is('peminjam/detail*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Status Pengajuan
                </a>
                <a href="/peminjam/notifikasi" class="nav-link-siprap {{ Request::is('peminjam/notifikasi*') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i> Notifikasi
                </a>
                <a href="/peminjam/profil" class="nav-link-siprap {{ Request::is('peminjam/profil*') ? 'active' : '' }}">
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

    <!-- MAIN CONTENT AREA -->
    <div class="main-wrapper">
        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <small class="text-secondary">@yield('breadcrumb', 'Peminjam / Dashboard')</small>
            </div>
            <div class="user-pill">
                <a href="/peminjam/notifikasi" class="btn btn-light rounded-circle border shadow-2xs position-relative me-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-bell text-secondary"></i>
                </a>
                <div class="avatar-circle">{{ session('inisial', 'AP') }}</div>
                <div>
                    <h6 class="m-0 fw-bold text-dark" style="font-size: 0.88rem;">{{ session('nama', 'Andi Pratama') }}</h6>
                    <small class="text-secondary" style="font-size: 0.75rem;">{{ session('role_label', 'Peminjam · Mahasiswa') }}</small>
                </div>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>