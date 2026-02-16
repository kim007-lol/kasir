@extends('layouts.app')

@section('title', 'Tambah Stok Kasir dari Gudang')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-arrow-right-circle"></i> Transfer Stok Gudang → Kasir
    </h2>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        <strong>Catatan:</strong> Stok yang Anda transfer akan otomatis dikurangi dari gudang dan ditambahkan ke kasir menggunakan database transaction.
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('cashier-items.store') }}" method="POST" id="transferForm">
                @csrf
                <div class="mb-3">
                    <label for="warehouse_item_id" class="form-label">Pilih Barang dari Gudang *</label>
                    <select name="warehouse_item_id" id="warehouse_item_id" class="form-select select2-basic @error('warehouse_item_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($warehouseItems as $item)
                        <option value="{{ $item->id }}"
                            data-code="{{ $item->code }}"
                            data-name="{{ $item->name }}"
                            data-category="{{ $item->category->name ?? '-' }}"
                            data-supplier="{{ $item->supplier->name ?? '-' }}"
                            data-price="{{ $item->final_price }}"
                            data-stock="{{ $item->stock }}"
                            @if(old('warehouse_item_id')==$item->id) selected @endif>
                            [{{ $item->code }}] {{ $item->name }} - Stok: {{ $item->stock }} | Harga: Rp. {{ number_format($item->final_price, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('warehouse_item_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Info barang terpilih -->
                <div id="itemInfo" class="card bg-light mb-3" style="display: none;">
                    <div class="card-body">
                        <h6 class="card-title">Detail Barang Terpilih:</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="150"><strong>Kode:</strong></td>
                                <td id="info-code">-</td>
                            </tr>
                            <tr>
                                <td><strong>Nama:</strong></td>
                                <td id="info-name">-</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori:</strong></td>
                                <td id="info-category">-</td>
                            </tr>
                            <tr>
                                <td><strong>Supplier:</strong></td>
                                <td id="info-supplier">-</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Jual:</strong></td>
                                <td id="info-price">-</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Gudang:</strong></td>
                                <td><span id="info-stock" class="badge bg-primary">0</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Jumlah yang Ditransfer *</label>
                    <input type="number" min="1" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" required>
                    @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted" id="stockWarning"></small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-arrow-right-circle"></i> Transfer ke Kasir
                    </button>
                    <a href="{{ route('cashier-items.index') }}" class="btn btn-secondary">
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
            width: '100%',
            placeholder: '-- Pilih Barang --',
            allowClear: true
        });

        const warehouseSelect = document.getElementById('warehouse_item_id');
        const quantityInput = document.getElementById('quantity');
        const itemInfo = document.getElementById('itemInfo');
        const submitBtn = document.getElementById('submitBtn');
        const stockWarning = document.getElementById('stockWarning');

        let currentStock = 0;

        // Use jQuery change event for Select2 compatibility
        $('#warehouse_item_id').on('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value) {
                // Show item info
                document.getElementById('info-code').textContent = selectedOption.dataset.code;
                document.getElementById('info-name').textContent = selectedOption.dataset.name;
                document.getElementById('info-category').textContent = selectedOption.dataset.category;
                document.getElementById('info-supplier').textContent = selectedOption.dataset.supplier;
                document.getElementById('info-price').textContent = 'Rp. ' + parseFloat(selectedOption.dataset.price).toLocaleString('id-ID');
                document.getElementById('info-stock').textContent = selectedOption.dataset.stock;

                currentStock = parseInt(selectedOption.dataset.stock);
                itemInfo.style.display = 'block';

                // Set max quantity
                quantityInput.max = currentStock;
                validateQuantity();
            } else {
                itemInfo.style.display = 'none';
                currentStock = 0;
            }
        });

        quantityInput.addEventListener('input', validateQuantity);

        function validateQuantity() {
            const qty = parseInt(quantityInput.value) || 0;

            if (qty > currentStock) {
                stockWarning.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Jumlah melebihi stok gudang!</span>';
                submitBtn.disabled = true;
            } else if (qty > 0) {
                stockWarning.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Stok gudang akan berkurang ' + qty + ', stok kasir akan bertambah ' + qty + '</span>';
                submitBtn.disabled = false;
            } else {
                stockWarning.textContent = '';
                submitBtn.disabled = false;
            }
        }

        // Trigger change on page load if there's old input
        if (warehouseSelect.value) {
            $(warehouseSelect).trigger('change');
        }
    });
</script>
@endpush
@endsection