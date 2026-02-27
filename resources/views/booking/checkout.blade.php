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
            <div class="card border-0 shadow-sm mb-4 theme-card">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3"><i class="bi bi-truck text-primary"></i> Metode Pengambilan</h6>

                    <div class="delivery-selector">
                        <input type="radio" name="delivery_type" id="typePickup" value="pickup" checked onchange="toggleDeliveryType()">
                        <label for="typePickup" class="delivery-option">
                            <i class="bi bi-shop fs-4"></i>
                            <span>AMBIL SENDIRI</span>
                        </label>

                        <input type="radio" name="delivery_type" id="typeDelivery" value="delivery" onchange="toggleDeliveryType()">
                        <label for="typeDelivery" class="delivery-option">
                            <i class="bi bi-bicycle fs-4"></i>
                            <span>PESAN ANTAR</span>
                        </label>
                    </div>

                    <!-- Pickup Time -->
                    <div id="pickupSection" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jam Ambil (Hari Ini) <span class="text-danger">*</span></label>
                            <input type="time" name="pickup_time" id="pickupTime" class="form-control premium-input"
                                min="{{ now()->addMinutes(15)->format('H:i') }}"
                                max="{{ App\Models\ShopSetting::get('close_hour', '15:00') }}" required>
                            <div class="form-text text-muted x-small mt-2">
                                <i class="bi bi-info-circle"></i> Minimal 15 menit dari sekarang. Maks {{ App\Models\ShopSetting::get('close_hour', '15:00') }}.
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div id="deliverySection" class="mt-3" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="delivery_address" id="deliveryAddress" class="form-control premium-input" rows="3"
                                      placeholder="Contoh: Jl. Merdeka No. 10, RT 02/RW 03..."></textarea>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Catatan Pesanan</label>
                        <textarea name="notes" class="form-control premium-input" rows="2" placeholder="Contoh: Pedas, tanpa bawang...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="card border-0 shadow-sm mb-4 theme-card">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3"><i class="bi bi-wallet2 text-primary"></i> Pembayaran</h6>
                    
                    <div id="pickupPaymentInfo" class="alert alert-soft-info mb-0">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill pt-1"></i>
                            <span>Pembayaran dilakukan di Kasir saat mengambil pesanan (Tunai/QRIS).</span>
                        </div>
                    </div>
                    
                    <div id="deliveryPaymentInfo" style="display: none;">
                        <div class="mb-3">
                            <div class="payment-selector">
                                <input type="radio" name="payment_method" id="payCash" value="cash" checked onchange="togglePaymentMethod()">
                                <label for="payCash" class="payment-option">
                                    <i class="bi bi-cash-stack"></i> Tunai
                                </label>

                                <input type="radio" name="payment_method" id="payQris" value="qris" onchange="togglePaymentMethod()">
                                <label for="payQris" class="payment-option">
                                    <i class="bi bi-qr-code"></i> QRIS
                                </label>
                            </div>
                        </div>

                        <div id="cashInputContainer" class="mb-0">
                            <label class="form-label small fw-bold">Siapkan Uang Tunai <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0 bg-light fw-bold">Rp</span>
                                <input type="number" name="amount_paid" id="amountPaid" class="form-control premium-input text-end fw-bold" 
                                    min="{{ $total }}" value="{{ $total }}" oninput="calculateChange()">
                            </div>
                            <div id="changeInfo" class="mt-2 small fw-bold text-success animate__animated animate__fadeIn">
                                Kembalian: Pas
                            </div>
                        </div>
                        
                        <div id="qrisInfo" class="alert alert-soft-primary small mt-3 mb-0" style="display:none;">
                            <i class="bi bi-info-circle-fill"></i> Sediakan aplikasi QRIS (Gopay/OVO/Dana/Mobile Banking) saat kurir tiba.
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 btn-lg shadow-lg fw-bold checkout-btn">
                <span>Konfirmasi & Buat Pesanan</span>
                <i class="bi bi-arrow-right-circle-fill ms-2"></i>
            </button>
            <a href="{{ route('booking.cart') }}" class="btn btn-link w-100 mt-2 text-muted text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
            </a>
        </form>
    </div>
</div>

<style>
    .theme-card {
        border-radius: 1.25rem !important;
        overflow: hidden;
    }
    .premium-input {
        border-radius: 0.75rem;
        padding: 0.6rem 0.85rem;
        border: 1px solid #e9ecef;
        background: #fdfdfe;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    .premium-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(255, 0, 0, 0.05);
        background: white;
    }
    
    .delivery-selector, .payment-selector {
        display: flex;
        gap: 0.75rem;
    }
    .delivery-selector input, .payment-selector input {
        display: none;
    }
    .delivery-option, .payment-option {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem 0.5rem;
        background: white;
        border: 2px solid #f1f3f5;
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
    }
    .delivery-option span, .payment-option {
        font-size: 0.75rem;
        font-weight: 800;
        margin-top: 0.4rem;
        color: #6c757d;
    }
    .delivery-selector input:checked + .delivery-option,
    .payment-selector input:checked + .payment-option {
        border-color: var(--primary);
        background: rgba(255, 0, 0, 0.02);
    }
    .delivery-selector input:checked + .delivery-option i,
    .delivery-selector input:checked + .delivery-option span,
    .payment-selector input:checked + .payment-option i,
    .payment-selector input:checked + .payment-option {
        color: var(--primary);
    }

    .alert-soft-info {
        background: #e7f3ff;
        color: #00529b;
        border: none;
        border-radius: 0.75rem;
    }
    .alert-soft-primary {
        background: rgba(255, 0, 0, 0.05);
        color: var(--primary);
        border: none;
        border-radius: 0.75rem;
    }
    .x-small { font-size: 0.75rem; }
    
    .checkout-btn {
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    .checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255,0,0,0.3) !important;
    }
    @media (max-width: 576px) {
        .delivery-selector, .payment-selector {
            flex-direction: column;
        }
        .delivery-option, .payment-option {
            padding: 0.75rem;
        }
    }
</style>

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
        const qrisInfo = document.getElementById('qrisInfo');
        const amountPaid = document.getElementById('amountPaid');

        if (isCash) {
            cashContainer.style.display = 'block';
            if (qrisInfo) qrisInfo.style.display = 'none';
            amountPaid.required = true;
            calculateChange();
        } else {
            cashContainer.style.display = 'none';
            if (qrisInfo) qrisInfo.style.display = 'block';
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