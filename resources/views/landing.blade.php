<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMEGABIZ — Sistem Kasir & Booking Makanan</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff0000;
            --primary-dark: #cc0000;
            --secondary: #ff2222;
            --accent: #ffe0e0;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 50%, #bb0000 100%);
            color: white;
            padding: 4rem 0 3rem;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .hero {
                padding: 3rem 0;
                text-align: center;
            }
            .hero h1 {
                font-size: 2rem !important;
            }
            .hero p {
                margin: 0 auto 1.5rem !important;
                font-size: 1rem !important;
            }
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
            margin-bottom: 2rem;
        }
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

        /* ===== FEATURE CARDS ===== */
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

        /* ===== TEAM FLIP CARDS (MYSTERIOUS) ===== */
        .team-card-container {
            perspective: 1000px;
            height: 420px;
            cursor: pointer;
            z-index: 1;
        }

        .team-card-flipper {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
        }

        .team-card-container.flipped .team-card-flipper {
            transform: rotateY(180deg);
        }

        .team-card-container:hover .team-card-flipper {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border-radius: 1.5rem;
        }

        .team-card-front, .team-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .team-card-front::before, .team-card-back::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--card-color, var(--primary));
            z-index: 0;
        }

        /* Front specific */
        .team-card-front {
            justify-content: center;
            align-items: center;
            padding: 2rem;
            z-index: 2;
        }

        .team-avatar {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            color: var(--card-color, var(--primary));
            background: white;
            box-shadow: 0 10px 30px rgba(var(--card-rgb, 13, 110, 253), 0.15);
            border: 4px solid white;
            transition: all 0.5s ease;
            position: relative;
            z-index: 1;
        }

        .team-avatar::after {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 2px dashed rgba(var(--card-rgb, 13, 110, 253), 0.4);
            animation: spin 20s linear infinite;
        }

        @keyframes spin { 100% { transform: rotate(360deg); } }

        .team-card-container:hover .team-avatar {
            transform: scale(1.05);
            color: white;
            background: var(--card-color, var(--primary));
        }

        .team-role-main {
            font-size: 1.4rem;
            font-weight: 800;
            color: #2b3452;
            margin-top: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .click-hint {
            position: absolute;
            bottom: 1.5rem;
            font-size: 0.85rem;
            color: #a0a0a0;
            animation: pulse-hint 2s infinite;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @keyframes pulse-hint { 0%, 100% { opacity: 0.5; } 50% { opacity: 1; } }

        /* Back specific */
        .team-card-back {
            transform: rotateY(180deg);
            padding: 2rem;
            justify-content: center;
            text-align: center;
        }

        .team-name {
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 0.25rem;
            color: #2b3452;
        }

        .team-role {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--card-color, var(--primary));
            margin-bottom: 1rem;
            display: inline-block;
            padding: 0.25rem 0.8rem;
            border-radius: 2rem;
            background: rgba(var(--card-rgb, 13, 110, 253), 0.1);
        }

        .team-desc {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .team-skills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
            margin-bottom: 1.5rem;
        }

        .team-skill-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.7rem;
            border-radius: 1rem;
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .team-quote {
            font-size: 0.8rem;
            font-style: italic;
            color: #adb5bd;
            position: relative;
        }

        .team-social {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .team-social-link {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: #6c757d;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .team-social-link:hover {
            background: var(--card-color, var(--primary));
            color: white;
            transform: translateY(-2px);
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
                <i class="bi bi-shop"></i> SMEGABIZ
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
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="bi bi-lightning-charge-fill"></i> <span data-i18n="hero.badge">Sistem Terintegrasi</span>
                    </div>
                    <h1 data-i18n="hero.title">Kelola Toko & Pemesanan dalam Satu Platform</h1>
                    <p data-i18n="hero.desc">SMEGABIZ menggabungkan sistem kasir (POS) canggih dengan pemesanan makanan online. Pelanggan bisa pesan dari mana saja, kasir terima pesanan secara real-time.</p>
                    <div class="d-flex gap-3 mt-4 flex-wrap">
                        <a href="{{ route('pelanggan.login') }}" class="btn-login-primary">
                            <i class="bi bi-bag-heart-fill"></i> <span data-i18n="hero.cta_order">Pesan Sekarang</span>
                        </a>
                        <a href="#features" class="btn-login-outline" style="border-color: rgba(255,255,255,0.4); color: white; background: transparent;">
                            <i class="bi bi-arrow-down-circle"></i> <span data-i18n="hero.cta_learn">Pelajari Lebih</span>
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
            <h2 class="section-title" data-i18n="feat.title">Fitur Unggulan</h2>
            <p class="section-subtitle" data-i18n="feat.subtitle">Semua yang Anda butuhkan untuk mengelola toko dan menerima pesanan online</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #fff3cd; color: #856404;">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <h5 data-i18n="feat.pos_title">Kasir (POS)</h5>
                        <p data-i18n="feat.pos_desc">Transaksi cepat dengan barcode scanner, multi metode pembayaran, dan struk otomatis.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #d4edda; color: #155724;">
                            <i class="bi bi-phone-fill"></i>
                        </div>
                        <h5 data-i18n="feat.booking_title">Booking Online</h5>
                        <p data-i18n="feat.booking_desc">Pelanggan pesan makanan langsung dari browser. Pilih pickup atau delivery.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #cce5ff; color: #004085;">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <h5 data-i18n="feat.stock_title">Stok Pintar</h5>
                        <p data-i18n="feat.stock_desc">Manajemen stok gudang dan kasir yang terintegrasi. Pantau pergerakan barang secara akurat.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #f8d7da; color: #a52834;">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h5 data-i18n="feat.realtime_title">Notifikasi Instan</h5>
                        <p data-i18n="feat.realtime_desc">Terima pesanan SmeGo secara real-time dengan notifikasi suara dan pop-up.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #e2e3e5; color: #383d41;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 data-i18n="feat.report_title">Laporan Bisnis</h5>
                        <p data-i18n="feat.report_desc">Rekap transaksi, laba, dan stok dalam satu klik. Ekspor data ke PDF & Excel dengan mudah.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
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


    <!-- About Us — Tim Pengembang -->
    <section class="features" id="tentang" style="background: white;">
        <div class="container">
            <h2 class="section-title" data-i18n="about.title">Tim Pengembang</h2>
            <p class="section-subtitle" data-i18n="about.subtitle">Kami adalah 4 siswa dari konsentrasi Rekayasa Perangkat Lunak (RPL) SMKN 10 Surabaya yang berkolaborasi membangun sistem ini sebagai proyek nyata. Setiap anggota membawa peran dan keahlian masing-masing untuk mewujudkan SMEGABIZ.</p>

            <div class="row g-4 justify-content-center">

                    {{-- Member 1: Engineer --}}
                <div class="col-md-6 col-lg-3">
                    <div class="team-card-container" onclick="this.classList.toggle('flipped')">
                        <div class="team-card-flipper" style="--card-color: #ff0000; --card-rgb: 111, 66, 193;">
                            <div class="team-card-front">
                                <div class="team-avatar">
                                    <i class="bi bi-code-slash"></i>
                                </div>
                                <div class="team-role-main">Programmer</div>
                                <div class="click-hint"><i class="bi bi-hand-index-thumb"></i> Ketuk untuk detail</div>
                            </div>
                            <div class="team-card-back">
                                <h5 class="team-name">Ngabdullah Hakim</h5>
                                <div class="team-role">Programmer</div>
                                <p class="team-desc" data-i18n="team.eng_desc">Seseorang yang suka ngoding</p>
                                
                                <div class="team-skills">
                                    <span class="team-skill-badge">Laravel</span>
                                    <span class="team-skill-badge">JavaScript</span>
                                    <span class="team-skill-badge">Bootstrap</span>
                                    <span class="team-skill-badge">PostgreSQL</span>
                                </div>
                                
                                <div class="team-quote">"Selalu belajar dan berkembang"</div>
                                
                                <div class="team-social">
                                    <a href="https://github.com/kim007-lol" target="_blank" class="team-social-link" title="GitHub"><i class="bi bi-github"></i></a>
                                    <a href="mailto:ngabdullahhakim99@gmail.com" class="team-social-link" title="Email"><i class="bi bi-envelope-fill"></i></a>
                                    <a href="https://www.linkedin.com/in/ngabdullah-hakim-121ab0292" target="_blank" class="team-social-link" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Member 2: Project Manager --}}
                <div class="col-md-6 col-lg-3">
                    <div class="team-card-container" onclick="this.classList.toggle('flipped')">
                        <div class="team-card-flipper" style="--card-color: #cd6beb; --card-rgb: 220, 53, 69;">
                            <div class="team-card-front">
                                <div class="team-avatar">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                                <div class="team-role-main text-center">Project<br>Manager</div>
                                <div class="click-hint"><i class="bi bi-hand-index-thumb"></i> Ketuk untuk detail</div>
                            </div>
                            <div class="team-card-back">
                                <h5 class="team-name">Syifa Rizka Angeli</h5>
                                <div class="team-role">Project Manager</div>
                                <p class="team-desc" data-i18n="team.pm_desc"></p>
                                
                                <div class="team-skills">
                                    <span class="team-skill-badge">Leadership</span>
                                    <span class="team-skill-badge">Planning</span>
                                    <span class="team-skill-badge">Communication</span>
                                </div>
                                
                                <div class="team-quote">"[Motto / Tagline]"</div>
                                
                                <div class="team-social">
                                    <a href="#" class="team-social-link"><i class="bi bi-instagram"></i></a>
                                    <a href="#" class="team-social-link"><i class="bi bi-envelope-fill"></i></a>
                                    <a href="#" class="team-social-link"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Member 3: System Analyst --}}
                <div class="col-md-6 col-lg-3">
                    <div class="team-card-container" onclick="this.classList.toggle('flipped')">
                        <div class="team-card-flipper" style="--card-color: #0d6efd; --card-rgb: 13, 110, 253;">
                            <div class="team-card-front">
                                <div class="team-avatar">
                                    {{-- <i class="bi bi-laptop"></i> --}}
                                    <i><img class="img-fluid rounded-circle" src="{{ asset('img/BAYU.jpg') }}"></i>
                                </div>
                                <div class="team-role-main text-center">System<br>Analyst</div>
                                <div class="click-hint"><i class="bi bi-hand-index-thumb"></i> Ketuk untuk detail</div>
                            </div>
                            <div class="team-card-back">
                                <h5 class="team-name">Bayu Bramasta</h5>
                                <div class="team-role">System Analyst</div>
                                {{-- <p class="team-desc" data-i18n="team.sa_desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Est perspiciatis possimus eveniet omnis cumque! Architecto, animi veritatis vitae rem blanditiis harum a tempora saepe accusantium, ab expedita odit aliquid? Corporis?</p> --}}
                                <p class="team-desc">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nihil, qui.</p>
                                
                                <div class="team-skills">
                                    <span class="team-skill-badge">Analysis</span>
                                    <span class="team-skill-badge">Documentation</span>
                                    {{-- <span class="team-skill-badge">UML</span> --}}
                                </div>
                                
                                <div class="team-quote"><marquee behavior="" onmouseover="this.stop();" onmouseout="this.start();" direction="left">I'm 18 Y.O an <b>Electrical Engineering Student</b> 😎</marquee></div>
                                
                                <div class="team-social">
                                    <a href="https://github.com/BayuBramasta" class="team-social-link"><i class="bi bi-github"></i></a>
                                    <a href="#" class="team-social-link"><i class="bi bi-envelope-fill"></i></a>
                                    <a href="https://www.facebook.com/share/1E46oyY1m8/" class="team-social-link"><i class="bi bi-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Member 4: Report Manager --}}
                <div class="col-md-6 col-lg-3">
                    <div class="team-card-container" onclick="this.classList.toggle('flipped')">
                        <div class="team-card-flipper" style="--card-color: #ffc107; --card-rgb: 255, 193, 7;">
                            <div class="team-card-front">
                                <div class="team-avatar">
                                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                                </div>
                                <div class="team-role-main text-center">UI/UX<br>Designer</div>
                                <div class="click-hint"><i class="bi bi-hand-index-thumb"></i> Ketuk untuk detail</div>
                            </div>
                            <div class="team-card-back">
                                <h5 class="team-name">M. Iqbal Fathyyatan</h5>
                                <div class="team-role" style="color: #d39e00; background: rgba(255, 193, 7, 0.15);">UI/UX Designer</div>
                                {{-- <p class="team-desc" data-i18n="team.rm_desc">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Maiores, excepturi?</p> --}}
                                <p class="team-desc">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Maiores, excepturi?</p>
                                
                                <div class="team-skills">
                                    <span class="team-skill-badge">Design</span>
                                    <span class="team-skill-badge">Prototyping</span>
                                    <span class="team-skill-badge">User Research</span>
                                </div>
                                
                                <div class="team-quote">Iqbal breathing</div>
                                
                                <div class="team-social">
                                    <a href="#" class="team-social-link"><i class="bi bi-instagram"></i></a>
                                    <a href="#" class="team-social-link"><i class="bi bi-envelope-fill"></i></a>
                                    <a href="#" class="team-social-link"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            

            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section" id="login" style="background: var(--light-bg);">
        <div class="container">
            <h2 class="section-title mb-2" style="font-weight: 800;" data-i18n="login.title">Masuk ke Sistem</h2>
            <p class="section-subtitle mb-4" data-i18n="login.subtitle">Pilih sesuai peran Anda</p>
            <div class="row g-4 justify-content-center">
                <!-- Staff Login -->
                <div class="col-md-5">
                    <div class="login-card">
                        <div class="icon-wrapper" style="background: #fff3cd; color: #856404;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h4 data-i18n="login.staff_title">Login Staf</h4>
                        <p data-i18n="login.staff_desc">Untuk admin dan kasir. Kelola toko, transaksi, stok, dan laporan.</p>
                        <a href="{{ route('staff.login') }}" class="btn-login-outline">
                            <i class="bi bi-box-arrow-in-right"></i> <span data-i18n="login.staff_btn">Login Staf</span>
                        </a>
                    </div>
                </div>
                <!-- Customer Login -->
                <div class="col-md-5">
                    <div class="login-card">
                        <div class="icon-wrapper" style="background: #d4edda; color: #155724;">
                            <i class="bi bi-bag-heart-fill"></i>
                        </div>
                        <h4 data-i18n="login.cust_title">Login Pelanggan</h4>
                        <p data-i18n="login.cust_desc">Untuk memesan makanan secara online. Akun diberikan oleh kasir toko.</p>
                        <a href="{{ route('pelanggan.login') }}" class="btn-login-primary">
                            <i class="bi bi-bag-heart"></i> <span data-i18n="login.cust_btn">Login Pelanggan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <p class="mb-0" data-i18n="footer.text">&copy; {{ date('Y') }} SMEGABIZ — Rekayasa Perangkat Lunak Project 2026</p>
        </div>
    </footer>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/i18next.min.js') }}"></script>
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
                    subtitle: "Kami adalah 4 siswa dari konsentrasi Rekayasa Perangkat Lunak (RPL) SMKN 10 Surabaya yang berkolaborasi membangun sistem ini sebagai proyek nyata. Setiap anggota membawa peran dan keahlian masing-masing untuk mewujudkan SMEGABIZ."
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
                footer: { text: "© 2026 SMEGABIZ — Rekayasa Perangkat Lunak Project 2026" }
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
                    subtitle: "We are 4 students from the Software Engineering (RPL) major at SMKN 10 Surabaya who collaborated to build this system as a real-world project. Each member brings their own role and expertise to bring SMEGABIZ to life."
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
                footer: { text: "© 2026 SMEGABIZ — Software Engineering Project 2026" }
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