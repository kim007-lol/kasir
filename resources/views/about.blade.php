@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container py-4">
    <!-- Language Toggle -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group shadow-sm" role="group" aria-label="Language Toggle">
            <button type="button" class="btn btn-outline-primary btn-sm fw-bold active" id="btn-id" onclick="changeLanguage('id')">ID</button>
            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" id="btn-en" onclick="changeLanguage('en')">EN</button>
        </div>
    </div>

    <!-- Sub-Navigation (Navbar Kecil) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-3" id="website-tab" data-bs-toggle="tab" data-bs-target="#website-profile" type="button" role="tab" aria-controls="website-profile" aria-selected="true">
                        <i class="bi bi-globe me-2"></i> <span data-i18n="nav.website">PROFILE WEBSITE</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3" id="smegabiz-tab" data-bs-toggle="tab" data-bs-target="#smegabiz-profile" type="button" role="tab" aria-controls="smegabiz-profile" aria-selected="false">
                        <i class="bi bi-briefcase me-2"></i> <span data-i18n="nav.smegabiz">PROFILE SMEGABIZ</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="profileTabsContent">
        <!-- Profile Website Section -->
        <div class="tab-pane fade show active" id="website-profile" role="tabpanel" aria-labelledby="website-tab">
            <div class="card border-0 shadow-sm p-4 text-dark">
                <div class="row align-items-center mb-4">
                    <div class="col-lg-7">
                        <h3 class="fw-bold text-primary mb-3" data-i18n="web.title">Tentang SMEGABIZ</h3>
                        <p class="lead text-muted" data-i18n="web.lead">Sistem Point of Sales (POS) dan Manajemen Gudang yang komprehensif untuk mendukung operasional bisnis retail masa kini.</p>
                        <hr class="my-4 opacity-25">
                        <p class="text-secondary" data-i18n="web.desc1">
                            <strong>SMEGABIZ</strong> hadir sebagai solusi digital terintegrasi yang memudahkan pengelolaan stok barang, transaksi penjualan, hingga pemantauan laporan keuangan secara realtime. Aplikasi ini dirancang untuk memberikan kecepatan bagi kasir dan akurasi data bagi pemilik bisnis.
                        </p>
                        <ul class="text-secondary">
                            <li><strong data-i18n="web.feat1_title">Manajemen Inventaris:</strong> <span data-i18n="web.feat1_desc">Monitoring stok gudang dan item kasir secara akurat.</span></li>
                            <li><strong data-i18n="web.feat2_title">Sistem Transaksi:</strong> <span data-i18n="web.feat2_desc">Proses checkout yang cepat dengan fitur keranjang belanja.</span></li>
                            <li><strong data-i18n="web.feat3_title">Manajemen Supplier & Member:</strong> <span data-i18n="web.feat3_desc">Kelola data pemasok dan pelanggan setia dalam satu tempat.</span></li>
                            <li><strong data-i18n="web.feat4_title">Laporan Pintar:</strong> <span data-i18n="web.feat4_desc">Ekspor laporan penjualan dan stok ke format PDF atau Excel.</span></li>
                        </ul>
                    </div>
                    <div class="col-lg-5 text-center">
                        <div class="website-icon-display" style="font-size: 6rem; color: #0d6efd; opacity: 0.15;">
                            <i class="bi bi-display"></i>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <i class="bi bi-lightning-fill text-warning h2 mb-2"></i>
                            <h5 class="fw-bold" data-i18n="web.tag1_title">Cepat</h5>
                            <p class="small text-muted mb-0" data-i18n="web.tag1_desc">Transaksi hitungan detik</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <i class="bi bi-shield-lock-fill text-success h2 mb-2"></i>
                            <h5 class="fw-bold" data-i18n="web.tag2_title">Aman</h5>
                            <p class="small text-muted mb-0" data-i18n="web.tag2_desc">Data terenkripsi & privat</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light text-center h-100">
                            <i class="bi bi-graph-up-arrow text-info h2 mb-2"></i>
                            <h5 class="fw-bold" data-i18n="web.tag3_title">Analitik</h5>
                            <p class="small text-muted mb-0" data-i18n="web.tag3_desc">Laporan realtime & akurat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile SMEGABIZ Section -->
        <div class="tab-pane fade" id="smegabiz-profile" role="tabpanel" aria-labelledby="smegabiz-tab">
            <div class="card border-0 shadow-sm p-4 border-start border-4 border-success text-dark">
                <div class="row align-items-center mb-5">
                    <div class="col-lg-8">
                        <h3 class="fw-bold text-success mb-3">SMEGABIZ Business Centre</h3>
                        <p class="lead text-muted" data-i18n="sme.lead">Laboratorium ritel (mini-market) unggulan di lingkungan SMK Negeri 10 Surabaya.</p>
                        <p class="text-secondary" data-i18n="sme.desc1">
                            SMEGABIZ Business Centre merupakan fasilitas pendidikan vokasi yang dibangun melalui kerjasama strategis antara <strong>SMKN 10 Surabaya</strong> dengan <strong>Alfamart</strong> dalam program <em>Alfamart Class (link & match)</em>. Diresmikan sejak tahun 2015, fasilitas ini menjadi tempat praktik langsung bagi siswa jurusan Bisnis Ritel dan Bisnis Digital.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="smegabiz-logo-display p-4 bg-light rounded text-success shadow-sm">
                            <h2 class="fw-bold mb-0">SMEGABIZ</h2>
                            <p class="small mb-0 opacity-75 text-uppercase">Business Centre</p>
                            <hr class="my-2 mx-auto" style="width: 50px;">
                            <p class="small mb-0 fst-italic">SMKN 10 Surabaya</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded h-100">
                            <h5 class="fw-bold mb-3"><i class="bi bi-history text-success me-2"></i> <span data-i18n="sme.hist_title">Sejarah & Latar Belakang</span></h5>
                            <p class="small text-secondary mb-0" data-i18n="sme.hist_desc">
                                Berawal pada tahun 2015 melalui donasi minimarket dari PT Sumber Alfaria Trijaya (Alfamart) sebagai bagian dari program CSR. Program ini menggabungkan teori manajemen ritel selama dua tahun di kelas dengan praktik nyata (PKL) selama 3-4 bulan di laboratorium sekolah bagi siswa kelas XII.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded h-100">
                            <h5 class="fw-bold mb-3"><i class="bi bi-box-seam text-success me-2"></i> <span data-i18n="sme.fac_title">Fasilitas Modern</span></h5>
                            <p class="small text-secondary mb-0" data-i18n="sme.fac_desc">
                                Beroperasi layaknya minimarket komersial dengan peralatan kasir lengkap, rak display standar industri, serta sistem pembayaran modern termasuk QRIS. Dirancang khusus untuk mengasah kompetensi siswa di bidang e-commerce, pemasaran digital, dan pengelolaan usaha retail.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-5 text-dark">
                    <h5 class="fw-bold mb-4 text-center" data-i18n="sme.act_title">Kegiatan Utama SMEGABIZ</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-none bg-white p-3 h-100 border-top border-3 border-primary">
                                <h6 class="fw-bold"><i class="bi bi-shop me-2"></i> <span data-i18n="sme.act1_title">Operasional Toko</span></h6>
                                <p class="small text-muted mb-0" data-i18n="sme.act1_desc">Melayani kebutuhan harian siswa & pegawai dengan sistem kasir profesional.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-none bg-white p-3 h-100 border-top border-3 border-warning">
                                <h6 class="fw-bold"><i class="bi bi-person-workspace me-2"></i> <span data-i18n="sme.act2_title">Praktik PKL</span></h6>
                                <p class="small text-muted mb-0" data-i18n="sme.act2_desc">Implementasi manajemen ritel & pengalaman kerja nyata dalam durasi 3-4 bulan.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-none bg-white p-3 h-100 border-top border-3 border-info">
                                <h6 class="fw-bold"><i class="bi bi-mortarboard me-2"></i> <span data-i18n="sme.act3_title">Alfamart Mengajar</span></h6>
                                <p class="small text-muted mb-0" data-i18n="sme.act3_desc">Pelatihan rutin & pembekalan karir langsung dari mentor pakar industri.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-success text-white p-4 rounded mb-5 shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-lg-9 text-white">
                            <h5 class="fw-bold mb-2" data-i18n="sme.cap_title">Capaian & Kolaborasi Unggulan</h5>
                            <p class="small mb-0 opacity-90" data-i18n="sme.cap_desc">
                                Sebagai salah satu pionir Alfamart Class di Jawa Timur, lulusan program SMEGABIZ memiliki kompetensi standar industri ritel dengan jaminan peluang karir yang lebih tinggi dan berjiwa wirausaha kuat.
                            </p>
                        </div>
                        <div class="col-lg-3 text-end d-none d-lg-block text-white">
                            <i class="bi bi-stars" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <span data-i18n="sme.contact_title">Kontak SMKN 10 Surabaya</span></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="small text-secondary mb-1"><strong><span data-i18n="sme.addr">Alamat</span>:</strong> Jl. Keputih Tegal, Keputih, Sukolilo, Surabaya 60111</p>
                            <p class="small text-secondary mb-1"><strong><span data-i18n="sme.phone">Telepon</span>:</strong> (031) 5937654</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="small text-secondary mb-1"><strong>Email:</strong> info@smkn10sby.sch.id</p>
                            <p class="small text-muted mt-2 fst-italic" style="font-size: 0.75rem;"><span data-i18n="sme.source">Sumber</span>: smkn10sby.sch.id | Liputan Antara News & Radar Surabaya</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- i18next Library -->
<script src="https://unpkg.com/i18next@23.5.1/dist/umd/i18next.min.js"></script>

<script>
    /** @type {Object} Translation dictionary */
    const translations = {
        id: {
            nav: {
                website: "PROFILE WEBSITE",
                smegabiz: "PROFILE SMEGABIZ"
            },
            web: {
                title: "Tentang SMEGABIZ",
                lead: "Sistem Point of Sales (POS) dan Manajemen Gudang yang komprehensif untuk mendukung operasional bisnis retail masa kini.",
                desc1: "<strong>SMEGABIZ</strong> hadir sebagai solusi digital terintegrasi yang memudahkan pengelolaan stok barang, transaksi penjualan, hingga pemantauan laporan keuangan secara realtime. Aplikasi ini dirancang untuk memberikan kecepatan bagi kasir dan akurasi data bagi pemilik bisnis.",
                feat1_title: "Manajemen Inventaris:",
                feat1_desc: "Monitoring stok gudang dan item kasir secara akurat.",
                feat2_title: "Sistem Transaksi:",
                feat2_desc: "Proses checkout yang cepat dengan fitur keranjang belanja.",
                feat3_title: "Manajemen Supplier & Member:",
                feat3_desc: "Kelola data pemasok dan pelanggan setia dalam satu tempat.",
                feat4_title: "Laporan Pintar:",
                feat4_desc: "Ekspor laporan penjualan dan stok ke format PDF atau Excel.",
                tag1_title: "Cepat",
                tag1_desc: "Transaksi hitungan detik",
                tag2_title: "Aman",
                tag2_desc: "Data terenkripsi & privat",
                tag3_title: "Analitik",
                tag3_desc: "Laporan realtime & akurat"
            },
            sme: {
                lead: "Laboratorium ritel (mini-market) unggulan di lingkungan SMK Negeri 10 Surabaya.",
                desc1: "SMEGABIZ Business Centre merupakan fasilitas pendidikan vokasi yang dibangun melalui kerjasama strategis antara <strong>SMKN 10 Surabaya</strong> dengan <strong>Alfamart</strong> dalam program <em>Alfamart Class (link & match)</em>. Diresmikan sejak tahun 2015, fasilitas ini menjadi tempat praktik langsung bagi siswa jurusan Bisnis Ritel dan Bisnis Digital.",
                hist_title: "Sejarah & Latar Belakang",
                hist_desc: "Berawal pada tahun 2015 melalui donasi minimarket dari PT Sumber Alfaria Trijaya (Alfamart) sebagai bagian dari program CSR. Program ini menggabungkan teori manajemen ritel selama dua tahun di kelas dengan praktik nyata (PKL) selama 3-4 bulan di laboratorium sekolah bagi siswa kelas XII.",
                fac_title: "Fasilitas Modern",
                fac_desc: "Beroperasi layaknya minimarket komersial dengan peralatan kasir lengkap, rak display standar industri, serta sistem pembayaran modern termasuk QRIS. Dirancang khusus untuk mengasah kompetensi siswa di bidang e-commerce, pemasaran digital, dan pengelolaan usaha retail.",
                act_title: "Kegiatan Utama SMEGABIZ",
                act1_title: "Operasional Toko",
                act1_desc: "Melayani kebutuhan harian siswa & pegawai dengan sistem kasir profesional.",
                act2_title: "Praktik PKL",
                act2_desc: "Implementasi manajemen ritel & pengalaman kerja nyata dalam durasi 3-4 bulan.",
                act3_title: "Alfamart Mengajar",
                act3_desc: "Pelatihan rutin & pembekalan karir langsung dari mentor pakar industri.",
                cap_title: "Capaian & Kolaborasi Unggulan",
                cap_desc: "Sebagai salah satu pionir Alfamart Class di Jawa Timur, lulusan program SMEGABIZ memiliki kompetensi standar industri ritel dengan jaminan peluang karir yang lebih tinggi dan berjiwa wirausaha kuat.",
                contact_title: "Kontak SMKN 10 Surabaya",
                addr: "Alamat",
                phone: "Telepon",
                source: "Sumber"
            }
        },
        en: {
            nav: {
                website: "WEBSITE PROFILE",
                smegabiz: "SMEGABIZ PROFILE"
            },
            web: {
                title: "About SMEGABIZ",
                lead: "Comprehensive Point of Sales (POS) and Warehouse Management system to support modern retail business operations.",
                desc1: "<strong>SMEGABIZ</strong> comes as an integrated digital solution that facilitates inventory management, sales transactions, and real-time financial report monitoring. This application is designed to provide speed for cashiers and data accuracy for business owners.",
                feat1_title: "Inventory Management:",
                feat1_desc: "Accurate monitoring of warehouse stock and cashier items.",
                feat2_title: "Transaction System:",
                feat2_desc: "Fast checkout process with shopping cart features.",
                feat3_title: "Supplier & Member Management:",
                feat3_desc: "Manage supplier data and loyal customers in one place.",
                feat4_title: "Smart Reports:",
                feat4_desc: "Export sales and stock reports to PDF or Excel format.",
                tag1_title: "Fast",
                tag1_desc: "Transactions in seconds",
                tag2_title: "Secure",
                tag2_desc: "Encrypted & private data",
                tag3_title: "Analytics",
                tag3_desc: "Real-time & accurate reports"
            },
            sme: {
                lead: "Leading retail laboratory (mini-market) within SMKN 10 Surabaya environment.",
                desc1: "SMEGABIZ Business Centre is a vocational education facility built through a strategic partnership between <strong>SMKN 10 Surabaya</strong> and <strong>Alfamart</strong> in the <em>Alfamart Class (link & match)</em> program. Inaugurated in 2015, this facility serves as a hands-on practice site for Retail Business and Digital Business students.",
                hist_title: "History & Background",
                hist_desc: "Starting in 2015 through a mini-market donation from PT Sumber Alfaria Trijaya (Alfamart) as part of a CSR program. This program combines retail management theory for two years in class with real-world practice (PKL) for 3-4 months at the school laboratory for 12th-grade students.",
                fac_title: "Modern Facilities",
                fac_desc: "Operates like a commercial mini-market with complete cashier equipment, industry-standard display racks, and modern payment systems including QRIS. Specifically designed to hone students' competencies in e-commerce, digital marketing, and retail business management.",
                act_title: "SMEGABIZ Main Activities",
                act1_title: "Shop Operations",
                act1_desc: "Serving daily needs of students & staff with a professional cashier system.",
                act2_title: "PKL Practice",
                act2_desc: "Implementation of retail management & real work experience within a 3-4 month duration.",
                act3_title: "Alfamart Teaching",
                act3_desc: "Routine training & career preparation directly from industry expert mentors.",
                cap_title: "Top Achievements & Collaboration",
                cap_desc: "As one of the pioneers of Alfamart Class in East Java, SMEGABIZ program graduates have industry-standard retail competencies with guaranteed higher career opportunities and a strong entrepreneurial spirit.",
                contact_title: "Contact SMKN 10 Surabaya",
                addr: "Address",
                phone: "Phone",
                source: "Source"
            }
        }
    };

    /**
     * Change application language
     * @param {'id' | 'en'} lang
     */
    function changeLanguage(lang) {
        i18next.changeLanguage(lang, (err, t) => {
            if (err) return console.log('something went wrong loading', err);
            updateContent();

            // Update button active state
            document.getElementById('btn-id').classList.toggle('active', lang === 'id');
            document.getElementById('btn-en').classList.toggle('active', lang === 'en');

            // Save preference
            localStorage.setItem('profile_lang', lang);
        });
    }

    /**
     * Update all elements with data-i18n attribute
     */
    function updateContent() {
        document.querySelectorAll('[data-i18n]').forEach(elem => {
            const key = elem.getAttribute('data-i18n');
            const translation = i18next.t(key);

            // Handle HTML if the translation contains tags
            if (translation.includes('<') && translation.includes('>')) {
                elem.innerHTML = translation;
            } else {
                elem.textContent = translation;
            }
        });
    }

    // Initialize i18next
    document.addEventListener('DOMContentLoaded', () => {
        const savedLang = localStorage.getItem('profile_lang') || 'id';

        i18next.init({
            lng: savedLang,
            debug: false,
            resources: {
                id: {
                    translation: translations.id
                },
                en: {
                    translation: translations.en
                }
            }
        }, function(err, t) {
            updateContent();
            document.getElementById('btn-id').classList.toggle('active', savedLang === 'id');
            document.getElementById('btn-en').classList.toggle('active', savedLang === 'en');
        });
    });
</script>

<style>
    .nav-pills .nav-link {
        color: #6c757d;
        transition: all 0.2s ease;
        border-radius: 8px;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
        color: #0d6efd;
    }

    .letter-spacing-2 {
        letter-spacing: 2px;
    }

    .tab-pane {
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-group .btn.active {
        background-color: #0d6efd;
        color: white;
    }
</style>
@endsection