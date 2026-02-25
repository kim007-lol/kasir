@extends('layouts.app')

@section('title', 'Riwayat Stok Masuk')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-box-arrow-in-down"></i> Riwayat Stok Masuk Gudang
        </h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Laporan
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filter Periode
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stockEntries') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('reports.stockEntries') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="data-container">
        <!-- Stock Entries Table -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff0000; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal Masuk</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th class="d-none d-lg-table-cell">Kategori</th>
                            <th class="d-none d-md-table-cell">Supplier</th>
                            <th class="text-center">Jumlah Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $index => $entry)
                        <tr>
                            <td>{{ ($entries->currentPage() - 1) * $entries->perPage() + $index + 1 }}</td>
                            <td>
                                <small>{{ $entry->entry_date->format('d/m/Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $entry->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $entry->warehouseItem->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $entry->warehouseItem->name }}</strong>
                                <br>
                                <small class="text-muted d-md-none">
                                    {{ $entry->warehouseItem->category->name }} | {{ $entry->supplier->name }}
                                </small>
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $entry->warehouseItem->category->name }}</td>
                            <td class="d-none d-md-table-cell">{{ $entry->supplier->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-success" style="font-size: 0.9rem; padding: 0.5rem 0.8rem;">
                                    +{{ $entry->quantity }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Tidak ada riwayat stok masuk</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $entries->withQueryString()->links() }}
        </div>
    </div>
</div>

<style>
    .table-responsive {
        border-radius: 0.5rem;
    }

    .card {
        border-radius: 0.75rem;
    }

    .badge {
        padding: 0.4rem 0.6rem;
    }

    @media (max-width: 576px) {
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
</style>
@endsection