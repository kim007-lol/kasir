<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pelanggan — SMEGABIZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ee5253;
            --border-radius: 0.75rem;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8f9fc 0%, #fff0f0 100%);
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
            max-width: 500px;
            width: 100%;
            padding: 2.5rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-logo .icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #48bb78, #38a169);
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
            border-color: #48bb78;
            box-shadow: 0 0 0 0.2rem rgba(72, 187, 120, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #333;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #48bb78, #38a169);
            border: none;
            border-radius: var(--border-radius);
            padding: 0.7rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(72, 187, 120, 0.3);
            color: white;
        }

        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link:hover {
            text-decoration: underline;
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

        .section-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
            color: #adb5bd;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
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
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <a href="{{ route('pelanggan.login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Login
        </a>

        <div class="auth-logo">
            <div class="icon"><i class="bi bi-person-plus-fill"></i></div>
            <h3>Daftar Akun</h3>
            <p>Buat akun untuk memesan makanan</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3">
            @foreach($errors->all() as $error)
            <div><i class="bi bi-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('pelanggan.register.submit') }}" id="registerForm">
            @csrf

            <div class="section-divider">DATA DIRI</div>

            <div class="mb-3">
                <label for="name" class="form-label">
                    <i class="bi bi-person"></i> Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                @error('name')
                <div class="invalid-feedback"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">
                    <i class="bi bi-telephone"></i> Nomor Telepon <span class="text-danger">*</span>
                </label>
                <input type="text" name="phone" id="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}" placeholder="Contoh: 08123456789" required>
                @error('phone')
                <div class="invalid-feedback"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">
                    <i class="bi bi-geo-alt"></i> Alamat / Kelas <span class="text-danger">*</span>
                </label>
                <input type="text" name="address" id="address"
                    class="form-control @error('address') is-invalid @enderror"
                    value="{{ old('address') }}" placeholder="Masukkan alamat atau kelas Anda" required>
                @error('address')
                <div class="invalid-feedback"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="section-divider">AKUN LOGIN</div>

            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-at"></i> Username <span class="text-danger">*</span>
                </label>
                <input type="text" name="username" id="username"
                    class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username') }}" placeholder="Buat username unik" required>
                <div class="form-text">Hanya huruf, angka, dash (-), dan underscore (_)</div>
                @error('username')
                <div class="invalid-feedback"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Minimal 8 karakter" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    <i class="bi bi-lock-fill"></i> Konfirmasi Password <span class="text-danger">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="form-control" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn btn-success-custom w-100 mb-3" id="submitBtn">
                <i class="bi bi-person-plus"></i> Daftar Sekarang
            </button>
        </form>

        <hr class="my-3">

        <p class="text-center text-muted mb-0" style="font-size: 0.9rem;">
            Sudah punya akun?
            <a href="{{ route('pelanggan.login') }}" class="auth-link">Login di sini</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const registerForm = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });

            registerForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mendaftarkan...';
            });
        });
    </script>
</body>

</html>