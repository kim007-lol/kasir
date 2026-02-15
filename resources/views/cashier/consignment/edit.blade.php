@extends('layouts.cashier')

@section('title', 'Edit Barang Titipan')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-pencil"></i> Edit Barang Titipan
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('cashier.consignment.update', $cashierItem) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Barang *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $cashierItem->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sumber Titipan *</label>
                    <input type="text" class="form-control @error('consignment_source') is-invalid @enderror" name="consignment_source" value="{{ old('consignment_source', $cashierItem->consignment_source) }}" required>
                    @error('consignment_source')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Harga Awal (Rp) *</label>
                        <input type="number" class="form-control @error('cost_price') is-invalid @enderror" name="cost_price" value="{{ old('cost_price', $cashierItem->cost_price) }}" required min="0">
                        @error('cost_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Harga Jual (Rp) *</label>
                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror" name="selling_price" value="{{ old('selling_price', $cashierItem->selling_price) }}" required min="0">
                        @error('selling_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Stok *</label>
                    <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $cashierItem->stock) }}" required min="0">
                    @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('cashier.consignment.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection