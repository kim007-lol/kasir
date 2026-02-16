@extends('layouts.cashier')

@section('title', 'Barang Titipan')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-bag-check"></i> Barang Titipan
        </h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addConsignmentModal">
            <i class="bi bi-plus-circle"></i> Tambah Barang Titipan
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle"></i> Barang titipan bersifat <strong>harian</strong> — stok otomatis hilang keesokan harinya.
                Setiap hari harus ditambahkan ulang. Laba dihitung dari selisih <strong>Harga Jual − Harga Awal</strong>.
            </p>
        </div>
    </div>

    <!-- Filter Tanggal -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('cashier.consignment.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1">Filter Harian</label>
                        <input type="date" class="form-control" name="date" value="{{ $filterDate ?? '' }}" {{ ($startDate && $endDate) ? '' : '' }}>
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <span class="text-muted px-2 pb-2">atau</span>
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1">Dari Tanggal</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('cashier.consignment.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Hari Ini
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($filterDate)
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-calendar-day"></i> Menampilkan barang titipan tanggal: <strong>{{ \Carbon\Carbon::parse($filterDate)->isoFormat('dddd, D MMMM Y') }}</strong>
        @if($filterDate == today()->toDateString())
        <span class="badge bg-success ms-2">Hari Ini</span>
        @else
        <span class="badge bg-secondary ms-2">Riwayat</span>
        @endif
    </div>
    @elseif($startDate && $endDate)
    <div class="alert alert-info py-2 mb-3">
        <i class="bi bi-calendar-range"></i> Menampilkan barang titipan dari <strong>{{ \Carbon\Carbon::parse($startDate)->isoFormat('dddd, D MMMM Y') }}</strong> sampai <strong>{{ \Carbon\Carbon::parse($endDate)->isoFormat('dddd, D MMMM Y') }}</strong>
    </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #ff6b6b; color: white;">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Barang</th>
                        <th>Sumber</th>
                        <th class="text-center">Harga Awal</th>
                        <th class="text-center">Harga Jual</th>
                        <th class="text-center">Laba/pcs</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($consignmentItems as $index => $item)
                    @php
                    $profit = $item->selling_price - $item->cost_price;
                    @endphp
                    <tr>
                        <td>{{ ($consignmentItems->currentPage() - 1) * $consignmentItems->perPage() + $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            <br><small class="text-muted">{{ $item->code }}</small>
                            <br><small class="text-muted">{{ $item->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</small>
                            @if($item->updated_at != $item->created_at)
                            <br><small class="text-info"><i class="bi bi-clock-history"></i> Last update: {{ $item->updated_at->format('H:i') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $item->consignment_source ?? '-' }}</span>
                        </td>
                        <td class="text-center">Rp. {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                        <td class="text-center">Rp. {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($profit > 0)
                            <span class="text-success fw-bold">+Rp. {{ number_format($profit, 0, ',', '.') }}</span>
                            @elseif($profit < 0)
                                <span class="text-danger fw-bold">-Rp. {{ number_format(abs($profit), 0, ',', '.') }}</span>
                                @else
                                <span class="text-muted">Rp. 0</span>
                                @endif
                        </td>
                        <td class="text-center">
                            @if($item->stock <= 0)
                                <span class="badge bg-danger">Habis</span>
                                @elseif($item->stock <= 5)
                                    <span class="badge bg-warning text-dark">{{ $item->stock }}</span>
                                    @else
                                    <span class="badge bg-success">{{ $item->stock }}</span>
                                    @endif
                        </td>
                        <td class="text-center">
                            @if($item->created_at->isToday())
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('cashier.consignment.edit', $item) }}" class="btn btn-warning btn-sm text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('cashier.consignment.destroy', $item) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="badge bg-secondary">Riwayat</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x fs-1 opacity-25"></i>
                            <p class="mt-2">Belum ada barang titipan untuk tanggal ini. Klik tombol <strong>"Tambah Barang Titipan"</strong> untuk menambahkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $consignmentItems->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Barang Titipan -->
<div class="modal fade" id="addConsignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff6b6b; color: white;">
                <h5 class="modal-title fw-bold">Tambah Barang Titipan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cashier.consignment.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang *</label>
                        <input type="text" class="form-control" name="name" required placeholder="Nama barang titipan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sumber Titipan *</label>
                        <input type="text" class="form-control" name="consignment_source" required placeholder="Contoh: Bu Ani / Kantin A">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Awal (Rp) *</label>
                            <input type="number" class="form-control" name="cost_price" required min="0" placeholder="Harga beli/modal">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Jual (Rp) *</label>
                            <input type="number" class="form-control" name="selling_price" required min="0" placeholder="Harga jual ke pelanggan">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok *</label>
                        <input type="number" class="form-control" name="stock" required min="1" placeholder="Jumlah barang">
                    </div>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Barang titipan bersifat <strong>harian</strong>.
                        Besok stok akan otomatis hilang dari daftar transaksi dan harus ditambahkan ulang.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection