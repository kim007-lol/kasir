@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Transaksi
        </h2>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filter Laporan
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <!-- Quick Filter Tabs -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Filter Periode:</label>
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

                <!-- Custom Date Range -->
                <div class="col-md-4 {{ ($filter ?? '') == 'custom' ? '' : 'd-none' }}" id="custom-dates">
                    <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-4 {{ ($filter ?? '') == 'custom' ? '' : 'd-none' }}" id="custom-dates-end">
                    <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Tampilkan Laporan
                        </button>
                        <a href="{{ route('reports.exportPdf', array_merge(request()->all(), ['type' => 'detail'])) }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-pdf"></i> PDF Detail
                        </a>
                        <a href="{{ route('reports.exportExcel', request()->all()) }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('reports.stockEntries') }}" class="btn btn-info text-white">
                            <i class="bi bi-box-arrow-in-down"></i> Riwayat Stok Masuk
                        </a>
                        <a href="{{ route('reports.transferHistory') }}" class="btn btn-warning text-white">
                            <i class="bi bi-clock-history"></i> Riwayat Transfer Kasir
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
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #ff6b6b;">
                    <div class="card-body">
                        <p class="text-muted mb-2 small">Total Transaksi</p>
                        <h3 class="mb-0" style="color: #ff6b6b;">{{ $totalTransactions }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #28a745;">
                    <div class="card-body">
                        <p class="text-muted mb-2 small">Produk Terjual</p>
                        <h3 class="mb-0" style="color: #28a745;">{{ $totalItemsSold }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #ffc107;">
                    <div class="card-body">
                        <p class="text-muted mb-2 small">Keuntungan Kotor</p>
                        <h4 class="mb-0" style="color: #ffc107;">Rp. {{ number_format($grossProfit, 0, ',', '.') }}</h4>
                        <small class="text-muted">Total Penjualan</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #17a2b8;">
                    <div class="card-body">
                        <p class="text-muted mb-2 small">Keuntungan Bersih</p>
                        <h4 class="mb-0" style="color: #17a2b8;">Rp. {{ number_format($netProfit, 0, ',', '.') }}</h4>
                        <small class="text-muted">Estimasi (Perlu purchase_price)</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #dc3545;">
                    <div class="card-body">
                        <p class="text-muted mb-2 small">Periode</p>
                        <h6 class="mb-0" style="color: #dc3545;">
                            @if($filter == 'today')
                            Hari Ini
                            @elseif($filter == 'week')
                            Minggu Ini
                            @elseif($filter == 'month')
                            Bulan Ini
                            @elseif($filter == 'custom')
                            Custom
                            @else
                            {{ \Carbon\Carbon::parse($date ?? now())->format('d/m/Y') }}
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Details with Stock Entry -->
        @if($itemDetails->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-box-seam"></i> Detail Barang & Stok
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #ff6b6b; color: white;">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th class="text-center">Terjual</th>
                                        <th class="text-center">Stok Masuk</th>
                                        <th class="text-center">Stok Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemDetails as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item['code'] }}</span>
                                        </td>
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $item['total_sold'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $item['stock_in'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                            $stockClass = 'bg-info';
                                            if ($item['current_stock'] < 10) {
                                                $stockClass='bg-danger' ;
                                                } elseif ($item['current_stock'] <=20) {
                                                $stockClass='bg-warning' ;
                                                }
                                                @endphp
                                                <span class="badge {{ $stockClass }}">{{ $item['current_stock'] }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Top Selling Items -->
        @if($topSellingItems->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy"></i> 5 Produk Terlaris
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #ff6b6b; color: white;">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Produk</th>
                                        <th>Kode</th>
                                        <th class="text-center">Jumlah Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSellingItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->item->name ?? '[Item Dihapus]' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->item->code ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $item->total_qty }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Transaction Details -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i> Detail Transaksi
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #ff6b6b; color: white;">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Invoice</th>
                                <th>Pembeli</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Waktu</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $index => $transaction)
                            <tr>
                                <td>{{ $transactions->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $transaction->invoice }}</span>
                                </td>
                                <td>
                                    {{ $transaction->customer_name ?? ($transaction->member ? $transaction->member->name : '-') }}
                                </td>
                                <td>
                                    <strong>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $transaction->payment_method == 'qris' ? 'bg-info' : 'bg-success' }}">
                                        {{ strtoupper($transaction->payment_method) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('history.show', $transaction) }}" class="btn btn-sm" style="background-color: #5b9dd9; color: white;" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p>Tidak ada transaksi pada tanggal {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $transactions->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection