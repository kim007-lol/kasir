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

    <!-- Operating Hours -->
    <section class="hours-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="hours-card">
                        <i class="bi bi-clock-fill text-primary" style="font-size: 2.5rem;"></i>
                        <h4 class="mt-3 fw-bold" data-i18n="hours.title">Jam Operasional</h4>
                        <p class="text-muted mb-2" data-i18n="hours.desc">Pemesanan online hanya tersedia pada:</p>
                        <h3 class="fw-bold" style="color: var(--primary);">
                            <i class="bi bi-sun-fill"></i> 07:00 — 15:00 WIB
                        </h3>
                        <p class="text-muted small mt-2 mb-0" data-i18n="hours.days">Senin — Jumat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang / About Section -->
    <section class="features" id="tentang" style="background: white;">
        <div class="container">
            <h2 class="section-title" data-i18n="about.title">Tentang Kami</h2>
            <p class="section-subtitle" data-i18n="about.subtitle">Mengenal lebih dekat SMEGABIZ, SMKN 10 Surabaya &amp; Alfamart Class</p>

            <!-- Tab Navigation -->
            <ul class="nav nav-pills nav-fill mb-4 mx-auto" style="max-width: 700px;" id="aboutTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="sekolah-tab" data-bs-toggle="tab" data-bs-target="#sekolah-profile" type="button" role="tab">
                        <i class="bi bi-building me-1"></i> <span data-i18n="about.tab_sekolah">Profile Sekolah</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="smegabiz-tab" data-bs-toggle="tab" data-bs-target="#smegabiz-profile" type="button" role="tab">
                        <i class="bi bi-shop me-1"></i> <span data-i18n="about.tab_smegabiz">SMEGABIZ</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="alfamart-tab" data-bs-toggle="tab" data-bs-target="#alfamart-profile" type="button" role="tab">
                        <i class="bi bi-mortarboard me-1"></i> <span data-i18n="about.tab_alfamart">Alfamart Class</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="aboutTabsContent">

                <!-- ==================== TAB 1: PROFILE SEKOLAH ==================== -->
                <div class="tab-pane fade show active" id="sekolah-profile" role="tabpanel">
                    <div class="row align-items-center mb-4">
                        <div class="col-lg-8">
                            <h3 class="fw-bold mb-3" style="color: var(--primary);">SMK Negeri 10 Surabaya</h3>
                            <p class="lead text-muted" data-i18n="sch.lead">Sekolah vokasi unggulan di kawasan pendidikan Keputih – Sukolilo – Surabaya. Berorientasi pada prestasi akademik dan pembentukan lulusan yang kompeten, berakhlak mulia, serta siap menghadapi tantangan global.</p>
                            <p class="text-secondary" data-i18n="sch.desc1">Sekolah ini memiliki akreditasi <strong>A</strong> dan berdiri sejak tahun <strong>1970</strong> dengan nama awal SMEA 3 Surabaya. Seiring perkembangan zaman, sekolah ini bertransformasi menjadi institusi vokasi yang adaptif dan berdaya saing tinggi. Berstatus <strong>Pusat Keunggulan</strong> dan <strong>BLUD</strong> (Badan Layanan Umum Daerah).</p>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <div class="p-4 bg-light rounded shadow-sm" style="color: var(--primary);">
                                <i class="bi bi-building" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold mt-2 mb-0">SMKN 10</h4>
                                <p class="small mb-0 opacity-75">Surabaya</p>
                                <hr class="my-2 mx-auto" style="width: 50px;">
                                <p class="small mb-0 fst-italic" data-i18n="sch.accred_badge">Akreditasi A • Sejak 1970</p>
                            </div>
                        </div>
                    </div>

                    <!-- Visi & Misi -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <div class="feature-card text-start" style="border-left: 4px solid var(--primary);">
                                <h5 class="fw-bold mb-2"><i class="bi bi-eye-fill text-primary me-2"></i> <span data-i18n="sch.visi_title">Visi</span></h5>
                                <p class="text-secondary mb-0 fst-italic" data-i18n="sch.visi">Terwujudnya SMK Negeri 10 Surabaya yang HEBAT.</p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="feature-card text-start" style="border-left: 4px solid #28a745;">
                                <h5 class="fw-bold mb-2"><i class="bi bi-list-check text-success me-2"></i> <span data-i18n="sch.misi_title">Misi</span></h5>
                                <ol class="small text-secondary mb-0 ps-3">
                                    <li data-i18n="sch.misi1">Mewujudkan sekolah ramah anak, menyenangkan, berwawasan lingkungan, serta memberikan pelayanan kepada stakeholder.</li>
                                    <li data-i18n="sch.misi2">Meningkatkan keunggulan SDM dan kompetensi peserta didik sesuai standar kelulusan.</li>
                                    <li data-i18n="sch.misi3">Mewujudkan SDM berkarakter mulia.</li>
                                    <li data-i18n="sch.misi4">Menjalankan ISO menuju sekolah adaptif dan akuntabel.</li>
                                    <li data-i18n="sch.misi5">Memperkuat link and match dengan stakeholder untuk meningkatkan daya saing.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Konsentrasi Keahlian -->
                    <h5 class="fw-bold mb-3"><i class="bi bi-bookmark-star-fill text-warning me-2"></i> <span data-i18n="sch.keahlian_title">Konsentrasi Keahlian</span></h5>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-palette-fill text-danger" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">DKV</p><p class="small text-muted mb-0" data-i18n="sch.k1">Desain Komunikasi Visual</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-code-slash text-primary" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">RPL</p><p class="small text-muted mb-0" data-i18n="sch.k2">Rekayasa Perangkat Lunak</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-capsule text-info" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">LPK3</p><p class="small text-muted mb-0" data-i18n="sch.k3">Layanan Kefarmasian</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-airplane-fill text-success" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">ULW</p><p class="small text-muted mb-0" data-i18n="sch.k4">Usaha Layanan Wisata</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-file-earmark-text-fill text-secondary" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">MP</p><p class="small text-muted mb-0" data-i18n="sch.k5">Manajemen Perkantoran</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-cart4 text-warning" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">BD</p><p class="small text-muted mb-0" data-i18n="sch.k6">Bisnis Digital</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-calculator-fill text-dark" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">AK</p><p class="small text-muted mb-0" data-i18n="sch.k7">Akuntansi</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-bank text-primary" style="font-size:1.5rem;"></i><p class="small fw-bold mb-0 mt-1">PBK</p><p class="small text-muted mb-0" data-i18n="sch.k8">Perbankan</p></div></div>
                    </div>



                    <!-- Kontak -->
                    <div class="border-top pt-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <span data-i18n="sch.contact_title">Kontak SMKN 10 Surabaya</span></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="small text-secondary mb-1"><strong><span data-i18n="sch.addr">Alamat</span>:</strong> Jl. Keputih Tegal, Sukolilo, Surabaya</p>
                                <p class="small text-secondary mb-1"><strong><span data-i18n="sch.phone">Telepon</span>:</strong> (031) 5937654</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="small text-secondary mb-1"><strong>Email:</strong> info@smkn10sby.sch.id</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== TAB 2: SMEGABIZ BUSINESS CENTRE ==================== -->
                <div class="tab-pane fade" id="smegabiz-profile" role="tabpanel">
                    <div class="row align-items-center mb-4">
                        <div class="col-lg-8">
                            <h3 class="fw-bold text-success mb-3">SMEGABIZ Business Centre</h3>
                            <p class="lead text-muted" data-i18n="sme.lead">Business center, unit produksi, dan laboratorium bisnis nyata di lingkungan sekolah — khususnya untuk konsentrasi keahlian Bisnis Digital.</p>
                            <p class="text-secondary" data-i18n="sme.desc1">SMEGABIZ menjadi jembatan antara teori dan praktik. Siswa dilatih untuk mengelola toko, melayani pelanggan, mengatur stok, display barang, melakukan transaksi, pembukuan, hingga strategi pemasaran.</p>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <div class="p-4 bg-light rounded text-success shadow-sm">
                                <h2 class="fw-bold mb-0">SMEGABIZ</h2>
                                <p class="small mb-0 opacity-75 text-uppercase">Business Centre</p>
                                <hr class="my-2 mx-auto" style="width: 50px;">
                                <p class="small mb-0 fst-italic" data-i18n="sme.est_badge">Didirikan 19 Juli 2010</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sejarah & Tujuan -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="feature-card text-start" style="border-left: 4px solid #28a745;">
                                <h5 class="fw-bold mb-3"><i class="bi bi-history text-success me-2"></i> <span data-i18n="sme.hist_title">Sejarah</span></h5>
                                <p class="small text-secondary mb-0" data-i18n="sme.hist_desc">Didirikan pada 19 Juli 2010 dengan modal awal Rp 100.000.000, berfokus pada perdagangan ritel (minimarket). Pada tahun 2015, dilakukan kerja sama dengan Alfamart meliputi renovasi toko, dukungan manajemen operasional, dan standarisasi sistem kerja retail modern.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card text-start" style="border-left: 4px solid var(--primary);">
                                <h5 class="fw-bold mb-3"><i class="bi bi-bullseye text-primary me-2"></i> <span data-i18n="sme.goal_title">Tujuan</span></h5>
                                <ul class="small text-secondary mb-0 ps-3">
                                    <li data-i18n="sme.goal1">Meningkatkan kompetensi siswa</li>
                                    <li data-i18n="sme.goal2">Media pra-magang sebelum PKL</li>
                                    <li data-i18n="sme.goal3">Memberikan pengalaman industri nyata</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Operasional -->
                    <div class="mb-4">
                        <div class="feature-card d-inline-flex align-items-center gap-3 text-start px-4 py-3" style="border-left: 4px solid var(--primary);">
                            <i class="bi bi-clock-fill text-primary" style="font-size: 1.6rem;"></i>
                            <div>
                                <p class="fw-bold mb-0" style="color: var(--primary);">06.30 – 15.00 WIB</p>
                                <p class="small text-muted mb-0">Senin – Jumat</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aktivitas Siswa -->
                    <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-success me-2"></i> <span data-i18n="sme.activity_title">Aktivitas Siswa di SMEGABIZ</span></h5>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-basket3-fill text-primary" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a1">Melayani Konsumen</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-upc-scan text-success" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a2">Transaksi Kasir</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-boxes text-warning" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a3">Stock Opname</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-grid-fill text-info" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a4">Display Barang</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-truck text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a5">Menerima Barang</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-journal-text text-dark" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a6">Pembukuan</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-droplet-half text-secondary" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a7">Kebersihan Toko</p></div></div>
                        <div class="col-6 col-md-3"><div class="feature-card text-center py-3"><i class="bi bi-archive-fill text-primary" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="sme.a8">Pengelolaan Gudang</p></div></div>
                    </div>

                    <!-- Struktur Organisasi -->
                    <div class="border-top pt-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3-fill text-primary me-2"></i> <span data-i18n="sme.org_title">Struktur Organisasi 2024/2025</span></h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-secondary">
                                    <p class="mb-1"><strong><span data-i18n="sme.org_pj">Penanggung Jawab</span>:</strong> Imam Soetopo, S.Pd., M.Pd</p>
                                    <p class="mb-1"><strong><span data-i18n="sme.org_mitra">Mitra Usaha</span>:</strong> PT. Sumber Alfaria Trijaya, Tbk</p>
                                    <p class="mb-1"><strong><span data-i18n="sme.org_ketua">Ketua Pengelola</span>:</strong> Devi Diana Safitri, S.Pd</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-secondary">
                                    <p class="mb-1"><strong><span data-i18n="sme.org_pembina">Pembina</span>:</strong> Zaima Faiza Hakim, S.Pd &amp; Roro Hami H.W, S.Pd</p>
                                    <p class="mb-1"><strong><span data-i18n="sme.org_pendamping">Pendamping</span>:</strong> Achmad Syaifudin Zuhri</p>
                                    <p class="mb-1"><strong><span data-i18n="sme.org_pelaksana">Pelaksana Harian</span>:</strong> <span data-i18n="sme.org_pelaksana_val">Siswa kelas X dan XI</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== TAB 3: ALFAMART CLASS ==================== -->
                <div class="tab-pane fade" id="alfamart-profile" role="tabpanel">
                    <div class="row align-items-center mb-4">
                        <div class="col-lg-8">
                            <h3 class="fw-bold mb-3" style="color: #e41e2d;">Alfamart Class</h3>
                            <p class="lead text-muted" data-i18n="alfa.lead">Sejak tahun 2015, SMKN 10 Surabaya menjalin kerja sama dengan PT. Sumber Alfaria Trijaya, Tbk (Alfamart).</p>
                            <p class="text-secondary" data-i18n="alfa.desc1">Alfamart Class merupakan kelas industri berbasis <em>teaching factory</em> yang bertujuan mencetak tenaga kerja profesional dan adaptif, siap terjun ke dunia usaha dan dunia industri (DUDI) — khususnya sektor ritel modern berbasis digital.</p>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <div class="p-4 bg-light rounded shadow-sm" style="color: #e41e2d;">
                                <i class="bi bi-mortarboard-fill" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold mt-2 mb-0">Alfamart Class</h4>
                                <p class="small mb-0 opacity-75" data-i18n="alfa.badge">Kelas Industri Berbasis Teaching Factory</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ruang Lingkup -->
                    <h5 class="fw-bold mb-3"><i class="bi bi-list-task text-danger me-2"></i> <span data-i18n="alfa.scope_title">Ruang Lingkup Kegiatan</span></h5>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-book text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s1">Sinkronisasi Kurikulum</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-person-check text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s2">Seleksi Siswa</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-camera-video text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s3">Pembelajaran Daring & Luring</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-person-badge text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s4">Instruktur Tamu Alfamart</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-tools text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s5">Pelatihan Softskills & Hardskills</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-briefcase-fill text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s6">Praktik Kerja Lapangan</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-clipboard-data text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s7">Monitoring & Evaluasi</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-building text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s8">Visitasi Business Center</p></div></div>
                        <div class="col-6 col-md-4"><div class="feature-card text-center py-3" style="border-top: 3px solid #e41e2d;"><i class="bi bi-people text-danger" style="font-size:1.3rem;"></i><p class="small fw-bold mb-0 mt-1" data-i18n="alfa.s9">Perekrutan Siswa</p></div></div>
                    </div>



                    <!-- Implementasi Kurikulum -->
                    <h5 class="fw-bold mb-3"><i class="bi bi-journal-bookmark-fill text-danger me-2"></i> <span data-i18n="alfa.kur_title">Implementasi Kurikulum</span></h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="feature-card text-start" style="border-left: 4px solid #e41e2d;">
                                <h6 class="fw-bold"><span data-i18n="alfa.fase_e">Fase E (Kelas X)</span></h6>
                                <ul class="small text-secondary mb-0 ps-3">
                                    <li data-i18n="alfa.fase_e1">Dasar-Dasar Pemasaran</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card text-start" style="border-left: 4px solid #e41e2d;">
                                <h6 class="fw-bold"><span data-i18n="alfa.fase_f">Fase F (Kelas XI & XII)</span></h6>
                                <ul class="small text-secondary mb-0 ps-3">
                                    <li>Visual Merchandising</li>
                                    <li data-i18n="alfa.fase_f2">Administrasi Transaksi</li>
                                    <li data-i18n="alfa.fase_f3">Konsentrasi Bisnis Digital</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border text-center small mb-0">
                        <i class="bi bi-award-fill text-danger me-1"></i>
                        <span data-i18n="alfa.closing">Tujuan akhir: Menghasilkan lulusan dengan hardskills dan softskills yang sesuai kebutuhan DUDI (Dunia Usaha dan Dunia Industri), khususnya sektor ritel modern berbasis digital.</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/i18next@23.5.1/dist/umd/i18next.min.js"></script>
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
                hours: {
                    title: "Jam Operasional",
                    desc: "Pemesanan online hanya tersedia pada:",
                    days: "Senin — Jumat"
                },
                about: {
                    title: "Tentang Kami",
                    subtitle: "Mengenal lebih dekat SMEGABIZ, SMKN 10 Surabaya & Alfamart Class",
                    tab_sekolah: "Profile Sekolah",
                    tab_smegabiz: "SMEGABIZ",
                    tab_alfamart: "Alfamart Class"
                },
                sch: {
                    lead: "Sekolah vokasi unggulan di kawasan pendidikan Keputih – Sukolilo – Surabaya. Berorientasi pada prestasi akademik dan pembentukan lulusan yang kompeten, berakhlak mulia, serta siap menghadapi tantangan global.",
                    desc1: "Sekolah ini memiliki akreditasi <strong>A</strong> dan berdiri sejak tahun <strong>1970</strong> dengan nama awal SMEA 3 Surabaya. Seiring perkembangan zaman, sekolah ini bertransformasi menjadi institusi vokasi yang adaptif dan berdaya saing tinggi. Berstatus <strong>Pusat Keunggulan</strong> dan <strong>BLUD</strong> (Badan Layanan Umum Daerah).",
                    accred_badge: "Akreditasi A • Sejak 1970",
                    visi_title: "Visi", visi: "Terwujudnya SMK Negeri 10 Surabaya yang HEBAT.",
                    misi_title: "Misi",
                    misi1: "Mewujudkan sekolah ramah anak, menyenangkan, berwawasan lingkungan, serta memberikan pelayanan kepada stakeholder.",
                    misi2: "Meningkatkan keunggulan SDM dan kompetensi peserta didik sesuai standar kelulusan.",
                    misi3: "Mewujudkan SDM berkarakter mulia.",
                    misi4: "Menjalankan ISO menuju sekolah adaptif dan akuntabel.",
                    misi5: "Memperkuat link and match dengan stakeholder untuk meningkatkan daya saing.",
                    keahlian_title: "Konsentrasi Keahlian",
                    k1: "Desain Komunikasi Visual", k2: "Rekayasa Perangkat Lunak",
                    k3: "Layanan Kefarmasian", k4: "Usaha Layanan Wisata",
                    k5: "Manajemen Perkantoran", k6: "Bisnis Digital",
                    k7: "Akuntansi", k8: "Perbankan",
                    stat_luas: "Luas Lahan", stat_siswa: "Jumlah Siswa", stat_ptk: "Jumlah PTK",
                    contact_title: "Kontak SMKN 10 Surabaya", addr: "Alamat", phone: "Telepon"
                },
                sme: {
                    lead: "Business center, unit produksi, dan laboratorium bisnis nyata di lingkungan sekolah — khususnya untuk konsentrasi keahlian Bisnis Digital.",
                    desc1: "SMEGABIZ menjadi jembatan antara teori dan praktik. Siswa dilatih untuk mengelola toko, melayani pelanggan, mengatur stok, display barang, melakukan transaksi, pembukuan, hingga strategi pemasaran.",
                    est_badge: "Didirikan 19 Juli 2010",
                    hist_title: "Sejarah",
                    hist_desc: "Didirikan pada 19 Juli 2010 dengan modal awal Rp 100.000.000, berfokus pada perdagangan ritel (minimarket). Pada tahun 2015, dilakukan kerja sama dengan Alfamart meliputi renovasi toko, dukungan manajemen operasional, dan standarisasi sistem kerja retail modern.",
                    goal_title: "Tujuan",
                    goal1: "Meningkatkan kompetensi siswa",
                    goal2: "Media pra-magang sebelum PKL",
                    goal3: "Memberikan pengalaman industri nyata",
                    hours_title: "Jam Operasional", hours_days: "Senin – Jumat",
                    omset_title: "Total Omset", profit_title: "Laba Bersih",
                    activity_title: "Aktivitas Siswa di SMEGABIZ",
                    a1: "Melayani Konsumen", a2: "Transaksi Kasir", a3: "Stock Opname", a4: "Display Barang",
                    a5: "Menerima Barang", a6: "Pembukuan", a7: "Kebersihan Toko", a8: "Pengelolaan Gudang",
                    org_title: "Struktur Organisasi 2024/2025",
                    org_pj: "Penanggung Jawab", org_mitra: "Mitra Usaha", org_ketua: "Ketua Pengelola",
                    org_pembina: "Pembina", org_pendamping: "Pendamping", org_pelaksana: "Pelaksana Harian",
                    org_pelaksana_val: "Siswa kelas X dan XI"
                },
                alfa: {
                    lead: "Sejak tahun 2015, SMKN 10 Surabaya menjalin kerja sama dengan PT. Sumber Alfaria Trijaya, Tbk (Alfamart).",
                    desc1: "Alfamart Class merupakan kelas industri berbasis <em>teaching factory</em> yang bertujuan mencetak tenaga kerja profesional dan adaptif, siap terjun ke dunia usaha dan dunia industri (DUDI) — khususnya sektor ritel modern berbasis digital.",
                    badge: "Kelas Industri Berbasis Teaching Factory",
                    scope_title: "Ruang Lingkup Kegiatan",
                    s1: "Sinkronisasi Kurikulum", s2: "Seleksi Siswa", s3: "Pembelajaran Daring & Luring",
                    s4: "Instruktur Tamu Alfamart", s5: "Pelatihan Softskills & Hardskills", s6: "Praktik Kerja Lapangan",
                    s7: "Monitoring & Evaluasi", s8: "Visitasi Business Center", s9: "Perekrutan Siswa",
                    data_title: "Jumlah Siswa Alfamart Class (5 Tahun Terakhir)",
                    th_tahun: "Tahun Ajaran", th_total: "Jumlah",
                    kur_title: "Implementasi Kurikulum",
                    fase_e: "Fase E (Kelas X)", fase_e1: "Dasar-Dasar Pemasaran",
                    fase_f: "Fase F (Kelas XI & XII)", fase_f2: "Administrasi Transaksi", fase_f3: "Konsentrasi Bisnis Digital",
                    closing: "Tujuan akhir: Menghasilkan lulusan dengan hardskills dan softskills yang sesuai kebutuhan DUDI (Dunia Usaha dan Dunia Industri), khususnya sektor ritel modern berbasis digital."
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
                hours: {
                    title: "Operating Hours",
                    desc: "Online ordering is only available during:",
                    days: "Monday — Friday"
                },
                about: {
                    title: "About Us",
                    subtitle: "Get to know SMEGABIZ, SMKN 10 Surabaya & Alfamart Class",
                    tab_sekolah: "School Profile",
                    tab_smegabiz: "SMEGABIZ",
                    tab_alfamart: "Alfamart Class"
                },
                sch: {
                    lead: "A leading vocational school in the Keputih – Sukolilo – Surabaya educational area. Oriented towards academic achievement and producing competent, noble, and globally-ready graduates.",
                    desc1: "The school holds <strong>A accreditation</strong> and was established in <strong>1970</strong> under the name SMEA 3 Surabaya. Over time, it has transformed into an adaptive and highly competitive vocational institution. It holds the status of <strong>Center of Excellence</strong> and <strong>BLUD</strong> (Regional Public Service Agency).",
                    accred_badge: "A Accreditation • Since 1970",
                    visi_title: "Vision", visi: "The realization of SMKN 10 Surabaya as a GREAT school.",
                    misi_title: "Mission",
                    misi1: "Creating a child-friendly, enjoyable, environmentally-conscious school that serves stakeholders.",
                    misi2: "Enhancing HR excellence and student competencies according to graduation standards.",
                    misi3: "Developing noble character in human resources.",
                    misi4: "Implementing ISO towards an adaptive and accountable school.",
                    misi5: "Strengthening link and match with stakeholders to increase competitiveness.",
                    keahlian_title: "Fields of Expertise",
                    k1: "Visual Communication Design", k2: "Software Engineering",
                    k3: "Pharmaceutical Services", k4: "Tourism Services",
                    k5: "Office Management", k6: "Digital Business",
                    k7: "Accounting", k8: "Banking",
                    stat_luas: "Land Area", stat_siswa: "Total Students", stat_ptk: "Total Staff",
                    contact_title: "Contact SMKN 10 Surabaya", addr: "Address", phone: "Phone"
                },
                sme: {
                    lead: "A business center, production unit, and real business laboratory within the school — specifically for the Digital Business concentration.",
                    desc1: "SMEGABIZ bridges theory and practice. Students are trained to manage stores, serve customers, manage stock, display products, process transactions, bookkeeping, and marketing strategies.",
                    est_badge: "Established July 19, 2010",
                    hist_title: "History",
                    hist_desc: "Established on July 19, 2010 with an initial capital of Rp 100,000,000, focusing on retail trade (minimarket). In 2015, a partnership with Alfamart was formed, including store renovation, operational management support, and standardization of modern retail work systems.",
                    goal_title: "Objectives",
                    goal1: "Improving student competencies",
                    goal2: "Pre-internship preparation before PKL",
                    goal3: "Providing real industry experience",
                    hours_title: "Operating Hours", hours_days: "Monday – Friday",
                    omset_title: "Total Revenue", profit_title: "Net Profit",
                    activity_title: "Student Activities at SMEGABIZ",
                    a1: "Serving Customers", a2: "Cashier Transactions", a3: "Stock Taking", a4: "Product Display",
                    a5: "Receiving Goods", a6: "Bookkeeping", a7: "Store Cleaning", a8: "Warehouse Management",
                    org_title: "Organizational Structure 2024/2025",
                    org_pj: "Person in Charge", org_mitra: "Business Partner", org_ketua: "Head Manager",
                    org_pembina: "Supervisor", org_pendamping: "Companion", org_pelaksana: "Daily Operations",
                    org_pelaksana_val: "Grade X and XI Students"
                },
                alfa: {
                    lead: "Since 2015, SMKN 10 Surabaya has partnered with PT. Sumber Alfaria Trijaya, Tbk (Alfamart).",
                    desc1: "Alfamart Class is an industry-based <em>teaching factory</em> class aimed at producing professional and adaptive workers, ready to enter the business and industrial world (DUDI) — especially in the digital-based modern retail sector.",
                    badge: "Industry Class Based on Teaching Factory",
                    scope_title: "Scope of Activities",
                    s1: "Curriculum Synchronization", s2: "Student Selection", s3: "Online & Offline Learning",
                    s4: "Alfamart Guest Instructors", s5: "Soft & Hard Skills Training", s6: "Field Work Practice",
                    s7: "Monitoring & Evaluation", s8: "Business Center Visits", s9: "Student Recruitment",
                    data_title: "Alfamart Class Students (Last 5 Years)",
                    th_tahun: "Academic Year", th_total: "Total",
                    kur_title: "Curriculum Implementation",
                    fase_e: "Phase E (Grade X)", fase_e1: "Marketing Fundamentals",
                    fase_f: "Phase F (Grade XI & XII)", fase_f2: "Transaction Administration", fase_f3: "Digital Business Concentration",
                    closing: "Ultimate goal: Producing graduates with hard skills and soft skills that meet the needs of DUDI (Business and Industrial World), especially the digital-based modern retail sector."
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