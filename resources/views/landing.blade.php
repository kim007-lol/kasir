<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMEGABIZ — Sistem Kasir & Booking Makanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ee5253;
            --secondary: #ff8a80;
            --accent: #fff0f0;
            --dark: #2c3e50;
            --light-bg: #f8f9fc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark);
        }

        /* ===== NAVBAR ===== */
        .landing-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .landing-nav .brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .landing-nav .brand i {
            font-size: 1.6rem;
        }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 50%, #d63031 100%);
            color: white;
            padding: 5rem 0 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }

        .hero h1 {
            font-weight: 800;
            font-size: 2.8rem;
            line-height: 1.2;
            margin-bottom: 1.25rem;
        }

        .hero p {
            font-size: 1.15rem;
            opacity: 0.92;
            line-height: 1.7;
            max-width: 550px;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* ===== FEATURE CARDS ===== */
        .features {
            padding: 4.5rem 0;
        }

        .features .section-title {
            font-weight: 800;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .features .section-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 3rem;
            font-size: 1.05rem;
        }

        .feature-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.8rem;
        }

        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: #6c757d;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        /* ===== LOGIN SECTION ===== */
        .login-section {
            padding: 4.5rem 0;
            background: white;
        }

        .login-card {
            background: var(--light-bg);
            border-radius: 1.25rem;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }

        .login-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255, 107, 107, 0.15);
        }

        .login-card .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2rem;
        }

        .login-card h4 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-card p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .btn-login-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-login-primary:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.35);
        }

        .btn-login-outline {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-login-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.35);
        }

        /* ===== OPERATING HOURS ===== */
        .hours-section {
            padding: 3rem 0;
            background: var(--accent);
        }

        .hours-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* ===== FOOTER ===== */
        .landing-footer {
            background: var(--dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 2rem 0;
            text-align: center;
            font-size: 0.9rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero {
                padding: 3rem 0 2.5rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .features,
            .login-section {
                padding: 3rem 0;
            }

            .login-card {
                padding: 1.75rem;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 1.6rem;
            }

            .features .section-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="landing-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="brand">
                <i class="bi bi-shop"></i> SMEGABIZ
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('staff.login') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-person-badge"></i> <span class="d-none d-sm-inline">Login Staf</span>
                </a>
                <a href="{{ route('pelanggan.login') }}" class="btn btn-sm text-white" style="background: var(--primary);">
                    <i class="bi bi-bag-heart"></i> <span class="d-none d-sm-inline">Pesan Makanan</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="bi bi-lightning-charge-fill"></i> Sistem Terintegrasi
                    </div>
                    <h1>Kelola Toko & Pemesanan dalam Satu Platform</h1>
                    <p>SMEGABIZ menggabungkan sistem kasir (POS) canggih dengan pemesanan makanan online. Pelanggan bisa pesan dari mana saja, kasir terima pesanan secara real-time.</p>
                    <div class="d-flex gap-3 mt-4 flex-wrap">
                        <a href="{{ route('pelanggan.login') }}" class="btn-login-primary">
                            <i class="bi bi-bag-heart-fill"></i> Pesan Sekarang
                        </a>
                        <a href="#features" class="btn-login-outline" style="border-color: rgba(255,255,255,0.4); color: white; background: transparent;">
                            <i class="bi bi-arrow-down-circle"></i> Pelajari Lebih
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <i class="bi bi-shop-window" style="font-size: 12rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Fitur Unggulan</h2>
            <p class="section-subtitle">Semua yang Anda butuhkan untuk mengelola toko dan menerima pesanan online</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #fff3cd; color: #856404;">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <h5>Kasir (POS)</h5>
                        <p>Transaksi cepat dengan barcode scanner, multi metode pembayaran, dan struk otomatis.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #d4edda; color: #155724;">
                            <i class="bi bi-phone-fill"></i>
                        </div>
                        <h5>Booking Online</h5>
                        <p>Pelanggan pesan makanan langsung dari browser. Pilih pickup atau delivery.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #cce5ff; color: #004085;">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h5>Real-Time</h5>
                        <p>Pesanan masuk langsung ke kasir dengan notifikasi. Stok terupdate otomatis.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #f8d7da; color: #721c24;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5>Laporan Lengkap</h5>
                        <p>Rekap transaksi, profit, dan stok. Export ke PDF & Excel dalam sekali klik.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Operating Hours -->
    <section class="hours-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="hours-card">
                        <i class="bi bi-clock-fill text-primary" style="font-size: 2.5rem;"></i>
                        <h4 class="mt-3 fw-bold">Jam Operasional</h4>
                        <p class="text-muted mb-2">Pemesanan online hanya tersedia pada:</p>
                        <h3 class="fw-bold" style="color: var(--primary);">
                            <i class="bi bi-sun-fill"></i> 07:00 — 15:00 WIB
                        </h3>
                        <p class="text-muted small mt-2 mb-0">Senin — Jumat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section" id="login">
        <div class="container">
            <h2 class="section-title mb-2" style="font-weight: 800;">Masuk ke Sistem</h2>
            <p class="section-subtitle mb-4">Pilih sesuai peran Anda</p>
            <div class="row g-4 justify-content-center">
                <!-- Staff Login -->
                <div class="col-md-5">
                    <div class="login-card">
                        <div class="icon-wrapper" style="background: #fff3cd; color: #856404;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h4>Login Staf</h4>
                        <p>Untuk admin dan kasir. Kelola toko, transaksi, stok, dan laporan.</p>
                        <a href="{{ route('staff.login') }}" class="btn-login-outline">
                            <i class="bi bi-box-arrow-in-right"></i> Login Staf
                        </a>
                    </div>
                </div>
                <!-- Customer Login -->
                <div class="col-md-5">
                    <div class="login-card">
                        <div class="icon-wrapper" style="background: #d4edda; color: #155724;">
                            <i class="bi bi-bag-heart-fill"></i>
                        </div>
                        <h4>Login Pelanggan</h4>
                        <p>Untuk memesan makanan secara online. Daftar gratis jika belum punya akun.</p>
                        <a href="{{ route('pelanggan.login') }}" class="btn-login-primary">
                            <i class="bi bi-bag-heart"></i> Login / Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} SMEGABIZ — Rekayasa Perangkat Lunak Project 2026</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });
    </script>
</body>

</html>