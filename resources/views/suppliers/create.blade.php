@php
$routePrefix = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.' : '';
$layout = $routePrefix ? 'layouts.cashier' : 'layouts.app';
@endphp
@extends($layout)

@section('title', 'Tambah Supplier')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-plus-circle"></i> Tambah Supplier
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route($routePrefix . 'suppliers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Supplier *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">No Telp</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="contract_date" class="form-label">Tanggal Kontrak</label>
                    <input type="date" class="form-control @error('contract_date') is-invalid @enderror" id="contract_date" name="contract_date" value="{{ old('contract_date') }}">
                    @error('contract_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route($routePrefix . 'suppliers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection