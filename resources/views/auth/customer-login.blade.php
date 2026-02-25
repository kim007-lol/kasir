<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login Pelanggan — SMEGABIZ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff0000;
            --primary-dark: #cc0000;
            --border-radius: 0.75rem;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8f9fc 0%, #ffe0e0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            max-width: 460px;
            width: 100%;
            padding: 2.5rem;
            transition: transform 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-3px);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-logo .icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            color: white;
            font-size: 1.6rem;
        }

        .auth-logo h3 {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .auth-logo p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: var(--border-radius);
            border: 1px solid #e0e0e0;
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #333;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: var(--border-radius);
            padding: 0.7rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), #bb0000);
        }

        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .input-group .btn {
            border-radius: var(--border-radius);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 1.75rem;
            }

            .auth-logo .icon {
                width: 52px;
                height: 52px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <a href="/" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>

        <div class="auth-logo">
            <div class="icon"><i class="bi bi-bag-heart-fill"></i></div>
            <h3>Login Pelanggan</h3>
            <p>Masuk untuk memesan makanan</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3">
            @foreach($errors->all() as $error)
            <div><i class="bi bi-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success py-2 px-3 mb-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('pelanggan.login.submit') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person"></i> Username
                </label>
                <input type="text" name="username" id="username"
                    class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> Password
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Masukkan password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>

        <hr class="my-3">

        <p class="text-center text-muted mb-0" style="font-size: 0.9rem;">
            Belum punya akun?
            <a href="{{ route('pelanggan.register') }}" class="auth-link">Daftar Sekarang</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });

            loginForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            });
        });
    </script>
</body>

</html>