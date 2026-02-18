@extends('layouts.cashier')

@section('title', 'Histori Booking Online')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-clock-history"></i> Histori Booking Online
        </h2>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Periode</label>
                    <select name="filter" class="form-select" onchange="toggleCustomDate(this.value)">
                        <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom</option>
                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>

                <div class="col-md-2 custom-date" style="{{ $filter == 'custom' ? '' : 'display:none' }}">
                    <label class="form-label small fw-semibold">Dari</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2 custom-date" style="{{ $filter == 'custom' ? '' : 'display:none' }}">
                    <label class="form-label small fw-semibold">Sampai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Kode / Nama..." value="{{ $search }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Total</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td><code>{{ $booking->booking_code }}</code></td>
                        <td>{{ $booking->customer_name }}</td>
                        <td class="text-center">{{ $booking->items_count ?? $booking->items->count() }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($booking->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $booking->delivery_type == 'delivery' ? 'bg-info' : 'bg-secondary' }}">
                                {{ ucfirst($booking->delivery_type) }}
                            </span>
                        </td>
                        <td><span class="badge bg-{{ $booking->status_badge }}">{{ $booking->status_label }}</span></td>
                        <td><small>{{ $booking->created_at->format('d/m/Y H:i') }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('cashier.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Belum ada histori booking</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $bookings->withQueryString()->links() }}
    </div>
</div>

<script>
    function toggleCustomDate(value) {
        document.querySelectorAll('.custom-date').forEach(el => {
            el.style.display = value === 'custom' ? '' : 'none';
        });
    }
</script>
@endsection