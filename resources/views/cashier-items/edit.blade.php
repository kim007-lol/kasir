@extends('layouts.app')

@section('title', 'Edit Stok Kasir')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-pencil"></i> Edit Stok Kasir
    </h2>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <h6 class="card-title">Detail Barang:</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td width="150"><strong>Kode:</strong></td>
                    <td>{{ $cashierItem->code }}</td>
                </tr>
                <tr>
                    <td><strong>Nama:</strong></td>
                    <td>{{ $cashierItem->name }}</td>
                </tr>
                <tr>
                    <td><strong>Kategori:</strong></td>
                    <td>{{ $cashierItem->category->name }}</td>
                </tr>
                <tr>
                    <td><strong>Supplier:</strong></td>
                    <td>{{ $cashierItem->supplier->name }}</td>
                </tr>
                <tr>
                    <td><strong>Harga Jual:</strong></td>
                    <td>Rp. {{ number_format($cashierItem->selling_price, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('cashier-items.update', $cashierItem) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="stock" class="form-label">Stok Kasir *</label>
                    <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $cashierItem->stock) }}" required>
                    @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Edit manual stok kasir (tidak mempengaruhi stok gudang)</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('cashier-items.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection