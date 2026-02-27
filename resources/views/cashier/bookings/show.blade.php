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
                <div class="card-body d-flex gap-2">
                    @if($booking->status === 'pending')
                    <form action="{{ route('cashier.bookings.accept', $booking) }}" method="POST" class="flex-fill"
                          onsubmit="event.preventDefault(); let form = this; Swal.fire({title: 'Terima Pesanan?', text: 'Pesanan ini belum siap namun stok kasir akan otomatis ter-booking.', icon: 'info', showCancelButton: true, confirmButtonText: 'Ya, Terima', cancelButtonText: 'Batal'}).then((res) => { if(res.isConfirmed) form.submit(); });">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg"></i> Terima Pesanan
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger flex-fill w-100" data-bs-toggle="modal" data-bs-target="#rejectDetailModal">
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
                        @if($booking->delivery_type === 'delivery')
                        <button type="button" class="btn btn-info text-white w-100" data-bs-toggle="modal" data-bs-target="#readyDetailModal">
                            <i class="bi bi-send"></i> Kirim Pesanan
                        </button>
                        @else
                        <form action="{{ route('cashier.bookings.ready', $booking) }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-bell"></i> Tandai Siap Ambil
                            </button>
                        </form>
                        @endif
                    @endif

                    @if($booking->canBeCompleted())
                        @if($booking->delivery_type === 'delivery')
                        <form action="{{ route('cashier.bookings.complete', $booking) }}" method="POST" class="w-100">
                            @csrf
                            <button type="button" class="btn btn-secondary w-100"
                                onclick="let form = this.closest('form'); Swal.fire({title: 'Selesaikan Pengiriman?', text: 'Pastikan kurir sudah kembali dan menyetor uang.', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Selesai!', cancelButtonText: 'Batal'}).then((res) => { if(res.isConfirmed) form.submit(); });">
                                <i class="bi bi-bag-check"></i> Pesanan Diantar/Selesai
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#completeDetailModal">
                            <i class="bi bi-check2-all"></i> Selesaikan & Bayar
                        </button>
                        @endif
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

{{-- Ready Modal (Delivery Only) --}}
@if($booking->status === 'processing' && $booking->delivery_type === 'delivery')
<div class="modal fade" id="readyDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cashier.bookings.ready', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Pesanan {{ $booking->booking_code }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Total: <strong style="font-size: 1.2rem; color: var(--primary-dark);">Rp <span id="paymentTotal">{{ number_format($booking->total, 0, ',', '') }}</span></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pengantar Pesanan *</label>
                        <input type="text" name="assignee_name" class="form-control" 
                            placeholder="Masukkan nama kurir pengantar" 
                            value="{{ auth()->user()->name }}"
                            required autocomplete="off">
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

                    <div class="alert alert-info py-2 mt-3 mb-0" style="font-size: 0.85rem;">
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
<div class="modal fade" id="completeDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cashier.bookings.complete', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Pembayaran {{ $booking->booking_code }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Total: <strong style="font-size: 1.2rem; color: var(--primary-dark);">Rp <span id="paymentTotal">{{ number_format($booking->total, 0, ',', '') }}</span></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kasir / Yang Melayani *</label>
                        <input type="text" name="assignee_name" class="form-control" 
                            placeholder="Masukkan nama kasir" 
                            value="{{ auth()->user()->name }}"
                            required autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" value="cash" checked onchange="toggleCashInput(true)">
                                <label class="form-check-label"><i class="bi bi-cash"></i> Cash</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" value="qris" onchange="toggleCashInput(false)">
                                <label class="form-check-label"><i class="bi bi-qr-code"></i> QRIS</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="cashInputContainer">
                        <label class="form-label fw-semibold">Uang Tunai (Cash) *</label>
                        <input type="number" name="paid_amount" id="paid_amount" class="form-control fw-bold" 
                            placeholder="0" value="{{ $booking->total }}" min="{{ $booking->total }}" required oninput="calculateChange()">
                        <div class="mt-2 fw-semibold" id="changeAmountText" style="color: green;">
                            Kembalian: Pas
                        </div>
                    </div>

                    <div class="alert alert-info py-2 mt-3 mb-0" style="font-size: 0.85rem;">
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

@endsection

@push('scripts')
<script>
    function toggleCashInput(isCash) {
        const container = document.getElementById('cashInputContainer');
        const input = document.getElementById('paid_amount');
        if (isCash) {
            container.style.display = 'block';
            input.setAttribute('required', 'required');
            calculateChange();
        } else {
            container.style.display = 'none';
            input.removeAttribute('required');
        }
    }

    function calculateChange() {
        const total = {{ $booking->total }};
        const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
        const changeText = document.getElementById('changeAmountText');
        
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