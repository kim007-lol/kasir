@extends('layouts.booking')

@section('title', 'Keranjang — SmeGo')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h4 class="mb-4 fw-bold">Keranjang Belanja</h4>

        @if(session('error'))
        <div class="alert alert-danger mb-3">
            {{ session('error') }}
        </div>
        @endif

        @if(empty($cart))
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Keranjang kosong</h5>
                <p class="text-muted">Yuk, pesan makanan dulu!</p>
                <a href="{{ route('booking.menu') }}" class="btn btn-primary mt-2">Lihat Menu</a>
            </div>
        </div>
        @else
        <form id="cart-form" action="{{ route('booking.cart.update') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($cart as $index => $item)
                        <div class="list-group-item p-3 cart-item" data-index="{{ $index }}" data-price="{{ $item['price'] }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $item['name'] }}</h6>
                                    <div class="text-muted small">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                </div>
                                <div class="text-end fw-bold item-subtotal">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="row g-2 align-items-center mt-2">
                                <div class="col-5 col-sm-3">
                                    <input type="number" name="cart[{{ $index }}][qty]"
                                        value="{{ $item['qty'] }}" min="1" max="{{ $item['max_stock'] }}"
                                        class="form-control form-control-sm text-center cart-qty-input">
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" name="cart[{{ $index }}][notes]"
                                        value="{{ $item['notes'] ?? '' }}"
                                        placeholder="Catatan (opsional)..."
                                        class="form-control form-control-sm cart-notes-input">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-success save-indicator" style="display:none;">
                                    <i class="bi bi-check-circle-fill"></i> Tersimpan
                                </small>
                                <button type="button" class="btn btn-link text-danger btn-sm text-decoration-none p-0 ms-auto"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $index }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-4">
                                        <h6 class="mb-3">Hapus {{ $item['name'] }}?</h6>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <a href="#" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $index }}').submit();"
                                                class="btn btn-danger btn-sm">Ya, Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total Pembayaran</span>
                        <h4 class="fw-bold mb-0 text-primary" id="cart-total">Rp {{ number_format($total, 0, ',', '.') }}</h4>
                    </div>
                    <div class="d-grid gap-2">
                        @if($isOpen)
                        <a href="{{ route('booking.checkout') }}" class="btn btn-primary btn-lg">
                            Lanjut Checkout <i class="bi bi-arrow-right"></i>
                        </a>
                        @else
                        <div class="alert alert-warning text-center mb-0">
                            <i class="bi bi-clock"></i> Toko Tutup (07:00 - 15:00)
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        {{-- Separate forms for deletion to avoid nesting issues --}}
        @foreach($cart as $index => $item)
        <form id="delete-form-{{ $index }}" action="{{ route('booking.cart.remove', $index) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
        @endforeach

        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer = null;

    // Format number to Rp
    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Recalculate subtotals and total client-side
    function recalculate() {
        let grandTotal = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const price = parseInt(item.dataset.price);
            const qtyInput = item.querySelector('.cart-qty-input');
            const qty = parseInt(qtyInput.value) || 1;
            const subtotal = price * qty;
            grandTotal += subtotal;
            item.querySelector('.item-subtotal').textContent = formatRupiah(subtotal);
        });
        document.getElementById('cart-total').textContent = formatRupiah(grandTotal);
    }

    // Auto-save cart via AJAX
    function autoSaveCart(triggerElement) {
        const form = document.getElementById('cart-form');
        const formData = new FormData(form);

        // Show saving indicator on the changed item
        const cartItem = triggerElement.closest('.cart-item');
        const indicator = cartItem ? cartItem.querySelector('.save-indicator') : null;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (response.ok && indicator) {
                indicator.style.display = '';
                setTimeout(() => { indicator.style.display = 'none'; }, 1500);
            }
        })
        .catch(err => console.log('Auto-save error:', err));
    }

    // Listen to qty changes
    document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.addEventListener('input', function() {
            // Enforce min/max
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            let val = parseInt(this.value) || min;
            if (val < min) val = min;
            if (val > max) val = max;
            this.value = val;

            // Instant visual update
            recalculate();

            // Debounced auto-save (500ms)
            clearTimeout(debounceTimer);
            const self = this;
            debounceTimer = setTimeout(() => autoSaveCart(self), 500);
        });
    });

    // Listen to notes changes
    document.querySelectorAll('.cart-notes-input').forEach(input => {
        input.addEventListener('change', function() {
            autoSaveCart(this);
        });
    });
});
</script>
@endpush