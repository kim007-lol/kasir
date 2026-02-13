@extends('layouts.app')

@section('title', 'Gudang')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-building"></i> Stok Gudang
        </h2>
        <a href="{{ route('warehouse.create') }}" class="btn btn-primary text-white fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.index') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label fw-semibold">Cari Barang Gudang</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama atau kode barang...">
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if(isset($search) && $search)
                        <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
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
                    <thead style="background-color: #ff6b6b; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th class="d-none d-lg-table-cell">Kategori</th>
                            <th class="d-none d-lg-table-cell">Supplier</th>
                            <th class="d-none d-md-table-cell">Harga Beli</th>
                            <th class="d-none d-md-table-cell">Harga Jual</th>
                            <th class="d-none d-xl-table-cell">Potongan</th>
                            <th class="d-none d-xl-table-cell">Harga Akhir</th>
                            <th class="d-none d-sm-table-cell">Stok</th>
                            <th class="d-none d-xl-table-cell">Exp Date</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($warehouseItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                <br>
                                <small class="text-muted d-md-none">
                                    {{ $item->category->name ?? 'Kategori Tidak Aktif' }} | {{ $item->supplier->name ?? 'Supplier Tidak Aktif' }}
                                </small>
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $item->category->name ?? 'Kategori Tidak Aktif' }}</td>
                            <td class="d-none d-lg-table-cell">{{ $item->supplier->name ?? 'Supplier Tidak Aktif' }}</td>
                            <td class="d-none d-md-table-cell">
                                Rp. {{ number_format($item->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="d-none d-md-table-cell">
                                <strong>Rp. {{ number_format($item->selling_price, 0, ',', '.') }}</strong>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @if($item->discount > 0)
                                <span class="badge bg-warning text-dark">Rp {{ number_format($item->discount, 0, ',', '.') }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <strong class="text-success">Rp. {{ number_format($item->final_price, 0, ',', '.') }}</strong>
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
                            <td class="d-none d-xl-table-cell">
                                {{ $item->exp_date ? $item->exp_date->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center" role="group">
                                    <a href="{{ route('warehouse.edit', $item) }}" class="btn btn-warning btn-sm text-white" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('warehouse.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, 'Barang ini akan dihapus dari gudang!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-white" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Tidak ada data barang gudang</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $warehouseItems->withQueryString()->links() }}
        </div>
    </div>

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