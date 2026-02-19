@extends('layouts.booking')

@section('title', 'Status Pesanan — SmeGo')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm mb-4 text-center">
            <div class="card-body p-4">
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Kode Booking</h6>
                <h2 class="display-4 fw-bold text-primary mb-3">{{ $booking->booking_code }}</h2>

                <div class="mb-4">
                    <span id="status-badge" class="badge rounded-pill bg-{{ $booking->status_badge }} fs-5 px-4 py-2">
                        {{ $booking->status_label }}
                    </span>
                </div>

                <p id="status-message" class="text-muted">
                    @switch($booking->status)
                    @case('pending')
                    Mohon tunggu, kasir sedang mengonfirmasi pesanan Anda.
                    @break
                    @case('confirmed')
                    Pesanan dikonfirmasi! Akan segera diproses.
                    @break
                    @case('processing')
                    Sedang disiapkan oleh kasir.
                    @break
                    @case('ready')
                    @if($booking->delivery_type === 'delivery')
                        @php
                            preg_match('/\[Estimasi Sampai: (.+?)\]/', $booking->notes, $m);
                            $estimasi = $m[1] ?? '';
                        @endphp
                        <span class="text-success fw-bold">Pesanan Siap! Sedang dalam pengantaran{{ $estimasi ? ', estimasi sampai pukul ' . $estimasi : '' }}.</span>
                    @else
                        <span class="text-success fw-bold">Pesanan Siap! Silakan ambil di kasir.</span>
                    @endif
                    @break
                    @case('completed')
                    Terima kasih! Selamat menikmati.
                    @break
                    @case('cancelled')
                    <span class="text-danger fw-bold">Pesanan dibatalkan oleh kasir.</span>
                    @if($booking->cancel_reason)
                    <br><small class="text-muted mt-1 d-block"><i class="bi bi-info-circle"></i> Alasan: {{ $booking->cancel_reason }}</small>
                    @endif
                    @break
                    @default
                    Status tidak diketahui.
                    @endswitch
                </p>

                @if(in_array($booking->status, ['pending', 'confirmed', 'processing']))
                <div class="spinner-border text-primary spinner-border-sm mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.7rem;">Update otomatis setiap 10 detik</small>
                @endif

                @if($booking->status == 'ready')
                <div class="alert alert-success mt-3 small">
                    @if($booking->delivery_type === 'delivery')
                        <i class="bi bi-bicycle"></i> Pesanan sedang diantar ke alamat Anda.
                    @else
                        <i class="bi bi-bell-fill"></i> Tunjukkan kode booking ini ke kasir.
                    @endif
                </div>
                @endif
            </div>

            <!-- Items Summary -->
            <div class="card-footer bg-white p-0">
                <div class="accordion accordion-flush" id="orderItems">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed small" type="button" data-bs-toggle="collapse" data-bs-target="#itemList">
                                <span class="small fw-bold">Lihat Detail Pesanan ({{ $booking->items->count() }} Item)</span>
                            </button>
                        </h2>
                        <div id="itemList" class="accordion-collapse collapse" data-bs-parent="#orderItems">
                            <div class="accordion-body text-start p-3 bg-light">
                                <ul class="list-unstyled mb-0">
                                    @foreach($booking->items as $item)
                                    <li class="d-flex justify-content-between mb-2 pb-2 border-bottom last-border-0">
                                        <div>
                                            <span class="fw-medium">{{ $item->name }}</span>
                                            <small class="text-muted d-block">x{{ $item->qty }}</small>
                                            @if($item->notes)
                                            <small class="text-info d-block fst-italic" style="font-size: 0.7rem;">Note: {{ $item->notes }}</small>
                                            @endif
                                        </div>
                                        <span class="small fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold text-danger">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <span class="text-muted small d-block">Metode:</span>
                                    <p class="small fw-medium mb-0">
                                        @if($booking->delivery_type === 'delivery')
                                            <i class="bi bi-bicycle"></i> Pesan Antar
                                        @else
                                            <i class="bi bi-shop"></i> Ambil di Toko
                                        @endif
                                    </p>
                                </div>
                                @if($booking->delivery_type === 'delivery' && $booking->delivery_address)
                                <div class="mt-2 pt-2 border-top">
                                    <span class="text-muted small d-block">Alamat Pengiriman:</span>
                                    <p class="small mb-0">{{ $booking->delivery_address }}</p>
                                </div>
                                @endif
                                <div class="mt-2 pt-2 border-top">
                                    <span class="text-muted small d-block">Catatan:</span>
                                    <p class="small fst-italic mb-0">{{ $booking->notes ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('booking.menu') }}" class="btn btn-outline-primary">
                <i class="bi bi-plus-lg"></i> Pesan Lagi
            </a>
            <a href="{{ route('booking.history') }}" class="btn btn-link text-decoration-none text-muted">
                Lihat Riwayat Pesanan
            </a>
        </div>
    </div>
</div>

{{-- Modal DITOLAK --}}
@if($booking->status === 'cancelled')
<div class="modal fade" id="rejectedModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title"><i class="bi bi-x-circle-fill me-2"></i>Pesanan Ditolak</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold text-danger mb-2">DITOLAK</h4>
                <p class="text-muted mb-1">Kode Booking: <strong>{{ $booking->booking_code }}</strong></p>
                @if($booking->cancel_reason)
                <div class="alert alert-light border mt-3 text-start">
                    <small class="text-muted fw-bold d-block mb-1">Alasan Penolakan:</small>
                    <p class="mb-0">{{ $booking->cancel_reason }}</p>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('booking.menu') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    @if(in_array($booking->status, ['pending', 'confirmed', 'processing']))
    // Polling status
    setInterval(function() {
        fetch("{{ route('booking.api.status', $booking->id) }}")
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('status-badge');
                badge.className = `badge rounded-pill bg-${data.status_badge} fs-5 px-4 py-2`;
                badge.innerText = data.status_label;

                if (['ready', 'completed', 'cancelled'].includes(data.status)) {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error polling status:', error));
    }, 10000);
    @endif

    @if($booking->status === 'cancelled')
    // Auto-show DITOLAK modal
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('rejectedModal')).show();
    });
    @endif
</script>
@endpush

@section('styles')
<style>
    .last-border-0:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endsection