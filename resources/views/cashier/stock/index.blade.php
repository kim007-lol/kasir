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
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" name="search" value="{{ $search ?? '' }}" placeholder="Cari Kode atau Nama Barang...">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background-color: #ff6b6b; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Status</th>
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
                            <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
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
                                @if($item->stock > 0)
                                <i class="bi bi-check-circle-fill text-success" title="Ready"></i>
                                @else
                                <i class="bi bi-x-circle-fill text-secondary" title="Habis"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
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
                        <select class="form-select" name="warehouse_item_id" id="warehouse_item_select" required>
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
                        <small class="text-muted">Stok akan dikurangi dari gudang dan ditambahkan ke kasir</small>
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

@push('scripts')
<script>
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
        } else {
            document.getElementById('selectedItemInfo').classList.add('d-none');
            document.getElementById('quantity_input').max = '';
        }
    });
</script>
@endpush