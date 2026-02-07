@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
    <div class="row w-100 justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow border-0 w-100">
            <div class="card-body p-3 p-sm-4 p-md-5">
                <div class="text-center mb-3 mb-md-4">
                    <h2 class="fw-bold fs-3 fs-md-2">Daftar Akun</h2>
                    <p class="text-muted fs-6 mb-0">Buat akun baru untuk mulai menggunakan aplikasi</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal Register!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-500">
                            <i class="bi bi-person-circle"></i> Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control form-control-lg @error('name') is-invalid @enderror" 
                            value="{{ old('name') }}" 
                            placeholder="Masukkan nama lengkap"
                            required 
                            autofocus>
                        @error('name')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-500">
                            <i class="bi bi-at"></i> Username
                        </label>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="form-control form-control-lg @error('username') is-invalid @enderror" 
                            value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            required>
                        <small class="form-text text-muted">Username harus unik dan tanpa spasi</small>
                        @error('username')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-500">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control form-control-lg @error('email') is-invalid @enderror" 
                            value="{{ old('email') }}"
                            placeholder="Masukkan email"
                            required>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-500">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                placeholder="Masukkan password (minimal 6 karakter)"
                                required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="passwordStrength" class="mt-2">
                            <small class="text-muted">Kekuatan Password:</small>
                            <div class="progress mt-1" style="height: 5px;">
                                <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-500">
                            <i class="bi bi-lock-check"></i> Konfirmasi Password
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                                placeholder="Ulangi password"
                                required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="matchFeedback" class="mt-2"></div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" id="submitBtn">
                        <i class="bi bi-person-plus"></i> Daftar Sekarang
                    </button>
                </form>

                <hr class="my-4">
                <p class="text-center text-muted mb-0">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const strengthBar = document.getElementById('strengthBar');
    const matchFeedback = document.getElementById('matchFeedback');
    const registerForm = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Password strength indicator
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;

        if (password.length >= 6) strength = 25;
        if (password.length >= 8) strength = 50;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength = 75;
        if (/[0-9]/.test(password) && /[!@#$%^&*]/.test(password)) strength = 100;

        strengthBar.style.width = strength + '%';
        
        if (strength < 50) {
            strengthBar.className = 'progress-bar bg-danger';
        } else if (strength < 75) {
            strengthBar.className = 'progress-bar bg-warning';
        } else {
            strengthBar.className = 'progress-bar bg-success';
        }

        checkPasswordMatch();
    });

    // Check password match
    passwordConfirmInput.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        if (passwordConfirmInput.value === '') {
            matchFeedback.innerHTML = '';
            return;
        }

        if (passwordInput.value === passwordConfirmInput.value) {
            matchFeedback.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Password cocok</small>';
        } else {
            matchFeedback.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle"></i> Password tidak cocok</small>';
        }
    }

    // Form submission
    registerForm.addEventListener('submit', function(e) {
        if (!registerForm.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else if (passwordInput.value !== passwordConfirmInput.value) {
            e.preventDefault();
            alert('Password tidak cocok!');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
    });
});
</script>

<style>
    :root {
        --primary-color: #0d6efd;
        --border-radius: 0.5rem;
    }

    body {
        background-color: #f0f5fa;
        min-height: 100vh;
    }

    .container-fluid {
        padding: 1rem;
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15) !important;
    }

    .card-body {
        padding: 2rem 1.5rem !important;
    }

    /* Responsive padding */
    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem 1rem !important;
        }

        .btn-lg {
            padding: 0.6rem 0.8rem !important;
            font-size: 0.95rem !important;
        }

        h2 {
            font-size: 1.5rem !important;
        }

        .form-control, .form-control-lg {
            font-size: 1rem !important;
            padding: 0.6rem 0.75rem !important;
        }

        .input-group .btn {
            padding: 0.6rem 0.75rem !important;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }
    }

    @media (min-width: 577px) and (max-width: 768px) {
        .card-body {
            padding: 2rem !important;
        }

        h2 {
            font-size: 1.75rem !important;
        }
    }

    .form-control, .form-control-lg {
        border-radius: var(--border-radius);
        font-size: 1rem;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-control-lg:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    @media (max-width: 576px) {
        .form-label {
            font-size: 0.85rem;
        }
    }

    .btn-primary {
        border-radius: var(--border-radius);
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }

    .input-group .btn {
        border-radius: var(--border-radius);
    }

    .alert {
        border-radius: 0.75rem;
        border: none;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-sm {
        padding: 0.75rem 1rem;
        margin-bottom: 0;
    }

    .alert ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .alert li {
        margin-bottom: 0.25rem;
    }

    @media (max-width: 576px) {
        .alert {
            font-size: 0.85rem;
        }

        .alert-sm {
            font-size: 0.8rem;
        }
    }

    .text-center {
        text-align: center;
    }

    .fw-500 {
        font-weight: 500;
    }

    .fw-bold {
        font-weight: 700;
    }

    .form-text {
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 576px) {
        .form-text,
        .invalid-feedback {
            font-size: 0.8rem;
        }
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 0.25rem;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .fs-3 {
        font-size: 1.75rem !important;
    }

    .fs-6 {
        font-size: 0.95rem !important;
    }

    @media (max-width: 576px) {
        .fs-3 {
            font-size: 1.5rem !important;
        }

        .fs-6 {
            font-size: 0.85rem !important;
        }
    }

    /* Responsive text sizes */
    .fs-md-2 {
        font-size: 2rem !important;
    }

    @media (max-width: 768px) {
        .fs-md-2 {
            font-size: 1.5rem !important;
        }
    }

    /* Min height for full viewport */
    html, body {
        height: 100%;
    }

    .min-vh-100 {
        min-height: 100vh;
    }

    /* Smooth transitions */
    * {
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    input:focus,
    button:focus {
        outline: none;
    }
</style>
@endsection
