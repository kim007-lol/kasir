@extends(auth()->check() && auth()->user()->role === 'kasir' ? 'layouts.cashier' : 'layouts.app')

@section('title', 'Transaksi')

@php
// Determine route prefix based on user role
$routePrefix = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.' : '';
@endphp

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
                {{-- Form Start Moved Down --}}
                <div id="multiCartFormContainer">

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
                            <input type="text" id="searchItem" name="search" class="form-control" placeholder="Scan Barcode atau Cari Produk..." value="{{ $search ?? '' }}" autofocus />
                            <button type="button" class="btn btn-primary" id="searchBtn">Cari</button>
                            @if(request('search'))
                            <a href="{{ route($routePrefix . 'transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route($routePrefix . 'transactions.addMultipleToCart') }}" method="POST" id="multiCartForm" onsubmit="return false;">
                        @csrf
                        <div id="product-list">
                            @fragment('product-list')
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
                                                        <span class="badge {{ $stockClass }}" id="stock-badge-{{ $item->id }}">{{ $item->stock }}</span>
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
                            @endfragment
                        </div>
                    </form>
                </div>
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
                                <br><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">-Rp {{ number_format($item['discount'], 0, ',', '.') }}</span>
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
                                <form action="{{ route($routePrefix . 'transactions.removeFromCart', $itemId) }}" method="POST" style="display:inline;">
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
                <form action="{{ route($routePrefix . 'transactions.checkout') }}" method="POST" id="paymentForm" onsubmit="return handleCheckoutSubmit(this);">
                    @csrf
                    <input type="hidden" name="_checkout_token" value="{{ \Illuminate\Support\Str::uuid() }}">

                    {{-- Cashier Selection --}}
                    <div class="mb-2">
                        <label for="cashier_name" class="form-label small fw-bold text-primary">Nama Kasir (Yang Melayani)</label>
                        <input type="text" name="cashier_name" id="cashier_name" class="form-control form-control-sm" required placeholder="Ketik nama kasir..." value="">
                    </div>

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

                    {{-- Global Discount (Potongan Rp) --}}
                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <div class="mb-2">
                        <label for="discount_amount" class="form-label small fw-bold">Potongan (Rp)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">Rp</span>
                            <input
                                type="number"
                                name="discount_amount"
                                id="discount_amount"
                                class="form-control"
                                placeholder="0"
                                min="0" />
                        </div>
                    </div>
                    @endif

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
    // M3 Fix: Prevent double-submit on checkout
    function handleCheckoutSubmit(form) {
        const btn = form.querySelector('#payButton');
        if (btn.dataset.submitting === 'true') return false;
        btn.dataset.submitting = 'true';
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const bladeData = document.getElementById('blade-data');
        const totalAmount = JSON.parse(bladeData.dataset.total);

        // --- GLOBAL STATE FOR SELECTION (Fixes "Search gabisa di choose") ---
        // Map<itemId, {qty: number, stock: number}>
        const selectedItems = new Map();

        function updateSelectionState(itemId, isSelected, qty, stock) {
            if (isSelected) {
                selectedItems.set(String(itemId), {
                    qty: parseInt(qty) || 1,
                    stock: parseInt(stock) || 0
                });
            } else {
                selectedItems.delete(String(itemId));
            }
            updateSelectAllState();
        }

        function restoreSelectionState() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            let allChecked = true;
            let hasVisibleItems = checkboxes.length > 0;

            checkboxes.forEach(cb => {
                const itemId = String(cb.dataset.itemId);
                const row = cb.closest('tr');
                const qtyInput = row.querySelector('.qty-input');

                if (selectedItems.has(itemId)) {
                    cb.checked = true;
                    if (qtyInput) {
                        qtyInput.disabled = false;
                        qtyInput.value = selectedItems.get(itemId).qty;
                    }
                } else {
                    cb.checked = false;
                    allChecked = false;
                    if (qtyInput) qtyInput.disabled = true;
                }
            });

            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = hasVisibleItems && allChecked;
            }
        }

        function updateSelectAllState() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            if (checkboxes.length === 0) return;

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const selectAll = document.getElementById('selectAll');
            if (selectAll) selectAll.checked = allChecked;
        }

        // --- Event Delegation for Dynamic Elements ---
        const productList = document.getElementById('product-list');

        // Handle "Select All" click
        document.body.addEventListener('change', function(e) {
            if (e.target.id === 'selectAll') {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                const isChecked = e.target.checked;

                checkboxes.forEach(cb => {
                    cb.checked = isChecked;
                    const row = cb.closest('tr');
                    const qtyInput = row.querySelector('.qty-input');
                    const itemId = cb.dataset.itemId;
                    const stock = cb.dataset.stock;

                    if (qtyInput) {
                        qtyInput.disabled = !isChecked;
                        // Update global state
                        updateSelectionState(itemId, isChecked, qtyInput.value, stock);
                    }
                });
            }
        });

        // Handle individual item checkbox
        document.body.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-checkbox')) {
                const row = e.target.closest('tr');
                const qtyInput = row.querySelector('.qty-input');
                const itemId = e.target.dataset.itemId;
                const stock = e.target.dataset.stock;

                if (qtyInput) {
                    qtyInput.disabled = !e.target.checked;
                    if (e.target.checked) qtyInput.focus();

                    // Update global state
                    updateSelectionState(itemId, e.target.checked, qtyInput.value, stock);
                }
            }
        });

        // Handle Quantity Input Change
        document.body.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input')) {
                const row = e.target.closest('tr');
                const checkbox = row.querySelector('.item-checkbox');
                if (checkbox && checkbox.checked) {
                    const itemId = checkbox.dataset.itemId;
                    const stock = checkbox.dataset.stock;
                    updateSelectionState(itemId, true, e.target.value, stock);
                }
            }
        });

        // --- Search ---
        const searchInput = document.getElementById('searchItem');
        const searchBtn = document.getElementById('searchBtn');
        let currentController = null; // For aborting previous fetches

        function performSearch() {
            const query = searchInput.value.trim();
            // Allow empty query to reset

            if (currentController) currentController.abort();
            currentController = new AbortController();

            const url = new URL("{{ route($routePrefix . 'transactions.index') }}");
            if (query) url.searchParams.set('search', query);

            if (productList) {
                productList.style.opacity = '0.5';
                // Don't disable pointer events strictly, allow typing
            }

            fetch(url, {
                    signal: currentController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async res => {
                    const text = await res.text();

                    // Try parsing JSON first, regardless of Content-Type
                    try {
                        const data = JSON.parse(text);
                        if (data.auto_added) {
                            searchInput.value = '';
                            window.location.href = data.redirect_url;
                            return;
                        }
                    } catch (e) {
                        // Regex fallback for JSON (if headers are present in body)
                        const jsonMatch = text.match(/\{.*"auto_added":true.*\}/);
                        if (jsonMatch) {
                            try {
                                const data = JSON.parse(jsonMatch[0]);
                                if (data.auto_added) {
                                    searchInput.value = '';
                                    window.location.href = data.redirect_url;
                                    return;
                                }
                            } catch (err) {}
                        }
                    }

                    if (productList) {
                        productList.innerHTML = text;
                        // IMPORTANT: Restore selection state after DOM update
                        restoreSelectionState();
                    }
                })
                .catch(err => {
                    if (err.name !== 'AbortError') console.error(err);
                })
                .finally(() => {
                    if (productList) productList.style.opacity = '1';
                });
        }

        if (searchBtn) searchBtn.addEventListener('click', performSearch);
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                // Debounce or just search on enter? Original used Enter.
                // Let's stick to Enter for specific matches or scan, but user might expect type-to-search.
                // Keeping original behavior: keypress Enter.
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });
        }

        // Ensure search input stays focused for continuous scanning
        if (searchInput) {
            searchInput.focus();
            document.addEventListener('click', function(e) {
                const isFormElement = ['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON'].includes(e.target.tagName);
                const isWithinPaymentForm = e.target.closest('#paymentForm');

                if (!isFormElement && !isWithinPaymentForm) {
                    searchInput.focus({
                        preventScroll: true
                    });
                }
            });
        }

        // --- Add Selected to Cart ---
        const addSelectedBtn = document.getElementById('addSelectedBtn');
        if (addSelectedBtn) {
            addSelectedBtn.addEventListener('click', function() {
                if (selectedItems.size === 0) {
                    toastr.error('Pilih minimal satu item!');
                    return;
                }

                // Create hidden inputs and submit
                const form = document.getElementById('multiCartForm');
                // Clean old hidden inputs
                form.querySelectorAll('input[type="hidden"][name^="items"]').forEach(el => el.remove());

                let index = 0;
                selectedItems.forEach((data, itemId) => {
                    const qty = parseInt(data.qty);
                    const stock = parseInt(data.stock);

                    if (qty > 0 && qty <= stock) {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = `items[${index}][item_id]`;
                        idInput.value = itemId;
                        form.appendChild(idInput);

                        const qtyInput = document.createElement('input');
                        qtyInput.type = 'hidden';
                        qtyInput.name = `items[${index}][qty]`;
                        qtyInput.value = qty;
                        form.appendChild(qtyInput);
                        index++;
                    }
                });

                if (index === 0) {
                    toastr.error('Item terpilih tidak valid (stok habis atau qty 0)');
                    return;
                }

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
                paidAmountInput.value = Math.ceil(totalAmount / 1000) * 1000;
                calculateChange();
            });
        }

        // --- Real-time Stock Polling ---
        function pollStockStatus() {
            fetch("{{ route($routePrefix . 'stock.status') }}")
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        // Badge updates...
                        const badge = document.getElementById(`stock-badge-${item.id}`);
                        if (badge) {
                            badge.textContent = item.stock;
                            badge.classList.remove('bg-success', 'bg-warning', 'bg-danger');
                            if (item.stock < 10) badge.classList.add('bg-danger');
                            else if (item.stock <= 20) badge.classList.add('bg-warning');
                            else badge.classList.add('bg-success');
                        }

                        // Update checkbox data (even if not checked, for future selection)
                        const checkbox = document.querySelector(`.item-checkbox[data-item-id="${item.id}"]`);
                        if (checkbox) {
                            checkbox.dataset.stock = item.stock;
                        }

                        // Update GLOBAL STATE if item is selected
                        if (selectedItems.has(String(item.id))) {
                            const currentData = selectedItems.get(String(item.id));
                            // Update stock in global state
                            currentData.stock = item.stock;
                            selectedItems.set(String(item.id), currentData);
                        }

                        // Update Inputs
                        const qtyInput = document.querySelector(`.qty-input[data-item-id="${item.id}"]`);
                        if (qtyInput) {
                            qtyInput.max = item.stock;
                            if (parseInt(qtyInput.value) > item.stock) {
                                qtyInput.value = item.stock;
                                // also update global
                                if (selectedItems.has(String(item.id))) {
                                    updateSelectionState(item.id, true, item.stock, item.stock);
                                }
                            }
                        }
                    });
                })
                .catch(console.error);
        }

        setInterval(pollStockStatus, 5000);
    });
</script>
@endsection