@extends('layouts.app')

@section('title', 'Stok Item Kasir')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-cart-check"></i> Stok Item Kasir (Display)
        </h2>
        <button class="btn btn-primary text-white fw-bold" data-bs-toggle="modal" data-bs-target="#warehouseStockModal">
            <i class="bi bi-plus-circle"></i> Tambah dari Gudang
        </button>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('cashier-items.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label fw-semibold">Cari Stok Kasir</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama atau kode barang...">
                </div>
                <div class="col-md-3 position-relative">
                    <label for="category_id" class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select select2-basic">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if((isset($search) && $search) || (isset($categoryId) && $categoryId))
                        <a href="{{ route('cashier-items.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff0000; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th class="d-none d-lg-table-cell">Kategori</th>
                            <th class="d-none d-md-table-cell">Harga Jual</th>
                            <th class="d-none d-md-table-cell">Potongan (Rp)</th>
                            <th class="d-none d-sm-table-cell">Stok</th>
                            <th class="d-none d-md-table-cell text-center">Exp Date</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashierItems as $index => $item)
                        <tr>
                            <td>{{ $cashierItems->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                <br>
                                <small class="text-muted d-md-none">
                                    {{ $item->category->name ?? '-' }} |
                                    @if($item->discount > 0)
                                    <s class="opacity-50">Rp. {{ number_format($item->selling_price, 0, ',', '.') }}</s>
                                    @endif
                                    Rp. {{ number_format($item->final_price, 0, ',', '.') }}
                                </small>
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $item->category->name ?? '-' }}</td>
                            <td class="d-none d-md-table-cell">
                                @if($item->discount > 0)
                                <small class="text-muted text-decoration-line-through">
                                    Rp. {{ number_format($item->selling_price, 0, ',', '.') }}
                                </small><br>
                                @endif
                                <strong>Rp. {{ number_format($item->final_price, 0, ',', '.') }}</strong>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($item->discount > 0)
                                <span class="badge bg-warning text-dark">Rp. {{ number_format($item->discount, 0, ',', '.') }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @php
                                $stockClass = 'bg-success';
                                if ($item->stock < 10) {
                                    $stockClass='bg-danger' ;
                                    } elseif ($item->stock <= 20) {
                                        $stockClass='bg-warning' ;
                                        }
                                        @endphp
                                        <span class="badge {{ $stockClass }}">{{ $item->stock }}</span>
                            </td>
                            <td class="d-none d-md-table-cell text-center">
                                @if($item->expiry_date)
                                    @php
                                        $daysUntilExpiry = now()->diffInDays($item->expiry_date, false);
                                    @endphp
                                    @if($daysUntilExpiry < 0)
                                        <span class="badge bg-dark text-white" title="Sudah Kadaluarsa">
                                            <i class="bi bi-x-circle"></i> EXPIRED
                                        </span>
                                    @elseif($daysUntilExpiry <= 30)
                                        <span class="badge bg-danger" title="Hampir Kadaluarsa">
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
                                    <a href="{{ route('cashier-items.edit', $item) }}" class="btn btn-warning btn-sm text-white" title="Edit Stok">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('cashier-items.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, 'Item ini akan dihapus dari kasir dan stok dikembalikan ke gudang!')">
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
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Tidak ada data item kasir</p>
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

<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.5rem;
    }
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
    @media (max-width: 576px) {
        .btn-sm {
            padding: 0.15rem 0.3rem;
            font-size: 0.7rem;
        }
        table { font-size: 0.8rem; }
        th, td { padding: 0.5rem 0.3rem !important; }
        .badge { font-size: 0.7rem; }
        small { font-size: 0.7rem; }
    }
    .table-responsive { border-radius: 0.5rem; }
    .card { border-radius: 0.75rem; }
    .badge { padding: 0.4rem 0.6rem; }
</style>

@push('modals')
<!-- Modal Tambah Stok dari Gudang -->
<div class="modal fade" id="warehouseStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0 pb-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-box-arrow-down text-success me-2"></i> Tambah Stok dari Gudang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('cashier-items.store') }}" method="POST" id="warehouseStockForm">
                    @csrf

                    <div class="mb-4 position-relative">
                        <label class="form-label fw-semibold text-dark">Pilih Barang dari Gudang <span class="text-danger">*</span></label>
                        <select class="form-select select2-basic" name="warehouse_item_id" id="warehouse_item_select" required>
                            <option value="">-- Cari dan Pilih Barang --</option>
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

                    <div id="selectedItemInfo" class="alert alert-info border-0 shadow-sm d-none mb-4">
                        <div class="d-flex align-items-center mb-2">
                             <i class="bi bi-info-circle-fill me-2 fs-5 text-info"></i>
                             <strong class="text-dark">Informasi Barang Terpilih</strong>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-sm-4">
                                <strong class="text-muted d-block mb-1">Nama Barang</strong>
                                <span id="infoName" class="fw-medium text-dark">-</span>
                            </div>
                            <div class="col-sm-4 border-start border-info border-opacity-25 px-3">
                                <strong class="text-muted d-block mb-1">Stok Tersedia</strong>
                                <span id="infoStock" class="fw-bold text-success fs-6">-</span>
                            </div>
                            <div class="col-sm-4 border-start border-info border-opacity-25 px-3">
                                <strong class="text-muted d-block mb-1">Harga Jual</strong>
                                <span class="fw-medium text-dark d-flex align-items-center">
                                    <span class="text-secondary me-1">Rp</span>
                                    <span id="infoPrice">-</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-semibold text-dark">Jumlah yang Ditambahkan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-plus-slash-minus text-muted"></i></span>
                                <input type="number" class="form-control border-start-0 ps-0 text-dark fw-medium" name="quantity" id="quantity_input" required min="1" placeholder="Masukkan jumlah">
                            </div>
                            <div class="form-text text-muted"><i class="bi bi-arrow-right-short"></i> Stok akan dipindah dari gudang ke kasir</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Diskon Jual (Rp) <span class="badge bg-light text-secondary fw-normal ms-1 border border-secondary border-opacity-25">Opsional</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" min="0" class="form-control border-start-0 ps-0 text-dark fw-medium" name="discount" placeholder="Contoh: 1500 / 0">
                            </div>
                            <div class="form-text text-muted"><i class="bi bi-tag"></i> Diskon khusus etalase penjualan</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 bg-light py-3 px-4 rounded-bottom">
                <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="warehouseStockForm" class="btn btn-success px-4 fw-medium"><i class="bi bi-check-circle me-1"></i> Tambahkan ke Kasir</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Generic initialization for static filters
        $('.select2-basic:not(#warehouse_item_select)').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $(this).parent(),
                placeholder: '-- Pilih Kategori --',
                allowClear: true
            });
        });

        // Specific initialization for Modal item selection
        $('#warehouse_item_select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Barang --',
            allowClear: true,
            dropdownParent: $('#warehouse_item_select').parent()
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
@endsection