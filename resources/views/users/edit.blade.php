@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-person-gear"></i> Edit User
        </h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                        id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Masukkan username" required>
                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Kosongkan jika tidak diubah">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="" disabled>-- Pilih Role --</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted mt-1 d-block">
                        <i class="bi bi-info-circle"></i>
                        <strong>Admin</strong> = akses penuh (gudang, laporan, user). <strong>Kasir</strong> = transaksi & stok kasir.
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
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