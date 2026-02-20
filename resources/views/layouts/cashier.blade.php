<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KASIR SMEGABIZ')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- Vite Assets (Required for Reverb/Echo) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --navbar-bg: #ee5253;
            --content-bg: #f0f5fa;
            --primary-color: #ff6b6b;
            --primary-dark: #ee5253;
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--content-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Customization */
        .navbar {
            background-color: var(--navbar-bg);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1050;
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
        }

        /* Dropdown Menu */
        .navbar .dropdown-menu {
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
        }

        .navbar .dropdown-item {
            color: #333 !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition);
        }

        .navbar .dropdown-item:hover,
        .navbar .dropdown-item.active {
            background-color: var(--primary-color);
            color: #fff !important;
        }

        .navbar .dropdown-divider {
            margin: 0.25rem 0;
        }

        .content {
            flex: 1;
            padding: 30px 0;
        }

        /* Toastr Customization */
        #toast-container>.toast-success {
            background-color: #48bb78;
        }

        #toast-container>.toast-error {
            background-color: #f56565;
        }

        #toast-container>.toast-warning {
            background-color: #ed8936;
        }

        #toast-container>.toast-info {
            background-color: #5b9dd9;
        }

        /* ===== GLOBAL RESPONSIVE UTILITIES ===== */
        .pagination svg {
            width: 1rem;
            height: 1rem;
        }
        .pagination .flex.justify-between.flex-1 {
            display: none;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .content {
                padding: 20px 0;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 15px 0;
            }
            .content .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            h2.fw-bold {
                font-size: 1.3rem;
            }
            .table th, .table td {
                font-size: 0.82rem;
                padding: 0.4rem 0.35rem;
            }
            .card-body {
                padding: 0.85rem;
            }
            .card-header {
                padding: 0.6rem 0.85rem;
            }
            /* Filter buttons horizontal scroll */
            .btn-group.w-100 {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .btn-group.w-100 .btn {
                white-space: nowrap;
                font-size: 0.78rem;
                padding: 0.35rem 0.6rem;
            }
            /* Navbar brand smaller */
            .navbar-brand {
                font-size: 0.95rem;
            }
            .nav-link {
                font-size: 0.85rem !important;
                padding: 0.4rem 0.6rem !important;
            }
        }

        @media (max-width: 576px) {
            .content {
                padding: 10px 0;
            }
            .content .container {
                padding-left: 8px;
                padding-right: 8px;
            }
            h2.fw-bold {
                font-size: 1.15rem;
            }
            .table th, .table td {
                font-size: 0.75rem;
                padding: 0.3rem 0.25rem;
            }
            .btn-sm {
                font-size: 0.7rem;
                padding: 0.2rem 0.35rem;
            }
            .card {
                border-radius: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('cashier.dashboard') }}">
                <i class="bi bi-cart4 me-2"></i> KASIR SMEGABIZ
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}" href="{{ route('cashier.dashboard') }}">DASHBOARD</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cashier.transactions.index') ? 'active' : '' }}" href="{{ route('cashier.transactions.index') }}">TRANSAKSI</a>
                    </li>
                    {{-- Dropdown: Stok --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('cashier.stock.*', 'cashier.stock-adjustments.*', 'cashier.consignment.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            STOK
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cashier.stock.index') ? 'active' : '' }}" href="{{ route('cashier.stock.index') }}">
                                    <i class="bi bi-box-seam me-2"></i>Stok Item Kasir
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cashier.stock-adjustments.*') ? 'active' : '' }}" href="{{ route('cashier.stock-adjustments.index') }}">
                                    <i class="bi bi-clipboard-check me-2"></i>Stock Opname
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cashier.consignment.*') ? 'active' : '' }}" href="{{ route('cashier.consignment.index') }}">
                                    <i class="bi bi-shop me-2"></i>Barang Titipan
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- Dropdown: Histori --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('cashier.history.*', 'cashier.bookings.history') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            HISTORI
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cashier.history.index') ? 'active' : '' }}" href="{{ route('cashier.history.index') }}">
                                    <i class="bi bi-clock-history me-2"></i>Histori Transaksi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cashier.bookings.history') ? 'active' : '' }}" href="{{ route('cashier.bookings.history') }}">
                                    <i class="bi bi-journal-text me-2"></i>Histori Booking
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cashier.bookings.index', 'cashier.bookings.show') ? 'active' : '' }}" href="{{ route('cashier.bookings.index') }}">
                            PESANAN
                            <span class="badge bg-danger ms-1" id="nav-booking-badge" style="{{ \App\Models\Booking::pending()->count() > 0 ? '' : 'display:none' }}">
                                {{ \App\Models\Booking::pending()->count() ?: '' }}
                            </span>
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <span class="me-3 d-none d-lg-block">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Global Scripts -->
    <script>
        // Global SweetAlert Delete Confirmation
        window.confirmDelete = function(event, message = "Data yang dihapus tidak dapat dikembalikan!") {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff6b6b',
                cancelButtonColor: '#adb5bd',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        };

        // Toastr Config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
        };
    </script>

    <!-- Toastr Data Bridge -->
    <div id="toastr-data"
        data-success="{{ session('success') }}"
        data-error="{{ session('error') }}"
        data-info="{{ session('info') }}"
        data-warning="{{ session('warning') }}"
        data-errors="{{ $errors->any() ? json_encode($errors->all()) : '' }}"
        style="display: none;"></div>

    <script>
        (function() {
            const dataEl = document.getElementById('toastr-data');
            if (!dataEl) return;
            const success = dataEl.dataset.success;
            const error = dataEl.dataset.error;
            const info = dataEl.dataset.info;
            const warning = dataEl.dataset.warning;
            const validationErrors = dataEl.dataset.errors;

            if (success) toastr.success(success);
            if (error) toastr.error(error);
            if (info) toastr.info(info);
            if (warning) toastr.warning(warning);
            if (validationErrors) {
                try {
                    const errs = JSON.parse(validationErrors);
                    errs.forEach(err => toastr.error(err));
                } catch (e) {}
            }
        })();
    </script>
    @stack('scripts')
</body>

</html>