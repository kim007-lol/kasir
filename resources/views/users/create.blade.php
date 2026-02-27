@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-person-plus"></i> Tambah User Baru
        </h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                        id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required>
                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Minimal 8 karakter" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="pelanggan" {{ old('role') === 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted mt-1 d-block">
                        <i class="bi bi-info-circle"></i>
                        <strong>Admin</strong> = akses penuh. <strong>Kasir</strong> = transaksi POS. <strong>Pelanggan</strong> = akses booking online.
                    </small>
                </div>

                {{-- Phone field: only required when role = pelanggan --}}
                <div class="mb-4" id="phone-field" style="{{ old('role') === 'pelanggan' ? '' : 'display:none;' }}">
                    <label for="phone" class="form-label fw-bold">
                        Nomor Telepon
                        <span class="text-danger">*</span>
                        <span class="badge bg-info text-dark ms-1" style="font-size: 0.7rem;">Pelanggan</span>
                    </label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                        id="phone" name="phone" value="{{ old('phone') }}"
                        placeholder="Contoh: 081234567890">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="alert alert-info py-2 px-3 mt-2 mb-0" style="font-size: 0.82rem;">
                        <i class="bi bi-lightbulb-fill me-1"></i>
                        Jika sudah ada <strong>member</strong> dengan nama & nomor telepon yang sama, akun ini akan <strong>ditautkan otomatis</strong> ke member tersebut (tidak membuat member baru).
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-check-circle"></i> Simpan User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>

                <script>
                    document.getElementById('role').addEventListener('change', function () {
                        const phoneField = document.getElementById('phone-field');
                        const phoneInput = document.getElementById('phone');
                        if (this.value === 'pelanggan') {
                            phoneField.style.display = '';
                        } else {
                            phoneField.style.display = 'none';
                            phoneInput.value = '';
                        }
                    });
                </script>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.75rem;
    }

    .form-control,
    .form-select {
        border-radius: 0.5rem;
    }
</style>
@endsection