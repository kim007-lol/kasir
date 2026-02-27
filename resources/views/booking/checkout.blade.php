@extends('layouts.booking')

@section('title', 'Checkout — SmeGo')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h4 class="mb-4 fw-bold">Konfirmasi Pesanan</h4>

        <!-- User Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-3"><i class="bi bi-person-circle"></i> Data Pemesan</h6>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted small">Nama</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-muted small">Email</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-3"><i class="bi bi-bag-check"></i> Ringkasan Pesanan</h6>
                <ul class="list-group list-group-flush">
                    @foreach($cart as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <span class="fw-medium">{{ $item['name'] }}</span>
                            <small class="text-muted d-block">x{{ $item['qty'] }} @ Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                            @if(!empty($item['notes']))
                            <small class="text-info d-block fst-italic"><i class="bi bi-pencil-fill" style="font-size: 0.7rem;"></i> {{ $item['notes'] }}</small>
                            @endif
                        </div>
                        <span class="fw-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-light mt-2 p-2 rounded">
                        <span class="fw-bold">TOTAL</span>
                        <span class="fw-bold text-danger fs-5">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Payment & Action -->
    <div class="col-md-4">
        <form action="{{ route('booking.placeOrder') }}" method="POST">
            @csrf

            <!-- Delivery Type -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3"><i class="bi bi-truck"></i> Metode Pengambilan</h6>

                    <div class="d-flex gap-2 mb-3">
                        <div class="form-check flex-fill">
                            <input class="form-check-input" type="radio" name="delivery_type" id="typePickup"
                                   value="pickup" checked onchange="toggleDeliveryType()">
                            <label class="form-check-label fw-medium" for="typePickup">
                                <i class="bi bi-shop"></i> Ambil di Toko
                            </label>
                        </div>
                        <div class="form-check flex-fill">
                            <input class="form-check-input" type="radio" name="delivery_type" id="typeDelivery"
                                   value="delivery" onchange="toggleDeliveryType()">
                            <label class="form-check-label fw-medium" for="typeDelivery">
                                <i class="bi bi-bicycle"></i> Pesan Antar
                            </label>
                        </div>
                    </div>

                    <!-- Pickup Time (shown for pickup) -->
                    <div id="pickupSection">
                        <div class="mb-3">
                            <label class="form-label small">Jam Ambil (Hari Ini) <span class="text-danger">*</span></label>
                            <input type="time" name="pickup_time" id="pickupTime" class="form-control"
                                min="{{ now()->addMinutes(15)->format('H:i') }}"
                                max="{{ App\Models\ShopSetting::get('close_hour', '15:00') }}" required>
                            <div class="form-text text-muted small mb-2">
                                Minimal 15 menit dari sekarang. Batas akhir {{ App\Models\ShopSetting::get('close_hour', '15:00') }}.
                            </div>
                            <div class="alert alert-warning small py-2 mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <strong>Penting:</strong> Harap ambil tepat waktu. Sistem akan membatalkan pesanan secara otomatis jika melewati batas waktu untuk menjaga kualitas makanan.
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address (shown for delivery) -->
                    <div id="deliverySection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label small">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="delivery_address" id="deliveryAddress" class="form-control" rows="3"
                                      placeholder="Contoh: Jl. Merdeka No. 10, RT 02/RW 03, Kel. Sukamaju..."></textarea>
                            <div class="form-text text-muted small">
                                Tuliskan alamat lengkap untuk pengiriman.
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger small py-2 mb-3">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Tolong dibungkus terpisah...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3"><i class="bi bi-wallet2"></i> Pembayaran</h6>
                    <div id="pickupPaymentInfo" class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Pembayaran dilakukan langsung di Kasir (Tunai/QRIS) saat Anda mengambil pesanan.
                    </div>
                    
                    <div id="deliveryPaymentInfo" style="display: none;">
                        <div class="alert alert-primary small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Kurir kami akan membawakan <strong>struk cetak (fisik)</strong> saat mengantarkan pesanan Anda.
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Metode Pembayaran</label>
                            <div class="d-flex gap-2">
                                <div class="form-check flex-fill">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payQris" value="qris" onchange="togglePaymentMethod()">
                                    <label class="form-check-label" for="payQris">
                                        <i class="bi bi-qr-code"></i> QRIS
                                    </label>
                                </div>
                                <div class="form-check flex-fill">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash" checked onchange="togglePaymentMethod()">
                                    <label class="form-check-label" for="payCash">
                                        <i class="bi bi-cash"></i> Tunai (Cash)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="cashInputContainer" class="mb-3">
                            <label class="form-label small fw-bold">Nominal Uang Tunai yang Disiapkan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount_paid" id="amountPaid" class="form-control" 
                                    min="{{ $total }}" value="{{ $total }}" oninput="calculateChange()">
                            </div>
                            <div class="form-text text-muted small mt-1">
                                Minimal senilai total pesanan (<span class="fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>) agar kurir bisa menyiapkan kembalian yang pas.
                            </div>
                            <div id="changeInfo" class="mt-2 fw-bold text-success" style="font-size: 0.9rem;">
                                Kembalian: Pas
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 btn-lg shadow fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i> Buat Pesanan
            </button>
            <a href="{{ route('booking.cart') }}" class="btn btn-outline-secondary w-100 mt-2">
                Kembali ke Keranjang
            </a>
        </form>
    </div>
</div>

<script>
    function toggleDeliveryType() {
        const isPickup = document.getElementById('typePickup').checked;
        const pickupSection = document.getElementById('pickupSection');
        const deliverySection = document.getElementById('deliverySection');
        const pickupTime = document.getElementById('pickupTime');
        const deliveryAddress = document.getElementById('deliveryAddress');
        const pickupPaymentInfo = document.getElementById('pickupPaymentInfo');
        const deliveryPaymentInfo = document.getElementById('deliveryPaymentInfo');

        if (isPickup) {
            pickupSection.style.display = 'block';
            deliverySection.style.display = 'none';
            pickupTime.required = true;
            deliveryAddress.required = false;
            
            // Tampilan info pembayaran
            if (pickupPaymentInfo) pickupPaymentInfo.style.display = 'block';
            if (deliveryPaymentInfo) deliveryPaymentInfo.style.display = 'none';
        } else {
            pickupSection.style.display = 'none';
            deliverySection.style.display = 'block';
            pickupTime.required = false;
            deliveryAddress.required = true;
            
            // Tampilan info pembayaran
            if (pickupPaymentInfo) pickupPaymentInfo.style.display = 'none';
            if (deliveryPaymentInfo) deliveryPaymentInfo.style.display = 'block';
        }
    }

    function togglePaymentMethod() {
        const isCash = document.getElementById('payCash').checked;
        const cashContainer = document.getElementById('cashInputContainer');
        const amountPaid = document.getElementById('amountPaid');

        if (isCash) {
            cashContainer.style.display = 'block';
            amountPaid.required = true;
            calculateChange();
        } else {
            cashContainer.style.display = 'none';
            amountPaid.required = false;
        }
    }

    function calculateChange() {
        const total = {{ $total }};
        const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
        const changeInfo = document.getElementById('changeInfo');
        
        if (paid < total) {
            changeInfo.className = 'mt-2 fw-bold text-danger';
            changeInfo.innerHTML = '<i class="bi bi-exclamation-circle"></i> Uang kurang: Rp ' + new Intl.NumberFormat('id-ID').format(total - paid);
        } else if (paid === total) {
            changeInfo.className = 'mt-2 fw-bold text-success';
            changeInfo.innerHTML = '<i class="bi bi-check-circle"></i> Kembalian: Pas';
        } else {
            changeInfo.className = 'mt-2 fw-bold text-success';
            changeInfo.innerHTML = '<i class="bi bi-check-circle"></i> Kembalian: Rp ' + new Intl.NumberFormat('id-ID').format(paid - total);
        }
    }

    // Panggil saat load untuk setting default
    document.addEventListener('DOMContentLoaded', function() {
        togglePaymentMethod();
    });
</script>
@endsection