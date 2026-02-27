@extends('layouts.booking')

@section('title', 'Menu — SmeGo')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <!-- Search & Filter Area -->
        <div class="row g-3 align-items-center mb-2">
            <div class="col-md-7 col-lg-8">
                <div class="d-flex gap-2 overflow-auto pb-2 scroll-sm" style="white-space: nowrap;">
                    <a href="{{ route('booking.menu', ['search' => request('search')]) }}"
                       class="btn category-pill {{ !request('category') ? 'active' : '' }}">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Semua
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('booking.menu', ['category' => $cat->id, 'search' => request('search')]) }}"
                           class="btn category-pill {{ request('category') == $cat->id ? 'active' : '' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-5 col-lg-4">
                <form action="{{ route('booking.menu') }}" method="GET">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="form-control search-input"
                            placeholder="Cari menu favorit Anda..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(!$isOpen)
    <div class="col-12 mb-4">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Toko Sedang Tutup!</strong><br>
                Maaf, kami hanya menerima pesanan saat jam operasional. Anda hanya dapat melihat menu kami.
            </div>
        </div>
    </div>
    @endif

    <!-- Menu Grid -->
    <div class="col-12">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse($items as $item)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm menu-card">
                    <div class="card-body p-3 d-flex flex-column">
                        <!-- Category & Stock -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $item->is_consignment ? 'bg-info' : 'bg-secondary' }} bg-opacity-25 {{ $item->is_consignment ? 'text-info' : 'text-secondary' }}" style="font-size: 0.7rem;">
                                {{ $item->is_consignment ? '🍱 Titipan' : ($item->category->name ?? 'Umum') }}
                            </span>
                            <span class="badge {{ $item->stock <= 3 ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $item->stock <= 3 ? 'text-danger' : 'text-success' }}" style="font-size: 0.7rem;">
                                {{ $item->stock <= 0 ? '❌ Habis' : '📦 ' . $item->stock }}
                            </span>
                        </div>

                        <!-- Name -->
                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $item->name }}">{{ $item->name }}</h6>

                        <!-- Price -->
                        <div class="mb-3">
                            <span class="text-danger fw-bold fs-6">Rp {{ number_format($item->final_price, 0, ',', '.') }}</span>
                        </div>

                        <!-- Add to Cart -->
                        <div class="mt-auto">
                            @if($item->stock > 0 && $isOpen)
                            <form action="{{ route('booking.cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cashier_item_id" value="{{ $item->id }}">
                                <div class="d-flex gap-2">
                                    <input type="number" name="qty" value="1" min="1" max="{{ $item->stock }}"
                                           class="form-control form-control-sm text-center" style="width: 55px;">
                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                        <i class="bi bi-cart-plus"></i> Pesan
                                    </button>
                                </div>
                            </form>
                            @elseif($item->stock <= 0)
                            <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                                <i class="bi bi-x-circle"></i> Stok Habis
                            </button>
                            @else
                            <button class="btn btn-sm btn-outline-warning w-100" disabled>
                                <i class="bi bi-clock"></i> Toko Tutup
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 w-100">
                <div class="py-5">
                    <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted">Belum ada menu yang tersedia {{ request('search') ? 'untuk pencarian "'.request('search').'"' : '' }}.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .menu-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px !important;
    }
    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    @media (max-width: 768px) {
        .menu-card .card-body {
            padding: 0.75rem !important;
        }
        .menu-card h6 {
            font-size: 0.88rem;
        }
        .menu-card .badge {
            font-size: 0.6rem !important;
        }
        .menu-card .fs-6 {
            font-size: 0.85rem !important;
        }
        /* Category filter scroll */
        .d-flex.gap-2.overflow-auto .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
        }
        .d-flex.gap-2.overflow-auto::-webkit-scrollbar {
            height: 3px;
        }
        .d-flex.gap-2.overflow-auto::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
    }

    @media (max-width: 576px) {
        .menu-card .card-body {
            padding: 0.6rem !important;
        }
        .menu-card h6 {
            font-size: 0.82rem;
        }
        .menu-card .btn-sm {
            font-size: 0.72rem;
            padding: 0.2rem 0.4rem;
        }
        .menu-card input[type="number"] {
            width: 45px !important;
            font-size: 0.75rem;
        }
    }
</style>
@endsection