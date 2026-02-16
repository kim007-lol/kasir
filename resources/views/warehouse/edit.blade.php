@extends('layouts.app')

@section('title', 'Edit Barang Gudang')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-pencil"></i> Edit Barang Gudang
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('warehouse.update', $warehouse) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Barang (Barcode) *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $warehouse->code) }}" required>
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori *</label>
                            <select name="category_id" id="category_id" class="form-select select2-basic @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if(old('category_id', $warehouse->category_id) == $category->id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier *</label>
                            <select name="supplier_id" id="supplier_id" class="form-select select2-basic @error('supplier_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @if(old('supplier_id', $warehouse->supplier_id) == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="purchase_price" class="form-label">Harga Beli *</label>
                            <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $warehouse->purchase_price) }}" required>
                            @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="selling_price" class="form-label">Harga Jual *</label>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $warehouse->selling_price) }}" required>
                            @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Discount disabled in Warehouse --}}
                        {{-- <div class="mb-3">
                            <label for="discount" class="form-label">Potongan (Rp)</label>
                            <input type="number" step="1" min="0" class="form-control" id="discount" name="discount" value="0" readonly>
                        </div> --}}

                        <div class="mb-3">
                            <label for="final_price_display" class="form-label">Harga Akhir (Estimasi)</label>
                            <input type="text" class="form-control bg-light" id="final_price_display" readonly value="Rp. {{ number_format($warehouse->final_price, 0, ',', '.') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok Gudang *</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $warehouse->stock) }}" required>
                            @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jika menambah stok, akan otomatis tercatat di riwayat stok masuk</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exp_date" class="form-label">Tanggal Kadaluarsa</label>
                            <input type="date" class="form-control @error('exp_date') is-invalid @enderror" id="exp_date" name="exp_date" value="{{ old('exp_date', $warehouse->exp_date ? $warehouse->exp_date->format('Y-m-d') : '') }}">
                            @error('exp_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-basic').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });

    // Auto-calculate final price
    function calculateFinalPrice() {
        const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
        const discount = 0; // Discount disabled for warehouse

        const finalPrice = sellingPrice - discount;

        document.getElementById('final_price_display').value = 'Rp. ' + finalPrice.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    document.getElementById('selling_price').addEventListener('input', calculateFinalPrice);
</script>
@endpush
@endsection