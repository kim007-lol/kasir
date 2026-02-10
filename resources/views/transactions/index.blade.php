@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-8">
        <h2 class="fw-bold mb-4">
            <i class="bi bi-receipt"></i> Transaksi
        </h2>
        <div class="card shadow-sm border-0">
            <div class="card-header" style="background-color: #ff6b6b; color: white;">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam"></i> Daftar Produk
                </h5>
            </div>
            <div class="card-body">
                <hr class="my-4">
                <form action="{{ route('transactions.addMultipleToCart') }}" method="POST" id="multiCartForm">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-check-square"></i> Pilih Produk
                        </h6>
                        <button type="button" class="btn btn-success fw-bold" id="addSelectedBtn">
                            <i class="bi bi-cart-plus"></i> Tambah Terpilih
                        </button>
                    </div>

                    {{-- Search Box with Server-Side Search --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="searchItem" name="search" class="form-control" placeholder="Cari kode atau nama produk (Tekan Enter)..." value="{{ $search ?? '' }}" />
                            <button type="button" class="btn btn-primary" id="searchBtn">Cari</button>
                            @if(request('search'))
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </div>

                    <div id="product-list">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>Kode</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th style="width: 100px;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="item-checkbox" data-stock="{{ $item->stock }}" data-item-id="{{ $item->id }}">
                                        </td>
                                        <td><small>{{ $item->code }}</small></td>
                                        <td>{{ $item->name }}</td>
                                        <td>Rp. {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                            $stockClass = 'bg-success';
                                            if ($item->stock < 10) {
                                                $stockClass='bg-danger' ;
                                                } elseif ($item->stock <= 20) {
                                                    $stockClass='bg-warning' ;
                                                    }
                                                    @endphp
                                                    <span class="badge {{ $stockClass }}">{{ $item->stock }}</span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm qty-input"
                                                value="1"
                                                min="1"
                                                max="{{ $item->stock }}"
                                                data-item-id="{{ $item->id }}"
                                                disabled>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mt-2 mb-0">Tidak ada produk ditemukan</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $items->withQueryString()->links() }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <h2 class="fw-bold mb-4">
            <i class="bi bi-cart"></i> Keranjang
        </h2>
        <div class="card shadow-sm border-0 sticky-lg-top" style="top: 100px;">
            @if (empty($cart))
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-cart-x" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3">Keranjang kosong</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th class="d-none d-md-table-cell">Harga</th>
                            <th style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $itemId => $item)
                        <tr>
                            <td>
                                <small>{{ substr($item['name'], 0, 15) }}...</small>
                                @if(isset($item['discount']) && $item['discount'] > 0)
                                <br><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">-{{ $item['discount'] }}%</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item['qty'] }}</span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if(isset($item['discount']) && $item['discount'] > 0)
                                <small class="text-muted" style="text-decoration: line-through; font-size: 0.7rem;">
                                    Rp. {{ number_format($item['original_price'] * $item['qty'], 0, ',', '.') }}
                                </small><br>
                                @endif
                                <small class="fw-bold">Rp. {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</small>
                            </td>
                            <td>
                                <form action="{{ route('transactions.removeFromCart', $itemId) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                {{-- Total Section --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Total Belanja</span>
                    <h4 class="mb-0 fw-bold" style="color: #667eea;">
                        Rp. {{ number_format($total, 0, ',', '.') }}
                    </h4>
                </div>

                {{-- Payment Form --}}
                <form action="{{ route('transactions.checkout') }}" method="POST" id="paymentForm">
                    @csrf
                    <div class="mb-2">
                        <label for="member_id" class="form-label small fw-bold">Pilih Member</label>
                        <select
                            name="member_id"
                            id="member_id"
                            class="form-select form-select-sm">
                            <option value="">-- Non Member --</option>
                            @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Metode Pembayaran</label>
                        <div class="d-flex gap-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked>
                                <label class="form-check-label" for="payment_cash">
                                    <i class="bi bi-cash"></i> Cash
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_qris" value="qris">
                                <label class="form-check-label" for="payment_qris">
                                    <i class="bi bi-qr-code"></i> QRIS
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Input Uang Dibayar --}}
                    <div class="mb-2">
                        <label for="paid_amount" class="form-label small fw-bold text-success">
                            <i class="bi bi-cash"></i> Uang Dibayar
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-success text-white">Rp</span>
                            <input
                                type="number"
                                name="paid_amount"
                                id="paid_amount"
                                class="form-control"
                                placeholder="0"
                                min="0">
                        </div>
                    </div>

                    {{-- Quick Payment Buttons --}}
                    <div class="mb-3">
                        <div class="d-flex gap-1 flex-wrap" id="quickPaymentButtons">
                            @foreach([10000, 20000, 50000, 100000] as $amount)
                            <button type="button" class="btn btn-outline-success btn-sm quick-pay-btn" data-amount="{{ $amount }}">
                                {{ number_format($amount, 0, ',', '.') }}
                            </button>
                            @endforeach
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="pasBtn">
                                Pas
                            </button>
                        </div>
                    </div>

                    {{-- Kembalian Display --}}
                    <div class="alert alert-success mb-3 py-2" id="changeDisplay" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small">Kembalian:</span>
                            <span class="fw-bold fs-5" id="changeAmount">Rp 0</span>
                        </div>
                    </div>

                    {{-- Error Display --}}
                    <div class="alert alert-danger mb-3 py-2" id="paymentError" style="display: none;">
                        <span class="small" id="paymentErrorText"></span>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-success btn-lg w-100 text-white fw-bold" id="payButton" disabled>
                        <i class="bi bi-check-circle"></i> Bayar Sekarang
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .sticky-lg-top {
            position: relative !important;
            top: auto !important;
        }

        .form-select-lg,
        .form-control-lg {
            font-size: 1rem;
            padding: 0.75rem;
        }

    }

    .card {
        border-radius: 0.75rem;
    }
</style>

<div id="blade-data"
    data-total="{{ json_encode($total ?? 0) }}"
    style="display: none;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bladeData = document.getElementById('blade-data');
        const totalAmount = JSON.parse(bladeData.dataset.total);

        // --- Event Delegation for Dynamic Elements (Checkboxes & Inputs) ---
        const productList = document.getElementById('product-list');

        // Handle "Select All" click
        document.body.addEventListener('change', function(e) {
            if (e.target.id === 'selectAll') {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                const qtyInputs = document.querySelectorAll('.qty-input');
                checkboxes.forEach((cb, index) => {
                    cb.checked = e.target.checked;
                    if (qtyInputs[index]) qtyInputs[index].disabled = !e.target.checked;
                });
            }
        });

        // Handle individual item checkbox
        document.body.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-checkbox')) {
                const row = e.target.closest('tr');
                const qtyInput = row.querySelector('.qty-input');
                if (qtyInput) {
                    qtyInput.disabled = !e.target.checked;
                    if (e.target.checked) qtyInput.focus();
                }
            }
        });

        // --- Search ---
        const searchInput = document.getElementById('searchItem');
        const searchBtn = document.getElementById('searchBtn');

        function performSearch() {
            const query = searchInput.value;
            const url = new URL("{{ route('transactions.index') }}");
            url.searchParams.set('search', query);

            // Show loading
            if (productList) productList.style.opacity = '0.5';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#product-list');
                    if (newContent && productList) {
                        productList.innerHTML = newContent.innerHTML;
                    }
                })
                .finally(() => {
                    if (productList) productList.style.opacity = '1';
                });
        }

        if (searchBtn) searchBtn.addEventListener('click', performSearch);
        if (searchInput) searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });


        // --- Add Selected to Cart ---
        const addSelectedBtn = document.getElementById('addSelectedBtn');
        if (addSelectedBtn) {
            addSelectedBtn.addEventListener('click', function() {
                const selectedItems = [];
                document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                    const row = cb.closest('tr');
                    const qtyInput = row.querySelector('.qty-input');
                    const qty = parseInt(qtyInput.value) || 1;
                    const stock = parseInt(cb.dataset.stock);
                    const itemId = cb.dataset.itemId;

                    if (qty > 0 && qty <= stock) {
                        selectedItems.push({
                            item_id: itemId,
                            qty: qty
                        });
                    }
                });

                if (selectedItems.length === 0) {
                    toastr.error('Pilih minimal satu item!');
                    return;
                }

                // Create hidden inputs and submit
                const form = document.getElementById('multiCartForm');
                // Clean info
                form.querySelectorAll('input[type="hidden"][name^="items"]').forEach(el => el.remove());

                selectedItems.forEach((item, index) => {
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = `items[${index}][item_id]`;
                    idInput.value = item.item_id;
                    form.appendChild(idInput);

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `items[${index}][qty]`;
                    qtyInput.value = item.qty;
                    form.appendChild(qtyInput);
                });

                form.submit();
            });
        }

        // --- Payment Logic (Static Elements) ---
        const paidAmountInput = document.getElementById('paid_amount');
        const changeDisplay = document.getElementById('changeDisplay');
        const changeAmount = document.getElementById('changeAmount');
        const payButton = document.getElementById('payButton');
        const paymentError = document.getElementById('paymentError');
        const paymentErrorText = document.getElementById('paymentErrorText');

        function calculateChange() {
            const paidAmount = parseFloat(paidAmountInput.value) || 0;
            const change = paidAmount - totalAmount;

            if (paidAmount > 0) {
                if (paidAmount < totalAmount) {
                    changeDisplay.style.display = 'none';
                    paymentError.style.display = 'block';
                    paymentErrorText.textContent = 'Kurang: Rp ' + (totalAmount - paidAmount).toLocaleString('id-ID');
                    payButton.disabled = true;
                } else {
                    paymentError.style.display = 'none';
                    changeDisplay.style.display = 'block';
                    changeAmount.textContent = 'Rp ' + change.toLocaleString('id-ID');
                    payButton.disabled = false;
                }
            } else {
                changeDisplay.style.display = 'none';
                paymentError.style.display = 'none';
                payButton.disabled = true;
            }
        }

        if (paidAmountInput) paidAmountInput.addEventListener('input', calculateChange);

        document.querySelectorAll('.quick-pay-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                paidAmountInput.value = this.dataset.amount;
                calculateChange();
            });
        });

        const pasBtn = document.getElementById('pasBtn');
        if (pasBtn) {
            pasBtn.addEventListener('click', function() {
                paidAmountInput.value = Math.ceil(totalAmount / 1000) * 1000; // Round up to nearest 1000
                calculateChange();
            });
        }
    });
</script>
@endsection