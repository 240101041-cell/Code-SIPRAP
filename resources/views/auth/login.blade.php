<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke SIPRAP | Sistem Peminjaman Ruangan & Alat Praktikum</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* Sisi Kiri (Navy) */
        .left-banner {
            background-color: #0c1e3d;
            color: #ffffff;
            width: 56%;
            padding: 48px 64px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-logo-circle {
            width: 48px;
            height: 48px;
            background-color: #ffffff;
            color: #0c1e3d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: -0.5px;
        }

        .hero-title {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: #94a3b8;
            max-width: 520px;
            line-height: 1.6;
        }

        .footer-note {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Sisi Kanan (Formulir Login) */
        .right-form-pane {
            width: 44%;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 44px 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }

        .card-logo-badge {
            width: 44px;
            height: 44px;
            background-color: #0c1e3d;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .form-control-custom {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 12px 14px 12px 42px;
            font-size: 0.92rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.05rem;
        }

        .btn-toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            padding: 0;
            cursor: pointer;
        }

        .btn-toggle-eye:hover {
            color: #475569;
        }

        .btn-login {
            background-color: #2563eb;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        /* Responsif untuk layar kecil / tablet */
        @media (max-width: 991.98px) {
            .login-wrapper {
                flex-direction: column;
            }
            .left-banner {
                width: 100%;
                padding: 40px 28px;
            }
            .hero-title {
                font-size: 2rem;
            }
            .right-form-pane {
                width: 100%;
                padding: 32px 20px 48px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- BAGIAN KIRI: IDENTITAS SISTEM -->
    <div class="left-banner">
        <!-- Logo & Brand Header -->
        <div class="d-flex align-items-center gap-3">
            <div class="brand-logo-circle">UDB</div>
            <div>
                <h5 class="fw-bold m-0" style="letter-spacing: 0.5px;">SIPRAP</h5>
                <small style="color: #94a3b8; font-size: 0.8rem;">Universitas Duta Bangsa Surakarta</small>
            </div>
        </div>

        <!-- Teks Utama -->
        <div class="my-auto py-5">
            <h1 class="hero-title">
                Peminjaman ruangan dan alat praktikum, lebih tertib dan mudah dipantau.
            </h1>
            <p class="hero-subtitle">
                Cek ketersediaan, ajukan permohonan, pantau status persetujuan, hingga serah-terima pengembalian dalam satu sistem terpusat.
            </p>
        </div>

        <!-- Copyright Footer -->
        <div class="footer-note">
            &copy; 2026 SIPRAP &middot; Universitas Duta Bangsa Surakarta
        </div>
    </div>

    <!-- BAGIAN KANAN: FORMULIR LOGIN -->
    <div class="right-form-pane">
        <div class="login-card">
            <!-- Badge Icon -->
            <div class="card-logo-badge">UDB</div>

            <h4 class="fw-bold text-dark mb-1">Masuk ke SIPRAP</h4>
            <p class="text-secondary small mb-4">Gunakan username dan password yang diberikan Admin.</p>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <!-- Input Username -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Username</label>
                    <div class="position-relative">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="username" class="form-control form-control-custom" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark mb-1">Password</label>
                    <div class="position-relative">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" id="inputPassword" name="password" class="form-control form-control-custom" placeholder="Masukkan password" required>
                        <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility()">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-login w-100">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Script Show/Hide Password -->
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('inputPassword');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>