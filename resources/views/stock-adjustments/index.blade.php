@extends(auth()->check() && auth()->user()->role === 'kasir' ? 'layouts.cashier' : 'layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-clipboard-check"></i> Stock Opname
        </h2>
        @php
            $routePrefix = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.' : '';
        @endphp
        <a href="{{ route($routePrefix . 'stock-adjustments.create') }}" class="btn btn-danger">
            <i class="bi bi-plus-lg"></i> Buat Penyesuaian
        </a>
    </div>

    {{-- Filter --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($routePrefix . 'stock-adjustments.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Cari Item</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama/kode item..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Tipe</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="increase" {{ ($type ?? '') === 'increase' ? 'selected' : '' }}>Penambahan</option>
                        <option value="decrease" {{ ($type ?? '') === 'decrease' ? 'selected' : '' }}>Pengurangan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route($routePrefix . 'stock-adjustments.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Tanggal</th>
                            <th>Item</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Stok Sebelum</th>
                            <th class="text-center">Stok Sesudah</th>
                            <th>Alasan</th>
                            <th>User</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adjustments as $i => $adj)
                        <tr>
                            <td class="text-muted">{{ $adjustments->firstItem() + $i }}</td>
                            <td>
                                <small>{{ $adj->created_at->isoFormat('DD MMM Y') }}</small><br>
                                <small class="text-muted">{{ $adj->created_at->isoFormat('HH:mm') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border me-1">{{ $adj->cashierItem->code ?? '-' }}</span>
                                {{ $adj->cashierItem->name ?? '[Dihapus]' }}
                            </td>
                            <td class="text-center">
                                @if($adj->type === 'increase')
                                    <span class="badge bg-success"><i class="bi bi-arrow-up"></i> Tambah</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-arrow-down"></i> Kurang</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold">{{ $adj->quantity }}</td>
                            <td class="text-center">{{ $adj->stock_before }}</td>
                            <td class="text-center fw-bold">{{ $adj->stock_after }}</td>
                            <td>
                                @php
                                    $reasonLabels = [
                                        'hilang' => ['Hilang', 'warning'],
                                        'rusak' => ['Rusak', 'danger'],
                                        'salah_input' => ['Salah Input', 'info'],
                                        'stock_opname' => ['Stock Opname', 'primary'],
                                        'lainnya' => ['Lainnya', 'secondary'],
                                    ];
                                    $label = $reasonLabels[$adj->reason] ?? [$adj->reason, 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $label[1] }}">{{ $label[0] }}</span>
                            </td>
                            <td>{{ $adj->user->name ?? '-' }}</td>
                            <td>
                                @if($adj->notes)
                                    <small class="text-muted" title="{{ $adj->notes }}">
                                        {{ Str::limit($adj->notes, 30) }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data penyesuaian stok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($adjustments->hasPages())
        <div class="card-footer bg-white">
            {{ $adjustments->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .card { border-radius: 0.75rem; }
    .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.025em; }
    .table td { vertical-align: middle; font-size: 0.9rem; }
</style>
@endsection
