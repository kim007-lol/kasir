@extends('layouts.app')

@section('title', 'Tambah Member')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-person-plus"></i> Tambah Member Baru
        </h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('members.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nama Member <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="Masukkan nama member" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold">No Telepon</label>
                    <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label fw-bold">Alamat</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                        rows="4" placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn fw-bold text-white" style="background-color: #5b9dd9;">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary fw-bold">
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
    .form-label {
        border-radius: 0.375rem;
    }

    .form-control:focus {
        border-color: #ff0000;
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
    }

    @media (max-width: 576px) {
        h2 {
            font-size: 1.25rem;
        }

        .btn {
            padding: 0.35rem 0.75rem;
            font-size: 0.9rem;
        }
    }
</style>
@endsection