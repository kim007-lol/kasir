<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasirku')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #ff6b6b;
            --sidebar-bg-solid: #ff6b6b;
            --navbar-bg: #ee5253;
            --content-bg: #f0f5fa;
            --primary-color: #ff6b6b;
            --primary-dark: #ee5253;
            --secondary-color: #ff8a80;
            --accent-color: #ffcccc;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --info-color: #5b9dd9;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--content-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: 4px 0 20px rgba(91, 157, 217, 0.15);
        }

        .sidebar-header {
            color: #ffffff;
            padding: 0 20px;
            margin-bottom: 20px;
        }

        .sidebar-header h5 {
            font-weight: 700;
            margin-bottom: 0;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar nav a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-left: 3px solid transparent;
            transition: var(--transition);
            font-size: 1rem;
        }

        .sidebar nav a i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .sidebar nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-left-color: #ffffff;
            transform: translateX(5px);
        }

        .sidebar nav a.active {
            background-color: rgba(255, 255, 255, 0.25);
            border-left-color: #ffffff;
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar .logout-btn {
            margin-top: 30px;
            padding: 0 20px;
        }

        .logout-btn form {
            width: 100%;
        }

        .logout-btn .btn {
            border-radius: 0.5rem;
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
        }

        .logout-btn .btn:hover {
            background-color: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background-color: var(--navbar-bg);
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            color: white !important;
        }

        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: white;
        }

        .navbar .toggle-sidebar-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0 10px;
            margin-right: 15px;
            transition: var(--transition);
        }

        .navbar .toggle-sidebar-btn:hover {
            color: rgba(255, 255, 255, 0.8);
            transform: scale(1.1);
        }

        /* ===== CONTENT ===== */
        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .content>*:first-child {
            margin-top: 0;
        }

        /* ===== SCROLLBAR ===== */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* ===== RESPONSIVE - TABLET ===== */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 220px;
            }

            .sidebar nav a {
                padding: 12px 15px;
                font-size: 0.95rem;
            }

            .content {
                padding: 20px;
            }
        }

        /* ===== RESPONSIVE - MOBILE ===== */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 60px;
                left: -100%;
                max-height: calc(100vh - 60px);
                border-radius: 0;
                padding: 20px 0;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-header {
                display: none;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .navbar .toggle-sidebar-btn {
                display: block;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .content {
                padding: 15px;
                margin-top: 0;
            }

            .sidebar nav a {
                padding: 12px 20px;
                font-size: 0.9rem;
            }

            .sidebar .logout-btn {
                margin-top: 20px;
                padding: 10px 20px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 0.9rem;
            }

            .content {
                padding: 12px;
            }

            .sidebar nav a {
                padding: 12px 15px;
                font-size: 0.85rem;
            }

            .sidebar nav a i {
                margin-right: 8px;
            }
        }

        /* ===== OVERLAY FOR MOBILE SIDEBAR ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar-overlay {
                display: block;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>

<body>
    @auth
    <!-- Sidebar Overlay untuk Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.navbar')
        <div class="content">
            @include('partials.alert')
            @yield('content')
        </div>
    </div>
    @else
    <div class="main-content" style="margin-left: 0; width: 100%;">
        @include('partials.navbar')
        <div class="content">
            @yield('content')
        </div>
    </div>
    @endauth

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.toggle-sidebar-btn');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            // Close sidebar when clicking a link
            const sidebarLinks = document.querySelectorAll('.sidebar nav a, .sidebar .logout-btn button');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('active');
                        overlay.classList.remove('active');
                    }
                });
            });

            // Close sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    </script>
</body>

</html>