@extends('layouts.booking')

@section('title', 'Riwayat Pesanan — SmeGo')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h4 class="mb-4 fw-bold">Riwayat Pesanan</h4>

        @if($bookings->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Belum ada riwayat pesanan</h5>
                <p class="text-muted">Semua pesanan Anda akan muncul di sini.</p>
                <a href="{{ route('booking.menu') }}" class="btn btn-primary mt-2">Pesan Sekarang</a>
            </div>
        </div>
        @else
        <div class="list-group shadow-sm">
            @foreach($bookings as $booking)
            <a href="{{ route('booking.status', $booking->id) }}" class="list-group-item list-group-item-action p-3 border-0 border-bottom">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="mb-0 fw-bold">{{ $booking->booking_code }}</h6>
                            <span class="badge bg-{{ $booking->status_badge }} rounded-pill" style="font-size: 0.7rem;">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i> {{ $booking->created_at->format('d M Y, H:i') }}
                        </small>
                        @if($booking->status === 'cancelled' && $booking->cancel_reason)
                        <br><small class="text-danger"><i class="bi bi-x-circle me-1"></i>{{ $booking->cancel_reason }}</small>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="fw-bold text-dark d-block">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                        <small class="text-primary">Lihat Detail <i class="bi bi-chevron-right"></i></small>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection