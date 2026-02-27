@extends('layouts.cashier')

@section('title', 'Stok Item Kasir')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-box-seam"></i> Stok Item Kasir
        </h2>
        <div class="btn-group">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#warehouseStockModal">
                <i class="bi bi-box-arrow-down"></i> Tambah Stok dari Gudang
            </button>
        </div>
    </div>

    <!-- Search Box -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('cashier.stock.index') }}">
                <div class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" name="search" value="{{ $search ?? '' }}" placeholder="Cari Kode atau Nama Barang...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="category_id" class="form-select select2-basic">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100" type="submit">Cari</button>
                            @if((isset($search) && $search) || (isset($categoryId) && $categoryId))
                            <a href="{{ route('cashier.stock.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background-color: #ff0000; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Exp Date</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashierItems as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration + ($cashierItems->perPage() * ($cashierItems->currentPage() - 1)) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->code }}</span></td>
                            <td>
                                {{ $item->name }}
                                @if($item->is_consignment)
                                <br><small class="text-muted fst-italic">Titipan: {{ $item->consignment_source }}</small>
                                @endif
                                @if($item->discount > 0)
                                <br><span class="badge bg-danger">Diskon Rp {{ number_format($item->discount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_consignment)
                                <span class="badge bg-info">Titipan</span>
                                @else
                                {{ $item->category->name ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if($item->discount > 0)
                                <small class="text-muted text-decoration-line-through">
                                    Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                                </small><br>
                                @endif
                                <strong>Rp {{ number_format($item->final_price, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-center">
                                @php
                                $bgClass = 'bg-success';
                                if($item->stock == 0) $bgClass = 'bg-secondary';
                                elseif($item->stock < 5) $bgClass='bg-danger' ;
                                    elseif($item->stock < 10) $bgClass='bg-warning text-dark' ;
                                        @endphp
                                        <span class="badge {{ $bgClass }}">{{ $item->stock }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->expiry_date)
                                    @php
                                        $daysUntilExpiry = now()->diffInDays($item->expiry_date, false);
                                    @endphp
                                    @if($daysUntilExpiry < 0)
                                        <span class="badge bg-dark text-white" title="Sudah Kadaluarsa">
                                            <i class="bi bi-x-circle"></i> EXPIRED
                                        </span>
                                    @elseif($daysUntilExpiry <= 30)
                                        <span class="badge bg-danger blink-badge" title="Hampir Kadaluarsa">
                                            <i class="bi bi-exclamation-triangle"></i> {{ $item->expiry_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $item->expiry_date->format('d/m/Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->is_consignment)
                                <span class="badge bg-info">Titipan</span>
                                @else
                                <div class="d-flex gap-1 justify-content-center" role="group">
                                    <a href="{{ route('cashier.stock.edit', $item) }}" class="btn btn-warning btn-sm text-white" title="Edit Stok">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('cashier.stock.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, 'Item ini akan dihapus dari kasir dan stok dikembalikan ke gudang!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-white" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 opacity-25"></i>
                                <p class="mt-2">Tidak ada data barang.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $cashierItems->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Stok dari Gudang -->
<div class="modal fade" id="warehouseStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Stok dari Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('cashier.stock.storeFromWarehouse') }}" method="POST" id="warehouseStockForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang dari Gudang</label>
                        <select class="form-select select2-basic" name="warehouse_item_id" id="warehouse_item_select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($warehouseItems as $wItem)
                            <option value="{{ $wItem->id }}"
                                data-stock="{{ $wItem->stock }}"
                                data-name="{{ $wItem->name }}"
                                data-price="{{ $wItem->final_price }}">
                                {{ $wItem->code }} - {{ $wItem->name }} (Stok Gudang: {{ $wItem->stock }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="selectedItemInfo" class="alert alert-light d-none">
                        <small class="text-muted">
                            <strong>Barang:</strong> <span id="infoName">-</span><br>
                            <strong>Stok Tersedia di Gudang:</strong> <span id="infoStock">-</span><br>
                            <strong>Harga Jual:</strong> Rp <span id="infoPrice">-</span>
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah yang Ditambahkan</label>
                        <input type="number" class="form-control" name="quantity" id="quantity_input" required min="1" placeholder="Masukkan jumlah">
                        <small class="text-muted d-block">Stok akan dikurangi dari gudang dan ditambahkan ke kasir</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan Harga / Diskon Kasir (Rp) <small class="text-muted fw-normal">(Opsional)</small></label>
                        <input type="number" min="0" class="form-control" name="discount" placeholder="Contoh: 1500 (kosongkan jika tidak ingin diubah)">
                        <small class="text-muted d-block fst-italic"><i class="bi bi-info-circle"></i> Input ini akan mengatur nilai diskon barang di etalase kasir.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambahkan ke Kasir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .blink-badge {
        animation: blink-anim 1.2s ease-in-out infinite;
    }
    @keyframes blink-anim {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Generic initialization for static filters
        $('.select2-basic:not(#warehouse_item_select)').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Kategori --',
            allowClear: true
        });

        // Specific initialization for Modal item selection to fix search focus
        $('#warehouse_item_select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Barang --',
            allowClear: true,
            dropdownParent: $('#warehouseStockModal')
        });
    });

    // Warehouse Stock Modal Logic
    document.getElementById('warehouse_item_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption.dataset.stock;
        const name = selectedOption.dataset.name;
        const price = selectedOption.dataset.price;

        if (this.value) {
            document.getElementById('selectedItemInfo').classList.remove('d-none');
            document.getElementById('infoName').textContent = name;
            document.getElementById('infoStock').textContent = stock;
            document.getElementById('infoPrice').textContent = Number(price).toLocaleString('id-ID');
            document.getElementById('quantity_input').max = stock;
            const discountInput = document.querySelector('input[name="discount"]');
            if (discountInput) {
                discountInput.max = Math.max(0, parseInt(price) - 1);
            }
        } else {
            document.getElementById('selectedItemInfo').classList.add('d-none');
            document.getElementById('quantity_input').max = '';
            const discountInput = document.querySelector('input[name="discount"]');
            if (discountInput) {
                discountInput.max = '';
            }
        }
    });
</script>
@endpush