<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke SIPRAP | Universitas Duta Bangsa Surakarta</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body, html { height: 100%; margin: 0; background-color: #f8fafc; }
        .login-container { display: flex; min-height: 100vh; }

        /* SISI KIRI: NAVY GELAP FIGMA (#0b1f44) */
        .left-banner {
            flex: 1.1;
            background-color: #0b1f44;
            color: #ffffff;
            padding: 64px 72px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .preview-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .preview-icon {
            width: 38px; height: 38px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: #93c5fd;
        }

        /* SISI KANAN: FORM LOGIN */
        .right-form {
            flex: 0.9;
            background-color: #f8fafc;
            display: flex; align-items: center; justify-content: center;
            padding: 40px;
        }
        .form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 40px;
            width: 100%; max-width: 440px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
        }
        .badge-logo-udb {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: #0b1f44; color: #ffffff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.9rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- PANEL KIRI (BRANDING & KARTU PREVIEW) -->
    <div class="left-banner d-none d-lg-flex">
        <div>
            <div class="d-flex align-items-center gap-3 mb-5">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: #ffffff; color: #0b1f44;" class="d-flex align-items-center justify-content-center fw-bold fs-6">
                    UDB
                </div>
                <div>
                    <h5 class="fw-bold m-0 text-white" style="letter-spacing: 0.5px;">SIPRAP</h5>
                    <small style="color: #94a3b8; font-size: 0.78rem;">Universitas Duta Bangsa Surakarta</small>
                </div>
            </div>

            <h1 class="fw-bold mb-3" style="font-size: 2.3rem; line-height: 1.25; max-width: 520px;">
                Peminjaman ruangan dan alat praktikum, lebih tertib dan mudah dipantau.
            </h1>
            <p style="color: #94a3b8; font-size: 0.95rem; max-width: 460px; line-height: 1.6;" class="mb-5">
                Cek ketersediaan, ajukan, pantau status, hingga pengembalian dalam satu sistem terpusat.
            </p>

            <!-- 3 KARTU PREVIEW -->
            <div style="max-width: 420px;">
                <div class="preview-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="preview-icon"><i class="bi bi-building"></i></div>
                        <div>
                            <span class="d-block fw-bold text-white small">Lab Komputer 1</span>
                            <small style="color: #94a3b8; font-size: 0.72rem;">Kapasitas 30 · 08.00–11.00</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-3 py-1" style="background: #dcfce7; color: #15803d; font-size: 0.72rem; font-weight: 600;">
                        Tersedia
                    </span>
                </div>

                <div class="preview-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="preview-icon"><i class="bi bi-box"></i></div>
                        <div>
                            <span class="d-block fw-bold text-white small">Proyektor Portable</span>
                            <small style="color: #94a3b8; font-size: 0.72rem;">Sisa stok 4 dari 5</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-3 py-1" style="background: #dcfce7; color: #15803d; font-size: 0.72rem; font-weight: 600;">
                        Tersedia
                    </span>
                </div>

                <div class="preview-card mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="preview-icon"><i class="bi bi-file-earmark-text"></i></div>
                        <div>
                            <span class="d-block fw-bold text-white small">PJ-2610-021</span>
                            <small style="color: #94a3b8; font-size: 0.72rem;">Lab Jaringan · 27 Okt 2026</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-3 py-1" style="background: #e0f2fe; color: #0369a1; font-size: 0.72rem; font-weight: 600;">
                        Disetujui
                    </span>
                </div>
            </div>
        </div>

        <div>
            <small style="color: #64748b; font-size: 0.75rem;">© 2026 SIPRAP · Universitas Duta Bangsa Surakarta</small>
        </div>
    </div>

    <!-- PANEL KANAN (FORM LOGIN BERSIH) -->
    <div class="right-form">
        <div class="form-card">
            <div class="badge-logo-udb">UDB</div>
            <h4 class="fw-bold text-dark mb-1">Masuk ke SIPRAP</h4>
            <p class="text-secondary small mb-4">Gunakan username dan password yang diberikan Admin.</p>

            @if(session('error'))
            <div class="alert alert-danger py-2 px-3 rounded-3 small mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="inputUsername" class="form-control border-start-0 ps-0" placeholder="Masukkan username" value="{{ old('username') }}" required style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark mb-1">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="inputPassword" class="form-control border-start-0 border-end-0 ps-0" placeholder="Masukkan password" required style="font-size: 0.88rem;">
                        <span class="input-group-text bg-white border-start-0 text-secondary" id="togglePasswordBtn" style="cursor: pointer;">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn w-100 py-2 fw-semibold text-white mb-2" style="background: #2563eb; border-radius: 10px; font-size: 0.9rem;">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // FUNGSI TOGGLE LIHAT PASSWORD
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('inputPassword');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    togglePasswordBtn.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'text') {
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    });
</script>

</body>
</html>