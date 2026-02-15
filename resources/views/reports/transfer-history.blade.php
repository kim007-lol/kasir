@extends('layouts.app')

@section('title', 'Riwayat Transfer Stok Kasir')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-clock-history"></i> Riwayat Transfer Stok Kasir
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
            <form method="GET" action="{{ route('reports.transferHistory') }}" class="row g-3">
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
                        <a href="{{ route('reports.transferHistory') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #ff6b6b; color: white;">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Barang</th>
                        <th>Tipe / Aksi</th>
                        <th class="text-center">Jumlah</th>
                        <th>Admin / User</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>
                            <span class="d-block fw-bold">{{ $log->created_at->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <strong class="text-primary">{{ $log->item_name }}</strong>
                            <br>
                            <span class="badge bg-light text-dark border">{{ $log->item_code }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $log->type_badge }}">
                                {{ $log->type_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold fs-5">{{ number_format($log->quantity, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            {{ $log->user->name ?? '-' }}
                        </td>
                        <td>
                            <small class="text-muted">{{ $log->notes ?? '-' }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 opacity-25"></i>
                            <p class="mt-2">Belum ada riwayat transfer stok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection