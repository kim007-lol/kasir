@extends('layouts.app')

@section('title', 'Stok Item Kasir')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-cart-check"></i> Stok Item Kasir (Display)
        </h2>
        <a href="{{ route('cashier-items.create') }}" class="btn btn-primary text-white fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah dari Gudang
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('cashier-items.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label fw-semibold">Cari Stok Kasir</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama atau kode barang...">
                </div>
                <div class="col-md-3">
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
                            <td colspan="8" class="text-center py-4 text-muted">
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
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-basic').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Kategori --',
            allowClear: true
        });
    });
</script>
@endpush

<style>
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    @media (max-width: 576px) {
        .btn-sm {
            padding: 0.15rem 0.3rem;
            font-size: 0.7rem;
        }

        table {
            font-size: 0.8rem;
        }

        th,
        td {
            padding: 0.5rem 0.3rem !important;
        }

        .badge {
            font-size: 0.7rem;
        }

        small {
            font-size: 0.7rem;
        }
    }

    .table-responsive {
        border-radius: 0.5rem;
    }

    .card {
        border-radius: 0.75rem;
    }

    .badge {
        padding: 0.4rem 0.6rem;
    }
</style>
@endsection