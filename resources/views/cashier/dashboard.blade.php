@extends('layouts.cashier')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);">
            <div class="card-body p-4 text-white">
                <h2 class="fw-bold mb-0">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="mb-0 mt-2 opacity-75">Siap melayani pelanggan hari ini?</p>
            </div>
        </div>
    </div>
</div>

{{-- Notifikasi Pesanan Baru --}}
<div class="row mb-4" id="booking-alert" style="display: none;">
    <div class="col-12">
        <a href="{{ route('cashier.bookings.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm border-start border-4 border-warning" style="animation: pulse 2s infinite;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-3">
                        <i class="bi bi-bell-fill text-warning fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <span id="pending-count">0</span> Pesanan Baru Menunggu!
                        </h5>
                        <small class="text-muted">Klik untuk melihat dan memproses pesanan</small>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted fs-4"></i>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Low Stock Alert --}}
@if(isset($lowStockItems) && $lowStockItems->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-4 border-danger">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <h6 class="fw-bold mb-0 text-danger">Stok Menipis ({{ $lowStockItems->count() }} item)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems->take(5) as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $item->stock <= 2 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $item->stock }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($lowStockItems->count() > 5)
                <a href="{{ route('cashier.stock.index') }}" class="btn btn-sm btn-outline-danger mt-2">
                    Lihat semua ({{ $lowStockItems->count() }}) →
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-md-4">
        <a href="{{ route('cashier.transactions.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex p-3 mb-3">
                        <i class="bi bi-cart-plus text-danger fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Transaksi Baru</h4>
                    <p class="text-muted mb-0">Layani pembelian pelanggan</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('cashier.stock.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-3 mb-3">
                        <i class="bi bi-box-seam text-primary fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Stok Item Kasir</h4>
                    <p class="text-muted mb-0">Cek dan kelola stok kasir</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('cashier.history.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-3 mb-3">
                        <i class="bi bi-clock-history text-success fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Riwayat Transaksi</h4>
                    <p class="text-muted mb-0">Lihat data penjualan hari ini</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('cashier.bookings.index') }}" class="text-decoration-none position-relative">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex p-3 mb-3">
                        <i class="bi bi-bag-check text-info fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">
                        Pesanan Online
                        <span class="badge bg-danger rounded-pill" id="menu-badge" style="display: none; font-size: 0.6rem;"></span>
                    </h4>
                    <p class="text-muted mb-0">Kelola pesanan booking online</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('cashier.settings') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-3 mb-3">
                        <i class="bi bi-gear-fill text-warning fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Pengaturan Toko</h4>
                    <p class="text-muted mb-0">Atur jam buka & tutup toko</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
