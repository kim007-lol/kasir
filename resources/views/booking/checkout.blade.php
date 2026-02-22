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
                            <label class="form-label small">Jam Ambil (Hari Ini)</label>
                            <input type="time" name="pickup_time" id="pickupTime" class="form-control"
                                min="{{ now()->addMinutes(15)->format('H:i') }}"
                                max="{{ App\Models\ShopSetting::get('close_hour', '15:00') }}">
                            <div class="form-text text-muted small">
                                Minimal 15 menit dari sekarang. Batas akhir {{ App\Models\ShopSetting::get('close_hour', '15:00') }}.
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
                        Pembayaran dilakukan di Kasir (Tunai/QRIS) saat mengambil pesanan.
                    </div>
                    
                    <div id="deliveryPaymentInfo" class="alert alert-warning small mb-0" style="display: none;">
                        <i class="bi bi-exclamation-square-fill me-1"></i>
                        Pembayaran dilakukan di tempat (COD). <strong>Mohon siapkan Uang Pas</strong> untuk mempermudah driver kami menyerahkan pesanan.
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
</script>
@endsection