@extends('layouts.cashier')

@section('title', 'Pesanan Online')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-bag-check"></i> Pesanan Online
            @if($pendingCount > 0)
            <span class="badge bg-danger" id="header-pending-badge">{{ $pendingCount }}</span>
            @endif
        </h2>
    </div>

    <!-- Status Tabs -->
    <ul class="nav nav-pills mb-4" id="status-tabs">
        <li class="nav-item">
            <a class="nav-link tab-pending {{ $status == 'pending' ? 'active' : '' }}" href="?status=pending">
                <i class="bi bi-clock"></i> Menunggu
                @if($statusCounts['pending'] > 0)
                <span class="badge bg-white text-dark">{{ $statusCounts['pending'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-confirmed {{ $status == 'confirmed' ? 'active' : '' }}" href="?status=confirmed">
                <i class="bi bi-check-circle"></i> Dikonfirmasi
                @if($statusCounts['confirmed'] > 0)
                <span class="badge bg-white text-dark">{{ $statusCounts['confirmed'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-processing {{ $status == 'processing' ? 'active' : '' }}" href="?status=processing">
                <i class="bi bi-fire"></i> Diproses
                @if($statusCounts['processing'] > 0)
                <span class="badge bg-white text-dark">{{ $statusCounts['processing'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-ready {{ $status == 'ready' ? 'active' : '' }}" href="?status=ready">
                <i class="bi bi-check2-all"></i> Siap/Pengiriman
                @if($statusCounts['ready'] > 0)
                <span class="badge bg-white text-dark">{{ $statusCounts['ready'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-all {{ $status == 'all' ? 'active' : '' }}" href="?status=all">
                <i class="bi bi-grid"></i> Semua
            </a>
        </li>
    </ul>

    <!-- Booking Cards -->
    <div class="row g-3" id="bookings-list">
        @forelse($bookings as $booking)
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #fff5f5;">
                    <div>
                        <span class="badge bg-secondary">{{ $booking->booking_code }}</span>
                        <span class="badge bg-{{ $booking->status_badge }}">{{ $booking->status_label }}</span>
                    </div>
                    <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong><i class="bi bi-person"></i> {{ $booking->customer_name }}</strong>
                        @if($booking->customer_phone)
                        <br><small class="text-muted"><i class="bi bi-telephone"></i> {{ $booking->customer_phone }}</small>
                        @endif
                    </div>
                    <div class="mb-2">
                        <span class="badge {{ $booking->delivery_type == 'delivery' ? 'bg-info' : 'bg-secondary' }}">
                            <i class="bi {{ $booking->delivery_type == 'delivery' ? 'bi-truck' : 'bi-shop' }}"></i>
                            {{ $booking->delivery_type == 'delivery' ? 'Delivery' : 'Pickup' }}
                        </span>
                    </div>

                    <!-- Items Summary -->
                    <div class="border rounded p-2 mb-2" style="background: #f8f9fa; font-size: 0.85rem;">
                        @foreach($booking->items->take(3) as $item)
                        <div class="d-flex justify-content-between">
                            <span>{{ $item->name }} × {{ $item->qty }}</span>
                            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @if($booking->items->count() > 3)
                        <div class="text-muted text-center small">+{{ $booking->items->count() - 3 }} item lainnya</div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center fw-bold">
                        <span>Total</span>
                        <span style="color: var(--primary-dark);">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                    </div>

                    @if($booking->notes)
                    <div class="mt-2">
                        <small class="text-muted"><i class="bi bi-chat-dots"></i> {{ $booking->notes }}</small>
                    </div>
                    @endif

                    @if($booking->cancel_reason)
                    <div class="mt-2 alert alert-danger py-1 px-2 mb-0" style="font-size: 0.8rem;">
                        <strong>Alasan ditolak:</strong> {{ $booking->cancel_reason }}
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-grid gap-2 w-100">
                        {{-- PENDING: Accept or Reject --}}
                        @if($booking->status === 'pending')
                        <form action="{{ route('cashier.bookings.accept', $booking) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-check-lg"></i> Terima
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $booking->id }}">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                        @endif

                        {{-- CONFIRMED: Process --}}
                        @if($booking->status === 'confirmed')
                        <form action="{{ route('cashier.bookings.process', $booking) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-fire"></i> Proses
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $booking->id }}">
                            <i class="bi bi-x-lg"></i> Batalkan
                        </button>
                        @endif

                        {{-- PROCESSING: Ready --}}
                        @if($booking->status === 'processing')
                            @if($booking->delivery_type === 'delivery')
                            <button type="button" class="btn btn-info text-white btn-sm w-100" data-bs-toggle="modal" data-bs-target="#readyModal{{ $booking->id }}">
                                <i class="bi bi-send"></i> Kirim Pesanan
                            </button>
                            @else
                            <form action="{{ route('cashier.bookings.ready', $booking) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-bell"></i> Tandai Siap Ambil
                                </button>
                            </form>
                            @endif
                        @endif

                        {{-- READY: Complete --}}
                        @if($booking->canBeCompleted())
                            @if($booking->delivery_type === 'delivery')
                            <form action="{{ route('cashier.bookings.complete', $booking) }}" method="POST" class="m-0">
                                @csrf
                                <button type="button" class="btn btn-secondary btn-sm w-100"
                                    onclick="let form = this.closest('form'); Swal.fire({title: 'Selesaikan Pengiriman?', text: 'Pastikan kurir sudah kembali dan menyetor uang.', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Selesai', cancelButtonText: 'Batal'}).then((res) => { if(res.isConfirmed) form.submit(); });">
                                    <i class="bi bi-bag-check"></i> Pesanan Diantar/Selesai
                                </button>
                            </form>
                            @else
                            <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#completeModal{{ $booking->id }}">
                                <i class="bi bi-check2-all"></i> Selesaikan & Bayar
                            </button>
                            @endif
                        @endif

                        <a href="{{ route('cashier.bookings.show', $booking) }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        @if($booking->canBeCancelled())
        <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('cashier.bookings.reject', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Pesanan {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="cancel_reason" class="form-label fw-semibold">Alasan Penolakan *</label>
                                <textarea name="cancel_reason" class="form-control" rows="3" required
                                    placeholder="Contoh: Stok habis, toko tutup lebih awal"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg"></i> Tolak Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Ready Modal (Delivery Only) --}}
        @if($booking->status === 'processing' && $booking->delivery_type === 'delivery')
        <div class="modal fade" id="readyModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('cashier.bookings.ready', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Kirim Pesanan {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Total: <strong style="font-size: 1.2rem; color: var(--primary-dark);">Rp <span id="paymentTotal{{ $booking->id }}">{{ number_format($booking->total, 0, ',', '') }}</span></strong></p>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Pengantar Pesanan *</label>
                                <input type="text" name="assignee_name" class="form-control" 
                                    placeholder="Masukkan nama kurir pengantar" 
                                    value="{{ auth()->user()->name }}" required autocomplete="off">
                            </div>

                            <div class="alert alert-secondary my-3">
                                <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-info-circle"></i> Info Pembayaran Pelanggan</h6>
                                <p class="mb-1">
                                    Metode: <strong>{{ strtoupper($booking->payment_method ?? 'CASH') }}</strong>
                                </p>
                                @if(($booking->payment_method ?? 'cash') === 'cash')
                                <p class="mb-0">
                                    Uang Tunai: <strong>Rp {{ number_format($booking->amount_paid ?? $booking->total, 0, ',', '.') }}</strong>
                                    <br>
                                    Kembalian: <strong class="text-success">
                                        {{ ($booking->amount_paid ?? $booking->total) > $booking->total ? 'Rp ' . number_format(($booking->amount_paid ?? $booking->total) - $booking->total, 0, ',', '.') : 'Pas' }}
                                    </strong>
                                </p>
                                <input type="hidden" name="payment_method" value="cash">
                                @else
                                <input type="hidden" name="payment_method" value="qris">
                                @endif
                                <small class="text-muted mt-2 d-block"><em>*Informasi pembayaran otomatis disinkronkan dari data pilihan pemesan.</em></small>
                            </div>

                            <div class="alert alert-info py-2 mb-0" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle"></i> Transaksi akan dicatat dan struk otomatis akan dicetak.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info text-white"><i class="bi bi-printer"></i> Kirim & Cetak Struk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Complete Modal (Pickup Only) --}}
        @if($booking->status === 'ready' && $booking->delivery_type === 'pickup')
        <div class="modal fade" id="completeModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('cashier.bookings.complete', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Selesaikan Pembayaran {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Total: <strong style="font-size: 1.2rem; color: var(--primary-dark);">Rp <span id="paymentTotal{{ $booking->id }}">{{ number_format($booking->total, 0, ',', '') }}</span></strong></p>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Kasir / Yang Melayani *</label>
                                <input type="text" name="assignee_name" class="form-control" 
                                    placeholder="Masukkan nama kasir" 
                                    value="{{ auth()->user()->name }}" required autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Metode Pembayaran *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input payment-method-radio-{{ $booking->id }}" type="radio" name="payment_method"
                                            id="cash{{ $booking->id }}" value="cash" checked onchange="toggleCashInput{{ $booking->id }}(true)">
                                        <label class="form-check-label" for="cash{{ $booking->id }}"><i class="bi bi-cash"></i> Cash</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input payment-method-radio-{{ $booking->id }}" type="radio" name="payment_method"
                                            id="qris{{ $booking->id }}" value="qris" onchange="toggleCashInput{{ $booking->id }}(false)">
                                        <label class="form-check-label" for="qris{{ $booking->id }}"><i class="bi bi-qr-code"></i> QRIS</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="cashInputContainer{{ $booking->id }}">
                                <label class="form-label fw-semibold">Uang Tunai (Cash) *</label>
                                <input type="number" name="paid_amount" id="paid_amount{{ $booking->id }}" class="form-control fw-bold" 
                                    placeholder="0" value="{{ $booking->total }}" min="{{ $booking->total }}" required oninput="calculateChange{{ $booking->id }}()">
                                <div class="mt-2 fw-semibold" id="changeAmountText{{ $booking->id }}" style="color: green;">
                                    Kembalian: Pas
                                </div>
                            </div>

                            <div class="alert alert-info py-2 mb-0" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle"></i> Transaksi akan dicatat dan struk otomatis akan dicetak.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"><i class="bi bi-printer"></i> Bayar & Selesai</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @push('scripts')
        <script>
            function toggleCashInput{{ $booking->id }}(isCash) {
                const container = document.getElementById('cashInputContainer{{ $booking->id }}');
                const input = document.getElementById('paid_amount{{ $booking->id }}');
                if (isCash) {
                    container.style.display = 'block';
                    input.setAttribute('required', 'required');
                    calculateChange{{ $booking->id }}();
                } else {
                    container.style.display = 'none';
                    input.removeAttribute('required');
                }
            }

            function calculateChange{{ $booking->id }}() {
                const total = {{ $booking->total }};
                const paid = parseFloat(document.getElementById('paid_amount{{ $booking->id }}').value) || 0;
                const changeText = document.getElementById('changeAmountText{{ $booking->id }}');
                
                if (paid < total) {
                    changeText.style.color = 'red';
                    changeText.textContent = 'Kurang: Rp ' + new Intl.NumberFormat('id-ID').format(total - paid);
                } else if (paid === total) {
                    changeText.style.color = 'green';
                    changeText.textContent = 'Kembalian: Pas';
                } else {
                    changeText.style.color = 'green';
                    changeText.textContent = 'Kembalian: Rp ' + new Intl.NumberFormat('id-ID').format(paid - total);
                }
            }
        </script>
        @endpush

        @empty
        <div class="col-12">
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2">Tidak ada pesanan {{ $status !== 'all' ? 'dengan status ini' : '' }}</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $bookings->withQueryString()->links() }}
    </div>
</div>

<!-- Notification sound element -->
<audio id="notification-sound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbsGczHjqMw9zQe0smMHm50+K1ZzUlXKXN3rmGSTc0bqzP4byNUj46d7PP5b2MUUA=" type="audio/wav">
</audio>
@endsection

@push('scripts')
<script>
    @if(session('print_transaction_id'))
    document.addEventListener('DOMContentLoaded', function() {
        let printUrl = "{{ route('cashier.transactions.thermalReceipt', session('print_transaction_id')) }}";
        
        Swal.fire({
            title: 'Transaksi Berhasil!',
            text: 'Uang pas/kembalian telah dihitung. Sistem memblokir popup otomatis, silakan klik tombol di bawah untuk mencetak struk.',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-printer"></i> Buka Struk',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#28a745',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(printUrl, 'Struk Pesanan', 'width=400,height=600');
            }
        });
    });
    @endif
</script>
@endpush

@push('scripts')
<script>
    // Menyediakan konstanta status saat ini yang terbaca oleh global script (di master layout)
    // agar tabel booking otomatis mereload saat grid notifikasi trigered.
    window.currentStatus = '{{ $status }}';
</script>
@endpush

<style>
    .nav-pills .nav-link {
        font-weight: 600;
        border-radius: 0.75rem;
        margin-right: 0.75rem;
        transition: all 0.3s ease;
        color: #fff;
    }

    .nav-pills .nav-link.tab-pending {
        background-color: #f0ad4e;
    }
    .nav-pills .nav-link.tab-pending.active {
        background-color: #d48806;
        box-shadow: 0 3px 10px rgba(212, 136, 6, 0.4);
    }

    .nav-pills .nav-link.tab-confirmed {
        background-color: #17a2b8;
    }
    .nav-pills .nav-link.tab-confirmed.active {
        background-color: #0d8da0;
        box-shadow: 0 3px 10px rgba(13, 141, 160, 0.4);
    }

    .nav-pills .nav-link.tab-processing {
        background-color: #4a90d9;
    }
    .nav-pills .nav-link.tab-processing.active {
        background-color: #2563eb;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.4);
    }

    .nav-pills .nav-link.tab-ready {
        background-color: #28a745;
    }
    .nav-pills .nav-link.tab-ready.active {
        background-color: #1e7e34;
        box-shadow: 0 3px 10px rgba(30, 126, 52, 0.4);
    }

    .nav-pills .nav-link.tab-all {
        background-color: #6c757d;
    }
    .nav-pills .nav-link.tab-all.active {
        background-color: #495057;
        box-shadow: 0 3px 10px rgba(73, 80, 87, 0.4);
    }

    .nav-pills .nav-link:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }

    .nav-pills .nav-link.active {
        transform: scale(1.05);
    }

    .card {
        border-radius: 0.75rem;
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        #status-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 5px;
        }

        #status-tabs::-webkit-scrollbar {
            height: 3px;
        }

        #status-tabs::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        .nav-pills .nav-link {
            font-size: 0.78rem;
            padding: 0.4rem 0.7rem;
            margin-right: 0.5rem;
            white-space: nowrap;
        }
    }
</style>