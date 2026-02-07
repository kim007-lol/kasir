@extends('layouts.app')

@section('title', 'History Transaksi')

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
            <form method="GET" action="{{ route('history.index') }}" class="row g-3">
                <!-- Quick Filter Tabs -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Filter Cepat:</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="filter" id="filter-today" value="today" {{ ($filter ?? '') == 'today' ? 'checked' : '' }}>
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
                        <a href="{{ route('history.index') }}" class="btn btn-outline-secondary">
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
                        <th class="d-none d-lg-table-cell">Tanggal</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $index => $transaction)
                    <tr>
                        <td>{{ $index + 1 }}</td>
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
                                {{ $transaction->created_at->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            <span class="badge {{ $transaction->payment_method == 'qris' ? 'bg-info' : 'bg-success' }}">
                                {{ strtoupper($transaction->payment_method) }}
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <small>{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('history.show', $transaction) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                                <span class="d-none d-md-inline">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p>Tidak ada data transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endsection