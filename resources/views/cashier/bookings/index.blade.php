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
                <i class="bi bi-check2-all"></i> Siap
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
                    <div class="d-flex gap-2 flex-wrap">
                        {{-- PENDING: Accept or Reject --}}
                        @if($booking->status === 'pending')
                        <form action="{{ route('cashier.bookings.accept', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"
                                onclick="return confirm('Terima pesanan ini? Stok akan dikurangi.')">
                                <i class="bi bi-check-lg"></i> Terima
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $booking->id }}">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                        @endif

                        {{-- CONFIRMED: Process --}}
                        @if($booking->status === 'confirmed')
                        <form action="{{ route('cashier.bookings.process', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-fire"></i> Proses
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $booking->id }}">
                            <i class="bi bi-x-lg"></i> Batalkan
                        </button>
                        @endif

                        {{-- PROCESSING: Ready --}}
                        @if($booking->status === 'processing')
                        <form action="{{ route('cashier.bookings.ready', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check2-all"></i> Siap!
                            </button>
                        </form>
                        @endif

                        {{-- READY: Complete --}}
                        @if($booking->status === 'ready')
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#completeModal{{ $booking->id }}">
                            <i class="bi bi-bag-check"></i> Selesaikan
                        </button>
                        @endif

                        <a href="{{ route('cashier.bookings.show', $booking) }}" class="btn btn-outline-secondary btn-sm">
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

        {{-- Complete Modal --}}
        @if($booking->canBeCompleted())
        <div class="modal fade" id="completeModal{{ $booking->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('cashier.bookings.complete', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Selesaikan Pesanan {{ $booking->booking_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Total pesanan: <strong>Rp {{ number_format($booking->total, 0, ',', '.') }}</strong></p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Metode Pembayaran *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="cash{{ $booking->id }}" value="cash" checked>
                                        <label class="form-check-label" for="cash{{ $booking->id }}">
                                            <i class="bi bi-cash"></i> Cash
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="qris{{ $booking->id }}" value="qris">
                                        <label class="form-check-label" for="qris{{ $booking->id }}">
                                            <i class="bi bi-qr-code"></i> QRIS
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 mb-0" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle"></i>
                                Menyelesaikan pesanan akan otomatis membuat transaksi di sistem.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-bag-check"></i> Selesaikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

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
    // ===== SMART NOTIFICATION SYSTEM =====
    // Primary: WebSocket (Reverb) — instant
    // Fallback: AJAX Polling — 30s (or 10s if WebSocket goes down)

    let lastPendingCount = {{ $pendingCount }};
    let wsConnected = false;
    let pollInterval = null;
    const POLL_NORMAL = 30000;   // 30s when WebSocket is active
    const POLL_FALLBACK = 10000; // 10s when WebSocket is down
    const currentStatus = '{{ $status }}';

    // ===== POLLING (Fallback) =====
    function pollBookings() {
        fetch('{{ route("cashier.bookings.pendingCount") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('header-pending-badge');
                const navBadge = document.getElementById('nav-booking-badge');

                // Update badges
                if (data.pending_count > 0) {
                    if (badge) { badge.textContent = data.pending_count; badge.style.display = ''; }
                    if (navBadge) { navBadge.textContent = data.pending_count; navBadge.style.display = ''; }
                } else {
                    if (badge) badge.style.display = 'none';
                    if (navBadge) navBadge.style.display = 'none';
                }

                // New order detected via polling (fallback mode)
                if (data.pending_count > lastPendingCount) {
                    try { document.getElementById('notification-sound').play(); } catch (e) {}

                    if (typeof toastr !== 'undefined') {
                        toastr.info('Ada pesanan baru masuk!', 'Pesanan Baru (Polling)');
                    }

                    // Auto reload if on relevant tab and no modal open
                    if (['pending', 'all'].includes(currentStatus) && !document.querySelector('.modal.show')) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }

                lastPendingCount = data.pending_count;
            })
            .catch(err => console.log('Polling error:', err));
    }

    // Start polling with adjustable interval
    function startPolling(interval) {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(pollBookings, interval);
        console.log(`📡 Polling started: every ${interval / 1000}s`);
    }

    // Initial poll + start with normal speed
    pollBookings();
    startPolling(POLL_NORMAL);

    // ===== WEBSOCKET (Primary) =====
    setTimeout(() => {
        if (window.Echo) {
            console.log('🔌 Connecting to WebSocket...');

            // Connection established
            window.Echo.connector.pusher.connection.bind('connected', () => {
                wsConnected = true;
                console.log('✅ WebSocket Connected! (Primary mode active)');
                if (typeof toastr !== 'undefined') {
                    toastr.success('Real-time aktif', 'Online', {timeOut: 2000});
                }
                // Slow down polling since WebSocket handles it
                startPolling(POLL_NORMAL);
            });

            // Connection lost — switch to fast polling
            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                wsConnected = false;
                console.warn('⚠️ WebSocket Disconnected. Switching to fast polling...');
                if (typeof toastr !== 'undefined') {
                    toastr.warning('WebSocket terputus. Beralih ke mode polling.', 'Offline');
                }
                startPolling(POLL_FALLBACK);
            });

            // Connection unavailable
            window.Echo.connector.pusher.connection.bind('unavailable', () => {
                wsConnected = false;
                console.error('❌ WebSocket Unavailable. Using polling fallback.');
                startPolling(POLL_FALLBACK);
            });

            // Listen for new bookings via WebSocket
            window.Echo.channel('bookings')
                .listen('NewBookingCreated', (e) => {
                    console.log('⚡ Real-time booking received:', e.booking);

                    try { document.getElementById('notification-sound').play(); } catch (err) {}

                    if (typeof toastr !== 'undefined') {
                        toastr.info('Pesanan Baru: ' + (e.booking.booking_code || ''), 'Pesanan Masuk!');
                    }

                    // Reload if on relevant tab and no modal open
                    if (['pending', 'all'].includes(currentStatus) && !document.querySelector('.modal.show')) {
                        setTimeout(() => window.location.reload(), 500);
                    }
                });
        } else {
            // Echo not available at all — use fast polling
            console.warn('⚠️ Echo not loaded. Using polling fallback only.');
            startPolling(POLL_FALLBACK);
        }
    }, 1000);
</script>
@endpush

<style>
    .nav-pills .nav-link {
        font-weight: 600;
        border-radius: 0.75rem;
        margin-right: 0.25rem;
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
            white-space: nowrap;
        }

        .card-footer .d-flex {
            flex-direction: column;
        }

        .card-footer .btn {
            width: 100%;
        }
    }
</style>