<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Booking Makanan — SmeGo')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ee5253;
            --light-bg: #f8f9fc;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: var(--light-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .booking-nav {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        .booking-nav .navbar-brand {
            font-weight: 800;
            color: white !important;
            font-size: 1.2rem;
        }

        .booking-nav .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .booking-nav .nav-link:hover,
        .booking-nav .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
        }

        .booking-nav .badge-cart {
            position: relative;
            top: -2px;
            font-size: 0.7rem;
        }

        .booking-content {
            flex: 1;
            padding: 1.5rem 0;
        }

        /* Footer simple */
        .booking-footer {
            background: white;
            border-top: 1px solid #eee;
            padding: 1rem 0;
            text-align: center;
            color: #6c757d;
            font-size: 0.8rem;
        }

        /* Global toast override */
        #toast-container>.toast-success {
            background-color: #48bb78;
        }

        #toast-container>.toast-error {
            background-color: #f56565;
        }

        @yield('styles')
    </style>
</head>

<body>
    <!-- Booking Navbar -->
    <nav class="booking-nav navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('booking.menu') }}">
                <i class="bi bi-bag-heart-fill me-1"></i> SmeGo
            </a>
            
            <!-- Mobile Cart Button (Visible on Mobile) -->
            <a href="{{ route('booking.cart') }}" class="btn btn-outline-light btn-sm position-relative ms-auto me-2 d-lg-none border-0">
                <i class="bi bi-cart3 fs-5"></i>
                @php $cartCount = count(session('booking_cart', [])); @endphp
                @if($cartCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-light" style="font-size: 0.6rem;">
                    {{ $cartCount }}
                </span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#bookingNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="bookingNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.menu') ? 'active' : '' }}"
                            href="{{ route('booking.menu') }}">
                            <i class="bi bi-grid-fill"></i> Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.cart') ? 'active' : '' }}"
                            href="{{ route('booking.cart') }}">
                            <i class="bi bi-cart3"></i> Keranjang
                            <span class="badge bg-white text-danger badge-cart" id="cart-count">
                                {{ count(session('booking_cart', [])) }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.history') ? 'active' : '' }}"
                            href="{{ route('booking.history') }}">
                            <i class="bi bi-clock-history"></i> Pesanan Saya
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-white small d-none d-lg-block">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                    </span>
                    <form action="{{ route('pelanggan.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="booking-content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="booking-footer">
        <div class="container">
            &copy; {{ date('Y') }} SmeGo — Jam Operasional: {{ App\Models\ShopSetting::get('open_hour', '07:00') }} - {{ App\Models\ShopSetting::get('close_hour', '15:00') }} WIB
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000",
        };

        @if(session('success'))
        toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
        toastr.error("{{ session('error') }}");
        @endif
        @if(session('warning'))
        toastr.warning("{{ session('warning') }}");
        @endif
    </script>
    @stack('scripts')
</body>

</html>