@extends('layouts.app')

@section('title', 'Riwayat Penyesuaian Stok (Opname)')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-clipboard-check"></i> Riwayat Penyesuaian Stok (Opname)
        </h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Laporan
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filter
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stockAdjustments') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Target</label>
                    <select name="target" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="cashier" {{ ($target ?? '') === 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="warehouse" {{ ($target ?? '') === 'warehouse' ? 'selected' : '' }}>Gudang</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Tipe</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="increase" {{ ($type ?? '') === 'increase' ? 'selected' : '' }}>Penambahan</option>
                        <option value="decrease" {{ ($type ?? '') === 'decrease' ? 'selected' : '' }}>Pengurangan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-semibold small">Tanggal Mulai</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-semibold small">Tanggal Akhir</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('reports.stockAdjustments') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #ff0000; color: white;">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Target</th>
                        <th>Barang</th>
                        <th>Tipe</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Sebelum</th>
                        <th class="text-center">Sesudah</th>
                        <th>Alasan</th>
                        <th>User</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $index => $adj)
                    <tr>
                        <td>{{ $adjustments->firstItem() + $index }}</td>
                        <td>
                            <span class="d-block fw-bold">{{ $adj->created_at->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ $adj->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            @if($adj->target === 'warehouse')
                                <span class="badge bg-warning text-dark"><i class="bi bi-box-seam"></i> Gudang</span>
                            @else
                                <span class="badge bg-primary"><i class="bi bi-shop"></i> Kasir</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-primary">{{ $adj->item_name }}</strong>
                            <br>
                            <span class="badge bg-light text-dark border">{{ $adj->item_code }}</span>
                        </td>
                        <td>
                            @if($adj->type === 'increase')
                                <span class="badge bg-success"><i class="bi bi-arrow-up"></i> Tambah</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-arrow-down"></i> Kurang</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold fs-5">{{ $adj->quantity }}</td>
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
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check fs-1 opacity-25"></i>
                            <p class="mt-2">Belum ada data penyesuaian stok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $adjustments->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
