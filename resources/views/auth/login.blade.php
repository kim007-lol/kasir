@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
    <div class="row w-100 justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow border-0 w-100">
            <div class="card-body p-3 p-sm-4 p-md-5">
                <div class="text-center mb-3 mb-md-4">
                    <h2 class="fw-bold fs-3 fs-md-2">Selamat Datang</h2>
                    <p class="text-muted fs-6 mb-0">Masuk ke akun Anda untuk melanjutkan</p>
                </div>
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <strong>Login Gagal!</strong>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf
                    
                    <div class="mb-3">
                        <label for="login" class="form-label fw-500">
                            <i class="bi bi-person"></i> Username atau Email
                        </label>
                        <input 
                            type="text" 
                            name="login" 
                            id="login" 
                            class="form-control form-control-lg @error('login') is-invalid @enderror" 
                            value="{{ old('login') }}" 
                            placeholder="Masukkan username atau email"
                            required 
                            autofocus>
                        @error('login')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-500">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                placeholder="Masukkan password"
                                required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold mb-3" id="submitBtn">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <hr class="my-4">
                
                <div class="text-center mb-3">
                    <p class="text-muted mb-2">Belum punya akun?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg w-100 fw-bold">
                        <i class="bi bi-person-plus"></i> Daftar Akun Baru
                    </a>
                </div>

               
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Form submission
    loginForm.addEventListener('submit', function() {
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

    .btn-outline-primary {
        border-radius: var(--border-radius);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
    }

    .input-group .btn {
        border-radius: var(--border-radius);
    }

    .alert {
        border-radius: 0.75rem;
        border: none;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
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

    .alert-sm {
        padding: 0.75rem 1rem;
        margin-bottom: 0;
    }

    @media (max-width: 576px) {
        .alert-sm {
            font-size: 0.85rem;
        }
    }

    .form-text {
        font-size: 0.85rem;
        margin-top: 0.25rem;
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

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    /* Min height for full viewport */
    html, body {
        height: 100%;
    }

    .min-vh-100 {
        min-height: 100vh;
    }
</style>
@endsection
