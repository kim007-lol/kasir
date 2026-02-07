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
                            <i class="bi bi-check-square"></i> Pilih Beberapa Produk Sekaligus Atau Satuan
                        </h6>
                        <button type="button" class="btn btn-success fw-bold" id="addSelectedBtn">
                            <i class="bi bi-cart-plus"></i> Tambah Terpilih ke Keranjang
                        </button>
                    </div>

                    {{-- Search Box --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="searchItem" class="form-control" placeholder="Cari berdasarkan nama atau kode produk..." />
                            <button type="button" class="btn btn-outline-secondary" id="clearSearchBtn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover mb-0" id="itemsTable">
                            <thead class="table-dark sticky-top" style="top: 0;">
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Kode</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok Produk</th>
                                    <th style="width: 100px;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                @if ($item->stock > 0)
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
                                                <span class="badge {{ $stockClass }}">
                                                {{ $item->stock }}
                                                </span>
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
                                @endif
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <p class="mt-2 mb-0">Tidak ada produk tersedia</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item['qty'] }}</span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <small>Rp. {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</small>
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
                    <button type="submit" class="btn btn-lg w-100 text-white fw-bold" style="background-color: #48bb78;" id="payButton" disabled>
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

        .table-sm {
            font-size: 0.8rem;
        }

        .badge {
            font-size: 0.75rem;
        }

        small {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        h2 {
            font-size: 1.25rem;
        }

        .form-select-lg,
        .form-control-lg {
            font-size: 0.95rem;
        }

        .btn-lg {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }
    }

    .card {
        border-radius: 0.75rem;
    }
</style>

<div id="blade-data"
    data-total="{{ json_encode($total ?? 0) }}"
    data-quick-amounts="{{ json_encode([10000, 20000, 50000, 100000]) }}"
    style="display: none;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Blade
        const bladeData = document.getElementById('blade-data');
        const totalAmount = JSON.parse(bladeData.dataset.total);
        const quickAmounts = JSON.parse(bladeData.dataset.quickAmounts);

        // Element references
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const qtyInputs = document.querySelectorAll('.qty-input');
        const addSelectedBtn = document.getElementById('addSelectedBtn');
        const searchInput = document.getElementById('searchItem');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const paidAmountInput = document.getElementById('paid_amount');
        const changeDisplay = document.getElementById('changeDisplay');
        const changeAmount = document.getElementById('changeAmount');
        const payButton = document.getElementById('payButton');
        const paymentError = document.getElementById('paymentError');
        const paymentErrorText = document.getElementById('paymentErrorText');
        const quickPayButtons = document.querySelectorAll('.quick-pay-btn');
        const pasBtn = document.getElementById('pasBtn');

        // Toggle quantity input saat checkbox di klik
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const qtyInput = row.querySelector('.qty-input');
                qtyInput.disabled = !this.checked;
                if (this.checked) {
                    qtyInput.focus();
                }
            });
        });

        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach((cb, index) => {
                cb.checked = this.checked;
                qtyInputs[index].disabled = !this.checked;
            });
        });

        // Add selected to cart
        addSelectedBtn.addEventListener('click', function() {
            const selectedItems = [];
            itemCheckboxes.forEach(cb => {
                if (cb.checked) {
                    const itemId = cb.dataset.itemId;
                    const row = cb.closest('tr');
                    const qtyInput = row.querySelector('.qty-input');
                    const qty = parseInt(qtyInput.value) || 1;
                    const stock = parseInt(cb.dataset.stock);

                    if (qty > 0 && qty <= stock) {
                        selectedItems.push({
                            item_id: itemId,
                            qty: qty
                        });
                    } else {
                        alert('Jumlah untuk item ini tidak valid! Maks: ' + stock);
                        return;
                    }
                }
            });

            if (selectedItems.length === 0) {
                alert('Pilih minimal satu item!');
                return;
            }

            // Submit form dengan data item
            const form = document.getElementById('multiCartForm');

            // Hapus input items sebelumnya jika ada
            const existingItems = form.querySelectorAll('input[name^="items["]');
            existingItems.forEach(input => input.remove());

            // Hapus selected_items
            const selectedItemsInputs = form.querySelectorAll('input[name="selected_items[]"]');
            selectedItemsInputs.forEach(input => input.remove());

            // Buat input hidden untuk setiap item
            selectedItems.forEach((item, index) => {
                const itemIdInput = document.createElement('input');
                itemIdInput.type = 'hidden';
                itemIdInput.name = `items[${index}][item_id]`;
                itemIdInput.value = item.item_id;
                form.appendChild(itemIdInput);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `items[${index}][qty]`;
                qtyInput.value = item.qty;
                form.appendChild(qtyInput);
            });

            form.submit();
        });

        // Search functionality
        searchInput.addEventListener('input', filterItems);
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterItems();
        });

        function filterItems() {
            const filter = searchInput.value.toLowerCase();
            const table = document.getElementById('itemsTable');
            const rows = table.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const codeCell = row.cells[1];
                const nameCell = row.cells[2];

                if (codeCell && nameCell) {
                    const codeText = codeCell.textContent || codeCell.innerText;
                    const nameText = nameCell.textContent || nameCell.innerText;

                    if (codeText.toLowerCase().indexOf(filter) > -1 ||
                        nameText.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            }

            // Tampilkan pesan jika tidak ada hasil
            let emptyRow = table.querySelector('.no-results-row');
            if (visibleCount === 0) {
                if (!emptyRow) {
                    emptyRow = table.insertRow();
                    emptyRow.className = 'no-results-row';
                    emptyRow.innerHTML = '<td colspan="6" class="text-center text-muted py-4"><i class="bi bi-search" style="font-size: 2rem; opacity: 0.3;"></i><p class="mt-2 mb-0">Produk tidak ditemukan</p></td>';
                }
                emptyRow.style.display = '';
            } else if (emptyRow) {
                emptyRow.style.display = 'none';
            }
        }

        // Payment calculation
        paidAmountInput.addEventListener('input', calculateChange);

        function calculateChange() {
            const paidAmount = parseFloat(paidAmountInput.value) || 0;
            const change = paidAmount - totalAmount;

            if (paidAmount > 0) {
                if (paidAmount < totalAmount) {
                    changeDisplay.style.display = 'none';
                    paymentError.style.display = 'block';
                    const kurang = totalAmount - paidAmount;
                    paymentErrorText.textContent = 'Kurang: Rp ' + formatNumber(kurang);
                    payButton.disabled = true;
                } else {
                    paymentError.style.display = 'none';
                    changeDisplay.style.display = 'block';
                    changeAmount.textContent = 'Rp ' + formatNumber(change);
                    payButton.disabled = false;
                }
            } else {
                changeDisplay.style.display = 'none';
                paymentError.style.display = 'none';
                payButton.disabled = true;
            }
        }

        // Quick payment buttons
        quickPayButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const amount = parseInt(this.dataset.amount);
                paidAmountInput.value = amount;
                calculateChange();
            });
        });

        // Pas button
        pasBtn.addEventListener('click', function() {
            const pasAmount = Math.ceil(totalAmount / 1000) * 1000;
            paidAmountInput.value = pasAmount;
            calculateChange();
        });

        // Format number function
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    });
</script>
@endsection