@extends('layouts.cashier')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);">
            <div class="card-body p-4 text-white">
                <h2 class="fw-bold mb-0">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="mb-0 mt-2 opacity-75">Siap melayani pelanggan hari ini?</p>
            </div>
        </div>
    </div>
</div>

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
</div>

<style>
    .hover-card {
        transition: transform 0.3s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection