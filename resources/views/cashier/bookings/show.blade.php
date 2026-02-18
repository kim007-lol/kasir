@extends('layouts.cashier')

@section('title', 'Detail Pesanan ' . $booking->booking_code)

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('cashier.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-receipt"></i> {{ $booking->booking_code }}
        </h2>
        <span class="badge bg-{{ $booking->status_badge }} fs-6">{{ $booking->status_label }}</span>
    </div>

    <div class="row g-4">
        <!-- Booking Detail -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-list-check"></i> Item Pesanan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        {{ $item->name }}
                                        @if($item->cashierItem)
                                        <br><small class="text-muted">Stok saat ini: {{ $item->cashierItem->stock }}</small>
                                        @else
                                        <br><small class="text-danger">Item sudah dihapus</small>
                                        @endif
                                        @if($item->notes)
                                        <br><small class="text-info"><i class="bi bi-chat-dots"></i> {{ $item->notes }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold" style="color: var(--primary-dark);">
                                        Rp {{ number_format($booking->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info & Actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person"></i> Info Pelanggan
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td class="fw-semibold">{{ $booking->customer_name }}</td>
                        </tr>
                        @if($booking->customer_phone)
                        <tr>
                            <td class="text-muted">Telepon</td>
                            <td>{{ $booking->customer_phone }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td>
                                <span class="badge {{ $booking->delivery_type == 'delivery' ? 'bg-info' : 'bg-secondary' }}">
                                    <i class="bi {{ $booking->delivery_type == 'delivery' ? 'bi-truck' : 'bi-shop' }}"></i>
                                    {{ $booking->delivery_type == 'delivery' ? 'Delivery' : 'Pickup' }}
                                </span>
                            </td>
                        </tr>
                        @if($booking->delivery_address)
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ $booking->delivery_address }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Waktu</td>
                            <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($booking->notes)
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td><em>{{ $booking->notes }}</em></td>
                        </tr>
                        @endif
                        @if($booking->cancel_reason)
                        <tr>
                            <td class="text-muted">Alasan Ditolak</td>
                            <td class="text-danger"><strong>{{ $booking->cancel_reason }}</strong></td>
                        </tr>
                        @endif
                        @if($booking->payment_method)
                        <tr>
                            <td class="text-muted">Pembayaran</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ strtoupper($booking->payment_method) }}
                                </span>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-lightning"></i> Aksi
                </div>
                <div class="card-body d-grid gap-2">
                    @if($booking->status === 'pending')
                    <form action="{{ route('cashier.bookings.accept', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"
                            onclick="return confirm('Terima pesanan ini? Stok akan dikurangi.')">
                            <i class="bi bi-check-lg"></i> Terima Pesanan
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectDetailModal">
                        <i class="bi bi-x-lg"></i> Tolak Pesanan
                    </button>
                    @endif

                    @if($booking->status === 'confirmed')
                    <form action="{{ route('cashier.bookings.process', $booking) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary w-100"><i class="bi bi-fire"></i> Mulai Proses</button>
                    </form>
                    @endif

                    @if($booking->status === 'processing')
                    <form action="{{ route('cashier.bookings.ready', $booking) }}" method="POST">
                        @csrf
                        <button class="btn btn-success w-100"><i class="bi bi-check2-all"></i> Pesanan Siap!</button>
                    </form>
                    @endif

                    @if($booking->canBeCompleted())
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#completeDetailModal">
                        <i class="bi bi-bag-check"></i> Selesaikan Pesanan
                    </button>
                    @endif

                    @if(in_array($booking->status, ['completed', 'cancelled']))
                    <div class="text-center text-muted py-2">
                        <i class="bi bi-lock"></i> Pesanan sudah {{ $booking->status === 'completed' ? 'selesai' : 'dibatalkan' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
@if($booking->canBeCancelled())
<div class="modal fade" id="rejectDetailModal" tabindex="-1">
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
                        <label class="form-label fw-semibold">Alasan Penolakan *</label>
                        <textarea name="cancel_reason" class="form-control" rows="3" required
                            placeholder="Contoh: Stok habis, item yang dipesan tidak tersedia"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-lg"></i> Tolak Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Complete Modal --}}
@if($booking->canBeCompleted())
<div class="modal fade" id="completeDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cashier.bookings.complete', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan {{ $booking->booking_code }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Total: <strong>Rp {{ number_format($booking->total, 0, ',', '.') }}</strong></p>
                    <label class="form-label fw-semibold">Metode Pembayaran *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="cash" checked>
                            <label class="form-check-label"><i class="bi bi-cash"></i> Cash</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="qris">
                            <label class="form-check-label"><i class="bi bi-qr-code"></i> QRIS</label>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 mt-3 mb-0" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle"></i> Transaksi otomatis tercatat di sistem.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-bag-check"></i> Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection