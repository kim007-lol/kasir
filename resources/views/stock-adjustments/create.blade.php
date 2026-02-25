@extends(auth()->check() && auth()->user()->role === 'kasir' ? 'layouts.cashier' : 'layouts.app')

@section('title', 'Buat Penyesuaian Stok')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        @php
            $routePrefix = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.' : '';
        @endphp
        <a href="{{ route($routePrefix . 'stock-adjustments.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-clipboard-plus"></i> Buat Penyesuaian Stok
        </h2>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route($routePrefix . 'stock-adjustments.store') }}" id="adjustmentForm">
                        @csrf

                        {{-- Select Item --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Item <span class="text-danger">*</span></label>
                            <select name="cashier_item_id" id="itemSelect" class="form-select @error('cashier_item_id') is-invalid @enderror" required>
                                <option value="">-- Cari dan pilih item --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}"
                                            data-stock="{{ $item->stock }}"
                                            data-code="{{ $item->code }}"
                                            {{ old('cashier_item_id') == $item->id ? 'selected' : '' }}>
                                        [{{ $item->code }}] {{ $item->name }} — Stok: {{ $item->stock }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cashier_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Current Stock Info --}}
                        <div class="mb-3" id="stockInfo" style="display: none;">
                            <div class="alert alert-info mb-0 d-flex align-items-center gap-3">
                                <i class="bi bi-box-seam fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Stok Saat Ini</div>
                                    <div class="fs-4 fw-bold" id="currentStockDisplay">0</div>
                                </div>
                                <div class="ms-auto text-end" id="previewContainer" style="display: none;">
                                    <div class="fw-semibold">Estimasi Setelah</div>
                                    <div class="fs-4 fw-bold" id="previewStock">0</div>
                                </div>
                            </div>
                        </div>

                        {{-- Type --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Penyesuaian <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="typeDecrease" value="decrease"
                                           {{ old('type', 'decrease') === 'decrease' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-semibold" for="typeDecrease">
                                        <i class="bi bi-arrow-down-circle"></i> Kurangi Stok
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="typeIncrease" value="increase"
                                           {{ old('type') === 'increase' ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-semibold" for="typeIncrease">
                                        <i class="bi bi-arrow-up-circle"></i> Tambah Stok
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantityInput"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   min="1" value="{{ old('quantity', 1) }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reason --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                <option value="">-- Pilih alasan --</option>
                                <option value="stock_opname" {{ old('reason') === 'stock_opname' ? 'selected' : '' }}>📋 Stock Opname (Penyesuaian hasil hitung fisik)</option>
                                <option value="rusak" {{ old('reason') === 'rusak' ? 'selected' : '' }}>💔 Rusak / Kadaluarsa</option>
                                <option value="hilang" {{ old('reason') === 'hilang' ? 'selected' : '' }}>🔍 Hilang / Tidak Ditemukan</option>
                                <option value="salah_input" {{ old('reason') === 'salah_input' ? 'selected' : '' }}>✏️ Koreksi Salah Input</option>
                                <option value="lainnya" {{ old('reason') === 'lainnya' ? 'selected' : '' }}>📝 Lainnya</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan <small class="text-muted">(opsional)</small></label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="3" placeholder="Keterangan tambahan...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route($routePrefix . 'stock-adjustments.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-danger" id="submitBtn">
                                <i class="bi bi-check-lg"></i> Simpan Penyesuaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 0.75rem; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemSelect = document.getElementById('itemSelect');
        const stockInfo = document.getElementById('stockInfo');
        const currentStockDisplay = document.getElementById('currentStockDisplay');
        const previewContainer = document.getElementById('previewContainer');
        const previewStock = document.getElementById('previewStock');
        const quantityInput = document.getElementById('quantityInput');
        const typeRadios = document.querySelectorAll('input[name="type"]');

        let currentStock = 0;

        // Initialize Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#itemSelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Cari dan pilih item --',
                allowClear: true,
            }).on('change', function () {
                updateStockPreview();
            });
        }

        itemSelect.addEventListener('change', updateStockPreview);
        quantityInput.addEventListener('input', updatePreview);
        typeRadios.forEach(r => r.addEventListener('change', updatePreview));

        function updateStockPreview() {
            const selected = itemSelect.options[itemSelect.selectedIndex];
            if (selected && selected.value) {
                currentStock = parseInt(selected.dataset.stock || 0);
                currentStockDisplay.textContent = currentStock;
                stockInfo.style.display = '';
                updatePreview();
            } else {
                stockInfo.style.display = 'none';
                currentStock = 0;
            }
        }

        function updatePreview() {
            const qty = parseInt(quantityInput.value) || 0;
            const type = document.querySelector('input[name="type"]:checked')?.value || 'decrease';

            if (qty > 0 && currentStock >= 0) {
                let newStock = type === 'increase' ? currentStock + qty : currentStock - qty;
                previewStock.textContent = newStock;
                previewStock.className = 'fs-4 fw-bold ' + (newStock < 0 ? 'text-danger' : 'text-success');
                previewContainer.style.display = '';
            } else {
                previewContainer.style.display = 'none';
            }
        }

        // Form submit confirmation
        document.getElementById('adjustmentForm').addEventListener('submit', function (e) {
            const selected = itemSelect.options[itemSelect.selectedIndex];
            const itemName = selected ? selected.text : 'Unknown';
            const qty = quantityInput.value;
            const type = document.querySelector('input[name="type"]:checked')?.value;
            const typeLabel = type === 'increase' ? 'MENAMBAH' : 'MENGURANGI';

            if (typeof Swal !== 'undefined') {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Penyesuaian',
                    html: `Anda akan <b>${typeLabel}</b> stok sebanyak <b>${qty}</b> untuk:<br><b>${itemName}</b>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    cancelButtonColor: '#adb5bd',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.submit();
                    }
                });
            }
        });

        // Trigger initial display if old value present
        if (itemSelect.value) {
            updateStockPreview();
        }
    });
</script>
@endsection
