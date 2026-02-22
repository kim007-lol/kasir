@extends(auth()->check() && auth()->user()->role === 'kasir' ? 'layouts.cashier' : 'layouts.app')

@section('title', 'History Transaksi')

@php
$routePrefix = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.' : '';
@endphp

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-clock-history"></i> History Transaksi
        </h2>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filter & Pencarian
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route($routePrefix . 'history.index') }}" class="row g-3">
                <!-- Quick Filter Tabs -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Filter Cepat:</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" {{ ($filter ?? '') == 'all' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="filter-all">Semua</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-today" value="today" {{ ($filter ?? 'today') == 'today' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="filter-today">Hari Ini</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-week" value="week" {{ ($filter ?? '') == 'week' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="filter-week">Minggu Ini</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-month" value="month" {{ ($filter ?? '') == 'month' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="filter-month">Bulan Ini</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-custom" value="custom" {{ ($filter ?? '') == 'custom' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="filter-custom">Custom</label>
                    </div>
                </div>

                <!-- Custom Date Range (shown when custom is selected) -->
                <div class="col-md-4 {{ ($filter ?? '') == 'custom' ? '' : 'd-none' }}" id="custom-dates">
                    <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-4 {{ ($filter ?? '') == 'custom' ? '' : 'd-none' }}" id="custom-dates-end">
                    <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>

                <!-- Payment Method Filter -->
                <div class="col-md-3">
                    <label for="payment_method" class="form-label fw-semibold">Metode Pembayaran</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="" {{ empty($paymentMethod) ? 'selected' : '' }}>Semua Metode</option>
                        <option value="cash" {{ ($paymentMethod ?? '') == 'cash' ? 'selected' : '' }}>Tunai (CASH)</option>
                        <option value="qris" {{ ($paymentMethod ?? '') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>

                <!-- Source Filter -->
                <div class="col-md-3">
                    <label for="source" class="form-label fw-semibold">Sumber Pesanan</label>
                    <select class="form-select" id="source" name="source">
                        <option value="" {{ empty($source) ? 'selected' : '' }}>Semua Sumber</option>
                        <option value="pos" {{ ($source ?? '') == 'pos' ? 'selected' : '' }}>Kasir (POS)</option>
                        <option value="online" {{ ($source ?? '') == 'online' ? 'selected' : '' }}>Pemesanan Online</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Cari Invoice / Nama Pembeli</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Ketik invoice atau nama pembeli...">
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Terapkan Filter
                        </button>
                        <a href="{{ route($routePrefix . 'history.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle custom date fields
        document.querySelectorAll('input[name="filter"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const customDates = document.getElementById('custom-dates');
                const customDatesEnd = document.getElementById('custom-dates-end');
                if (this.value === 'custom') {
                    customDates.classList.remove('d-none');
                    customDatesEnd.classList.remove('d-none');
                } else {
                    customDates.classList.add('d-none');
                    customDatesEnd.classList.add('d-none');
                }
            });
        });
    </script>

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff6b6b; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Invoice</th>
                            <th class="d-none d-md-table-cell">Pembeli</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th class="d-none d-md-table-cell">Sumber</th>
                            <th>Kasir</th>
                            <th class="d-none d-lg-table-cell">Tanggal</th>
                            <th style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $loop->iteration + ($transactions->perPage() * ($transactions->currentPage() - 1)) }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $transaction->invoice }}</span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $transaction->customer_name ?? '-' }}
                            </td>
                            <td>
                                <strong>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                                <br>
                                <small class="text-muted d-lg-none">
                                    {{ $transaction->created_at->isoFormat('dddd, D MMMM Y') }}
                                </small>
                            </td>
                            <td>
                                <span class="badge {{ $transaction->payment_method == 'qris' ? 'bg-info' : 'bg-success' }}">
                                    {{ strtoupper($transaction->payment_method) }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge {{ $transaction->source == 'online' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ strtoupper($transaction->source) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $transaction->cashier_name ?? $transaction->user->name ?? 'System' }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ $transaction->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</small>
                            </td>
                            <td>
                                <a href="{{ route($routePrefix . 'history.show', $transaction) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Tidak ada data transaksi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</div>
<style>
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        /* Filter quick tabs horizontal scroll */
        .btn-group.w-100 {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 3px;
        }
        .btn-group.w-100 .btn {
            white-space: nowrap;
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
        }
        .btn-group.w-100::-webkit-scrollbar {
            height: 3px;
        }
        .btn-group.w-100::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        /* Table compact */
        .table th, .table td {
            font-size: 0.78rem;
            padding: 0.35rem 0.3rem;
        }
        .table .badge {
            font-size: 0.68rem;
        }

        /* Page header */
        h2.fw-bold {
            font-size: 1.2rem;
        }

        /* Cards */
        .card-body {
            padding: 0.85rem;
        }
        .card-header {
            padding: 0.6rem 0.85rem;
        }
        .card-header h5 {
            font-size: 0.95rem;
        }

        /* Filter form */
        .row.g-3 .form-label {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .table th, .table td {
            font-size: 0.72rem;
            padding: 0.25rem 0.2rem;
        }
        h2.fw-bold {
            font-size: 1.1rem;
        }
    }
</style>
</div>
@endsection