<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmegaMart — Sistem Kasir & Booking Makanan</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <!-- Animasi Lokal Ringan -->
    <link href="{{ asset('css/aos.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <style>
        :root {
            --primary: #ff0000;
            --primary-dark: #cc0000;
            --secondary: #ff2222;
            --accent: #ffe0e0;
            --dark: #1a1a2e;
            --light-bg: #f4f7f6;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
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

        /* ===== LEGENDARY TYPOGRAPHY ===== */
        .text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #ffcccc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-dark {
            background: linear-gradient(135deg, var(--primary) 0%, #4a0000 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ===== HERO EMPOWERED ===== */
        .hero {
            background: linear-gradient(-45deg, #ff0000, #cc0000, #a00000, #d91c1c);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
            padding: 7rem 0 6rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .hero::before, .hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            filter: blur(60px);
        }

        .hero::before {
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.15);
            top: -150px;
            right: -100px;
            animation: floatShape 8s ease-in-out infinite alternate;
        }

        .hero::after {
            width: 400px;
            height: 400px;
            background: rgba(0, 0, 0, 0.15);
            bottom: -150px;
            left: -100px;
            animation: floatShape 12s ease-in-out infinite alternate-reverse;
        }

        @keyframes floatShape {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(50px) scale(1.1); }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 4rem 0 3rem;
                text-align: center;
            }
            .hero h1 { font-size: 2.2rem !important; }
            .hero p { margin: 0 auto 1.5rem !important; font-size: 1rem !important; }
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
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255,255,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
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

        /* ===== FEATURE CARDS ===== */
        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid var(--glass-border);
            height: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transform-style: preserve-3d;
        }

        .feature-card:hover {
            box-shadow: 0 20px 40px rgba(255, 0, 0, 0.08);
            border: 1px solid rgba(255,0,0,0.2);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            transform: translateZ(30px); /* 3D effect on tilt */
            transition: transform 0.3s ease;
        }

        .feature-card h5 {
            font-weight: 800;
            margin-bottom: 0.75rem;
            transform: translateZ(20px);
        }

        .feature-card p {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            transform: translateZ(10px);
        }

        /* ===== LANYARD TEAM CARDS ===== */
        .team-section {
            padding: 5rem 0;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .lanyard-team-container {
            display: grid;
            grid-template-columns: repeat(3, 250px);
            justify-content: center;
            row-gap: 0;
            column-gap: 3rem;
            padding-top: 2rem;
            padding-bottom: 3rem;
        }

        .lanyard-wrapper.pm { grid-column: 2; grid-row: 1; z-index: 4; }
        .lanyard-wrapper.prog { grid-column: 1; grid-row: 1; margin-top: 220px; z-index: 3; }
        .lanyard-wrapper.sa { grid-column: 3; grid-row: 1; margin-top: 220px; z-index: 3; }
        .lanyard-wrapper.ux { grid-column: 2; grid-row: 2; margin-top: -180px; z-index: 2; }

        @media (max-width: 991px) {
            .lanyard-team-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 2.5rem;
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
            .lanyard-wrapper.pm, .lanyard-wrapper.prog, .lanyard-wrapper.sa, .lanyard-wrapper.ux {
                margin-top: 0;
            }
        }

        .lanyard-wrapper {
            perspective: 1000px;
            position: relative;
            width: 250px;
            animation: lanyard-swing 4s ease-in-out infinite;
            transform-origin: top center;
            z-index: 2;
        }

        /* Stagger the swings */
        .lanyard-wrapper:nth-child(1) { animation-delay: 0s; }
        .lanyard-wrapper:nth-child(2) { animation-delay: -1s; }
        .lanyard-wrapper:nth-child(3) { animation-delay: -2s; }
        .lanyard-wrapper:nth-child(4) { animation-delay: -3s; }

        .lanyard-wrapper:hover {
            animation-play-state: paused;
            z-index: 10;
        }

        .lanyard-string {
            width: 6px;
            height: 90px;
            background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
            margin: 0 auto;
            border-radius: 3px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }

        .lanyard-string::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 24px;
            height: 20px;
            background: #64748b;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            z-index: 2;
        }

        .lanyard-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.25rem;
            padding: 2.5rem 1.5rem 1.5rem;
            text-align: center;
            border: 1px solid var(--glass-border);
            border-top: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform-style: preserve-3d;
            position: relative;
            z-index: 2;
            margin-top: -5px; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .lanyard-card::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 8px;
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }

        .lanyard-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            margin: 0 auto 1.25rem;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            color: var(--card-color);
            box-shadow: 0 10px 25px rgba(var(--card-rgb), 0.15);
            border: 4px solid white;
            transform: translateZ(40px);
            position: relative;
        }
        
        .lanyard-avatar::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 2px dashed rgba(var(--card-rgb), 0.4);
            animation: spin 15s linear infinite;
            z-index: 1;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        .lanyard-avatar i, 
        .lanyard-avatar img {
            position: relative;
            z-index: 2;
        }

        .lanyard-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .lanyard-name {
            font-weight: 800;
            font-size: 1.2rem;
            color: #1e293b;
            margin-bottom: 0.2rem;
            transform: translateZ(30px);
        }

        .lanyard-role {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--card-color);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 1rem;
            transform: translateZ(25px);
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            background: rgba(var(--card-rgb), 0.1);
        }

        .lanyard-desc {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
            transform: translateZ(20px);
            margin-bottom: 1.5rem;
            height: 60px; /* Fixed height for uniformity */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-line-clamp: 3; /* Duplicated for compatibility */
            -webkit-box-orient: vertical;
        }

        .lanyard-social {
            display: flex;
            justify-content: center;
            gap: 10px;
            transform: translateZ(25px);
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .lanyard-social a {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .lanyard-social a:hover {
            background: var(--card-color);
            color: white;
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 5px 15px rgba(var(--card-rgb), 0.3);
        }

        @keyframes lanyard-swing {
            0%, 100% { transform: rotateZ(-5deg); }
            50% { transform: rotateZ(5deg); }
        }

        /* ===== LOGIN SECTION ===== */
        .login-section {
            padding: 4.5rem 0;
            background: white;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid var(--glass-border);
            height: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transform-style: preserve-3d;
        }

        .login-card:hover {
            border-color: rgba(255,0,0,0.3);
            box-shadow: 0 20px 40px rgba(255, 0, 0, 0.12);
        }

        .login-card .icon-wrapper {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem;
            transform: translateZ(40px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .login-card h4 {
            font-weight: 800;
            margin-bottom: 0.75rem;
            transform: translateZ(30px);
        }

        .login-card p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            transform: translateZ(20px);
        }

        .btn-login-wrapper {
            transform: translateZ(30px);
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

            .feature-card {
                padding: 1.25rem;
            }

            /* About section tabs horizontal scroll */
            #aboutTabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 5px;
            }
            #aboutTabs .nav-link {
                white-space: nowrap;
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem;
            }
            #aboutTabs::-webkit-scrollbar {
                height: 3px;
            }
            #aboutTabs::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 10px;
            }

            /* Navbar buttons compact */
            .landing-nav .btn-sm {
                font-size: 0.75rem;
                padding: 0.3rem 0.5rem;
            }

            /* Feature icon smaller */
            .feature-icon {
                width: 55px;
                height: 55px;
                font-size: 1.4rem;
            }

            /* Hours section */
            .hours-section {
                padding: 2rem 0;
            }
            .hours-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 1.6rem;
            }

            .hero-badge {
                font-size: 0.75rem;
                padding: 0.3rem 0.8rem;
            }

            .features .section-title {
                font-size: 1.5rem;
            }

            .landing-nav .brand {
                font-size: 1.1rem;
            }
            .landing-nav .brand i {
                font-size: 1.3rem;
            }

            /* CTA buttons */
            .btn-login-primary,
            .btn-login-outline {
                padding: 0.6rem 1.25rem;
                font-size: 0.85rem;
            }

            /* Login cards */
            .login-card {
                padding: 1.25rem;
            }
            .login-card .icon-wrapper {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            .login-card h4 {
                font-size: 1.1rem;
            }

            /* Feature cards */
            .feature-card h5 {
                font-size: 0.95rem;
            }
            .feature-card p {
                font-size: 0.82rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="landing-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="brand">
                <i class="bi bi-shop"></i> SmegaMart
            </a>
            <div class="d-flex gap-2 align-items-center">
                <div class="btn-group btn-group-sm me-2" role="group" aria-label="Language">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold active" id="btn-id" onclick="changeLanguage('id')" style="font-size: 0.7rem;">ID</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" id="btn-en" onclick="changeLanguage('en')" style="font-size: 0.7rem;">EN</button>
                </div>
                <a href="{{ route('staff.login') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-person-badge"></i> <span class="d-none d-sm-inline" data-i18n="nav.login_staff">Login Staf</span>
                </a>
                <a href="{{ route('pelanggan.login') }}" class="btn btn-sm text-white" style="background: var(--primary);">
                    <i class="bi bi-bag-heart"></i> <span class="d-none d-sm-inline" data-i18n="nav.order_food">Pesan Makanan</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1000">
                    <div class="hero-badge">
                        <i class="bi bi-lightning-charge-fill text-warning"></i> <span data-i18n="hero.badge">Sistem Terintegrasi</span>
                    </div>
                    <h1 class="text-gradient" data-i18n="hero.title" style="font-size: 3.2rem; letter-spacing: -1px;">Kelola Toko & Pemesanan dalam Satu Platform</h1>
                    <p data-i18n="hero.desc" style="font-size: 1.2rem; font-weight: 400;">SmegaMart menggabungkan sistem kasir (POS) canggih dengan pemesanan makanan online. Pelanggan bisa pesan dari mana saja, kasir terima pesanan secara real-time.</p>
                    <div class="d-flex gap-3 mt-5 flex-wrap" data-aos="fade-up" data-aos-delay="300">
                        <a href="{{ route('pelanggan.login') }}" class="btn-login-primary" style="background: white; color: var(--primary);">
                            <i class="bi bi-bag-heart-fill"></i> <span data-i18n="hero.cta_order">Pesan Sekarang</span>
                        </a>
                        <a href="#features" class="btn-login-outline" style="border-color: rgba(255,255,255,0.4); color: white; background: transparent;">
                            <i class="bi bi-arrow-down-circle"></i> <span data-i18n="hero.cta_learn">Pelajari Lebih</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center position-relative" data-aos="zoom-in-up" data-aos-duration="1200" data-aos-delay="200">
                    <i class="bi bi-shop-window animate__animated animate__pulse animate__infinite animate__slower" style="font-size: 14rem; opacity: 0.2; display: inline-block;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features" style="position: relative; z-index: 2;">
        <div class="container">
            <h2 class="section-title text-gradient-dark" data-i18n="feat.title" data-aos="fade-up" style="font-size: 2.5rem; letter-spacing: -1px;">Fitur Unggulan</h2>
            <p class="section-subtitle mb-5" data-i18n="feat.subtitle" data-aos="fade-up" data-aos-delay="100" style="font-size: 1.1rem;">Semua yang Anda butuhkan untuk mengelola toko dan menerima pesanan online</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #fff3cd; color: #856404;">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <h5 data-i18n="feat.pos_title">Kasir (POS)</h5>
                        <p data-i18n="feat.pos_desc">Transaksi cepat dengan barcode scanner, multi metode pembayaran, dan struk otomatis.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="250">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #d4edda; color: #155724;">
                            <i class="bi bi-phone-fill"></i>
                        </div>
                        <h5 data-i18n="feat.booking_title">Booking Online</h5>
                        <p data-i18n="feat.booking_desc">Pelanggan pesan makanan langsung dari browser. Pilih pickup atau delivery.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="350">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #cce5ff; color: #004085;">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <h5 data-i18n="feat.stock_title">Stok Pintar</h5>
                        <p data-i18n="feat.stock_desc">Manajemen stok gudang dan kasir yang terintegrasi. Pantau pergerakan barang secara akurat.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="450">
                    <div class="feature-card" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2">
                        <div class="feature-icon" style="background: #f8d7da; color: #a52834;">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h5 data-i18n="feat.realtime_title">Notifikasi Instan</h5>
                        <p data-i18n="feat.realtime_desc">Terima pesanan SmeGo secara real-time dengan notifikasi suara dan pop-up.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="550">
                    <div class="feature-card" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2">
                        <div class="feature-icon" style="background: #e2e3e5; color: #383d41;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 data-i18n="feat.report_title">Laporan Bisnis</h5>
                        <p data-i18n="feat.report_desc">Rekap transaksi, laba, dan stok dalam satu klik. Ekspor data ke PDF & Excel dengan mudah.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="650">
                    <div class="feature-card" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2">
                        <div class="feature-icon" style="background: #d1ecf1; color: #0c5460;">
                            <i class="bi bi-display-fill"></i>
                        </div>
                        <h5 data-i18n="feat.display_title">Layar Pelanggan</h5>
                        <p data-i18n="feat.display_desc">Tingkatkan kepercayaan dengan monitor khusus untuk transparansi transaksi pelanggan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About Us — Tim Pengembang (Lanyard Edition) -->
    <section class="team-section" id="tentang">
        <!-- Decorative blobs -->
        <div style="position: absolute; top: -50px; left: 10%; width: 250px; height: 250px; background: rgba(0,255,100,0.05); filter: blur(60px); border-radius: 50%; z-index:0;"></div>
        <div style="position: absolute; bottom: 50px; right: 5%; width: 300px; height: 300px; background: rgba(255,200,0,0.05); filter: blur(60px); border-radius: 50%; z-index:0;"></div>

        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center" data-aos="fade-up">
                <h2 class="section-title text-gradient-dark" data-i18n="about.title" style="font-size: 2.5rem; letter-spacing: -1px; font-weight:800;">Tim Pengembang</h2>
                <p class="section-subtitle mb-2" data-i18n="about.subtitle" style="font-size: 1.1rem; color: #64748b; max-width: 800px; margin: 0 auto;">Kami adalah 4 siswa dari konsentrasi Rekayasa Perangkat Lunak (RPL) SMKN 10 Surabaya yang berkolaborasi dalam satu tim untuk mewujudkan SmegaMart.</p>
            </div>
            
            <div class="lanyard-team-container">

                {{-- Member 1: Engineer --}}
                <div class="lanyard-wrapper prog" data-aos="zoom-in" data-aos-delay="150" data-aos-offset="0">
                    <div class="lanyard-string"></div>
                    <div class="lanyard-card" data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2" style="--card-color: #e60000; --card-rgb: 230, 0, 0;">
                        <div class="lanyard-avatar">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <h5 class="lanyard-name">Ngabdullah Hakim</h5>
                        <div class="lanyard-role">Programmer</div>
                        <p class="lanyard-desc" data-i18n="team.eng_desc">Seseorang yang suka ngoding dan selalu belajar berkembang. Mengurus flow Backend & Frontend.</p>
                        <div class="lanyard-social">
                            <a href="https://github.com/kim007-lol" target="_blank" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="mailto:ngabdullahhakim99@gmail.com" title="Email"><i class="bi bi-envelope-fill"></i></a>
                            <a href="https://www.linkedin.com/in/ngabdullah-hakim-121ab0292" target="_blank" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Member 2: Project Manager --}}
                <div class="lanyard-wrapper pm" data-aos="zoom-in" data-aos-delay="250" data-aos-offset="0">
                    <div class="lanyard-string"></div>
                    <div class="lanyard-card" data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2" style="--card-color: #6f42c1; --card-rgb: 111, 66, 193;">
                        <div class="lanyard-avatar">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h5 class="lanyard-name">Syifa Rizka Angeli</h5>
                        <div class="lanyard-role">Project Manager</div>
                        <p class="lanyard-desc" data-i18n="team.pm_desc">Mengatur timeline, komunikasi tim, dan memastikan project rilis tepat pada waktunya.</p>
                        <div class="lanyard-social">
                            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" title="Email"><i class="bi bi-envelope-fill"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Member 3: System Analyst --}}
                <div class="lanyard-wrapper sa" data-aos="zoom-in" data-aos-delay="350" data-aos-offset="0">
                    <div class="lanyard-string"></div>
                    <div class="lanyard-card" data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2" style="--card-color: #0d6efd; --card-rgb: 13, 110, 253;">
                        <div class="lanyard-avatar">
                            <img src="{{ asset('img/BAYU.jpg') }}" alt="Bayu">
                        </div>
                        <h5 class="lanyard-name">Bayu Bramasta</h5>
                        <div class="lanyard-role">System Analyst</div>
                        <p class="lanyard-desc">I'm 18 Y.O an Electrical Engineering Student 😎.</p>
                        <div class="lanyard-social">
                            <a href="https://github.com/BayuBramasta" target="_blank" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="https://www.facebook.com/share/1E46oyY1m8/" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Member 4: UI/UX Designer --}}
                <div class="lanyard-wrapper ux" data-aos="zoom-in" data-aos-delay="450" data-aos-offset="0">
                    <div class="lanyard-string"></div>
                    <div class="lanyard-card" data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2" style="--card-color: #ffc107; --card-rgb: 255, 193, 7;">
                        <div class="lanyard-avatar" style="color: #d39e00;">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        </div>
                        <h5 class="lanyard-name">M. Iqbal Fathyyatan</h5>
                        <div class="lanyard-role" style="color: #d39e00;">UI/UX Designer</div>
                        <p class="lanyard-desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Iqbal breathing.</p>
                        <div class="lanyard-social">
                            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section" id="login" style="background: var(--light-bg); position: relative; overflow: hidden;">
        <!-- Decorative blobs -->
        <div style="position: absolute; top: -100px; left: -100px; width: 300px; height: 300px; background: rgba(255,0,0,0.05); filter: blur(50px); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -100px; right: -100px; width: 400px; height: 400px; background: rgba(0,0,255,0.03); filter: blur(60px); border-radius: 50%;"></div>
        
        <div class="container" style="position: relative; z-index: 2;">
            <h2 class="section-title mb-2 text-gradient-dark" style="font-size: 2.5rem; letter-spacing: -1px; font-weight: 800;" data-i18n="login.title" data-aos="fade-up">Masuk ke Sistem</h2>
            <p class="section-subtitle mb-5" data-i18n="login.subtitle" data-aos="fade-up" data-aos-delay="100" style="font-size: 1.1rem;">Pilih sesuai peran Anda</p>
            <div class="row g-4 justify-content-center">
                <!-- Staff Login -->
                <div class="col-md-5" data-aos="fade-right" data-aos-delay="200">
                    <div class="login-card" data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
                        <div class="icon-wrapper" style="background: #fff3cd; color: #856404;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h4 data-i18n="login.staff_title">Login Staf</h4>
                        <p data-i18n="login.staff_desc">Untuk admin dan kasir. Kelola toko, transaksi, stok, dan laporan.</p>
                        <div class="btn-login-wrapper">
                            <a href="{{ route('staff.login') }}" class="btn-login-outline">
                                <i class="bi bi-box-arrow-in-right"></i> <span data-i18n="login.staff_btn">Login Staf</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Customer Login -->
                <div class="col-md-5" data-aos="fade-left" data-aos-delay="300">
                    <div class="login-card" data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
                        <div class="icon-wrapper" style="background: #d4edda; color: #155724;">
                            <i class="bi bi-bag-heart-fill"></i>
                        </div>
                        <h4 data-i18n="login.cust_title">Login Pelanggan</h4>
                        <p data-i18n="login.cust_desc">Untuk memesan makanan secara online. Akun diberikan oleh kasir toko.</p>
                        <div class="btn-login-wrapper">
                            <a href="{{ route('pelanggan.login') }}" class="btn-login-primary">
                                <i class="bi bi-bag-heart"></i> <span data-i18n="login.cust_btn">Login Pelanggan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <p class="mb-0" data-i18n="footer.text">&copy; {{ date('Y') }} SmegaMart — Rekayasa Perangkat Lunak Project 2026</p>
        </div>
    </footer>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/i18next.min.js') }}"></script>
    <!-- AOS Animate On Scroll JS -->
    <script src="{{ asset('js/aos.js') }}"></script>
    <!-- VanillaTilt JS (Efek 3D Apple-like) -->
    <script src="{{ asset('js/vanilla-tilt.min.js') }}"></script>
    <script>
        // Init AOS
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    once: true,
                    offset: 50,
                    duration: 800,
                    easing: 'ease-out-cubic'
                });
            }
        });

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

        // ===== i18next Multi-language =====
        const translations = {
            id: {
                nav: { login_staff: "Login Staf", order_food: "Pesan Makanan" },
                hero: {
                    badge: "Eco-System Bisnis Terintegrasi",
                    title: "Sistem Kasir Pintar & Pemesanan Online Terintegrasi",
                    desc: "Optimalkan operasional bisnis Anda dengan solusi ritel modern. Kelola transaksi kasir, stok gudang, hingga pesanan online pelanggan dalam satu ekosistem digital yang efisien.",
                    cta_order: "Pesan Sekarang",
                    cta_learn: "Pelajari Lebih"
                },
                feat: {
                    title: "Fitur Unggulan",
                    subtitle: "Semua yang Anda butuhkan untuk mengelola toko dan menerima pesanan online",
                    pos_title: "Kasir Canggih",
                    pos_desc: "Transaksi secepat kilat dengan pencarian produk cerdas, multi metode pembayaran, dan cetak struk otomatis.",
                    booking_title: "SmeGo Online",
                    booking_desc: "Pelanggan pesan makanan langsung dari browser (Pickup/Delivery), mengurangi antrian di toko.",
                    stock_title: "Stok Pintar",
                    stock_desc: "Manajemen stok gudang dan kasir yang terintegrasi. Pantau pergerakan barang secara akurat.",
                    realtime_title: "Notifikasi Instan",
                    realtime_desc: "Terima pesanan SmeGo secara real-time dengan notifikasi suara dan pop-up otomatis.",
                    report_title: "Laporan Bisnis",
                    report_desc: "Rekap transaksi, laba, dan stok harian hingga bulanan. Ekspor data ke PDF & Excel dengan mudah.",
                    display_title: "Layar Pelanggan",
                    display_desc: "Tingkatkan kepercayaan dengan monitor khusus untuk transparansi rincian belanja pelanggan."
                },
                about: {
                    title: "Tim Pengembang",
                    subtitle: "Kami adalah 4 siswa dari konsentrasi Rekayasa Perangkat Lunak (RPL) SMKN 10 Surabaya yang berkolaborasi membangun sistem ini sebagai proyek nyata. Setiap anggota membawa peran dan keahlian masing-masing untuk mewujudkan SmegaMart."
                },
                team: {
                    pm_desc: "[Deskripsi Project Manager]",
                    sa_desc: "[Deskripsi System Analyst]",
                    rm_desc: "[Deskripsi UI/UX Designer]",
                    eng_desc: "Seseorang yang suka ngoding dan terus berkembang dalam dunia pengembangan web."
                },
                login: {
                    title: "Masuk ke Sistem",
                    subtitle: "Pilih sesuai peran Anda",
                    staff_title: "Login Staf",
                    staff_desc: "Untuk admin dan kasir. Kelola toko, transaksi, stok, dan laporan.",
                    staff_btn: "Login Staf",
                    cust_title: "Login Pelanggan",
                    cust_desc: "Untuk memesan makanan secara online. Akun diberikan oleh kasir toko.",
                    cust_btn: "Login Pelanggan"
                },
                footer: { text: "© 2026 SmegaMart — Rekayasa Perangkat Lunak Project 2026" }
            },
            en: {
                nav: { login_staff: "Staff Login", order_food: "Order Food" },
                hero: {
                    badge: "Integrated Business Ecosystem",
                    title: "Smart POS & Integrated Online Ordering System",
                    desc: "Optimize your business operations with modern retail solutions. Manage cashier transactions, warehouse stock, and online customer orders in one efficient digital ecosystem.",
                    cta_order: "Order Now",
                    cta_learn: "Learn More"
                },
                feat: {
                    title: "Key Features",
                    subtitle: "Everything you need to manage your store and receive online orders",
                    pos_title: "Advanced POS",
                    pos_desc: "Lightning-fast transactions with smart product search, multiple payment methods, and auto-receipt prints.",
                    booking_title: "SmeGo Online",
                    booking_desc: "Customers order food directly from their browser (Pickup/Delivery), reducing store queues.",
                    stock_title: "Smart Inventory",
                    stock_desc: "Integrated warehouse and cashier stock management. Track goods movement accurately.",
                    realtime_title: "Instant Alerts",
                    realtime_desc: "Receive SmeGo orders in real-time with automatic sound notifications and pop-ups.",
                    report_title: "Business Reports",
                    report_desc: "Daily to monthly transaction, profit, and stock recaps. Easy export to PDF & Excel.",
                    display_title: "Customer Display",
                    display_desc: "Boost trust with a dedicated monitor for real-time transaction detail transparency."
                },
                about: {
                    title: "Development Team",
                    subtitle: "We are 4 students from the Software Engineering (RPL) major at SMKN 10 Surabaya who collaborated to build this system as a real-world project. Each member brings their own role and expertise to bring SmegaMart to life."
                },
                team: {
                    pm_desc: "[Project Manager description]",
                    sa_desc: "[System Analyst description]",
                    rm_desc: "[UI/UX Designer description]",
                    eng_desc: "Someone who loves coding and keeps growing in the world of web development."
                },
                login: {
                    title: "Sign In",
                    subtitle: "Choose based on your role",
                    staff_title: "Staff Login",
                    staff_desc: "For admins and cashiers. Manage store, transactions, stock, and reports.",
                    staff_btn: "Staff Login",
                    cust_title: "Customer Login",
                    cust_desc: "To order food online. Account is provided by the store cashier.",
                    cust_btn: "Customer Login"
                },
                footer: { text: "© 2026 SmegaMart — Software Engineering Project 2026" }
            }
        };

        function changeLanguage(lang) {
            i18next.changeLanguage(lang, (err) => {
                if (err) return console.log('Error:', err);
                updateContent();
                document.getElementById('btn-id').classList.toggle('active', lang === 'id');
                document.getElementById('btn-en').classList.toggle('active', lang === 'en');
                localStorage.setItem('landing_lang', lang);
            });
        }

        function updateContent() {
            document.querySelectorAll('[data-i18n]').forEach(elem => {
                const key = elem.getAttribute('data-i18n');
                const translation = i18next.t(key);
                if (translation.includes('<') && translation.includes('>')) {
                    elem.innerHTML = translation;
                } else {
                    elem.textContent = translation;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('landing_lang') || 'id';
            i18next.init({
                lng: savedLang,
                debug: false,
                resources: {
                    id: { translation: translations.id },
                    en: { translation: translations.en }
                }
            }, function(err) {
                updateContent();
                document.getElementById('btn-id').classList.toggle('active', savedLang === 'id');
                document.getElementById('btn-en').classList.toggle('active', savedLang === 'en');
            });
        });
    </script>
</body>

</html>