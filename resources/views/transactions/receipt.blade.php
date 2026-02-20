@extends(auth()->check() && auth()->user()->role === 'kasir' ? 'layouts.cashier' : 'layouts.app')

@section('title', 'Struk Transaksi')

@section('content')
<style>
    /* General Styles for Receipt */
    .receipt-container {
        font-family: 'Courier New', Courier, monospace;
        width: 100%;
        max-width: 350px;
        /* Standard thermal width approx 80mm */
        margin: 0 auto;
        background: white;
        padding: 15px;
        color: #000;
        font-size: 12px;
    }

    .shop-name {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }

    .shop-address,
    .shop-phone {
        font-size: 10px;
        text-align: center;
        margin-bottom: 5px;
    }

    .receipt-title {
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        margin: 10px 0;
        text-transform: uppercase;
    }

    .dashed-line {
        border-top: 1px dashed #000;
        margin: 5px 0;
        display: block;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .item-row {
        margin-bottom: 5px;
    }

    .item-name {
        font-weight: bold;
        display: block;
    }

    .item-details {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-top: 2px;
    }

    .big-total {
        font-size: 18px;
        font-weight: bold;
        text-align: right;
        margin-top: 5px;
    }

    .footer {
        text-align: center;
        margin-top: 15px;
        font-size: 10px;
    }

    .barcode-box {
        height: 40px;
        width: 90%;
        margin: 10px auto;
        background: repeating-linear-gradient(90deg,
                #000 0px,
                #000 2px,
                transparent 2px,
                transparent 5px,
                #000 5px,
                #000 7px,
                transparent 7px,
                transparent 9px);
    }

    .action-buttons {
        margin-top: 20px;
        text-align: center;
    }

    /* Print Specific Styles */
    @media print {
        body {
            background: white !important;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 0;
            size: auto;
            /* Allow printer to determine size, or use 80mm if needed */
        }

        .receipt-container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            border: none;
            box-shadow: none;
        }

        /* Hide everything else */
        .sidebar,
        .navbar,
        .btn,
        .action-buttons,
        .alert,
        footer,
        header {
            display: none !important;
        }

        /* Ensure texts are black */
        * {
            color: #000 !important;
        }
    }

    .text-green {
        color: green !important;
        font-weight: bold;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="receipt-container" id="receiptCard" data-print="{{ session()->has('success') ? 'true' : 'false' }}">

            {{-- Header --}}
            <div class="shop-name">SMEGABIZ</div>
            <div class="shop-address">Surabaya</div>

            <div class="receipt-title">STRUK PEMBELIAN</div>
            <div class="text-center" style="font-size: 10px; margin-bottom: 5px;">SMEGABIZ</div>

            <div class="dashed-line"></div>

            {{-- Info Transaksi --}}
            <div class="info-row">
                <span>No. Trans:</span>
                <span>{{ $lastTransaction->invoice }}</span>
            </div>
            <div class="info-row">
                <span>Tgl:</span>
                <span>{{ $lastTransaction->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</span>
            </div>
            <div class="info-row">
                <span>Customer:</span>
                <span>{{ $lastTransaction->customer_name }}</span>
            </div>
            <div class="info-row">
                <span>Kasir:</span>
                <span>{{ $lastTransaction->cashier_name ?? ($lastTransaction->user->name ?? 'System') }}</span>
            </div>

            <div class="dashed-line"></div>

            {{-- Item List --}}
            @foreach ($details as $detail)
            <div class="item-row">
                <span class="item-name">{{ $detail->item->name }}</span>
                <div class="item-details">
                    <span>
                        {{ $detail->qty }} x {{ number_format($detail->price, 0, ',', '.') }}
                        @if($detail->discount > 0)
                        <small class="text-muted" style="text-decoration: line-through; font-size: 0.8em; opacity: 0.8;">
                            ({{ number_format($detail->original_price, 0, ',', '.') }})
                        </small>
                        @endif
                    </span>
                    <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($detail->discount > 0)
                <div class="item-details">
                    <span style="font-style: italic;">(Disc)</span>
                    <span>-{{ number_format($detail->discount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
            @endforeach

            <div class="dashed-line"></div>

            {{-- Totals --}}
            <div class="total-row">
                <span>Total Belanja</span>
                <span>{{ number_format($lastTransaction->total + ($lastTransaction->discount_amount ?? 0), 0, ',', '.') }}</span>
            </div>

            @if(($lastTransaction->discount_amount ?? 0) > 0)
            <div class="total-row">
                <span>Potongan Struk</span>
                <span>-{{ number_format($lastTransaction->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="total-row">
                <span>Metode Bayar</span>
                <span>{{ strtoupper($lastTransaction->payment_method) }}</span>
            </div>
            <div class="total-row">
                <span>Tunai</span>
                <span>{{ number_format($lastTransaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row" style="font-weight: bold; color: #000;">
                <span class="{{ $lastTransaction->change_amount > 0 ? 'text-green' : '' }}">Kembalian</span>
                <span class="{{ $lastTransaction->change_amount > 0 ? 'text-green' : '' }}">{{ number_format($lastTransaction->change_amount, 0, ',', '.') }}</span>
            </div>

            <div class="dashed-line"></div>
            <div class="dashed-line"></div>

            {{-- Big Grand Total --}}
            <div class="text-end">
                <small style="font-size: 10px;">{{ strtoupper($lastTransaction->payment_method) }}</small>
                <div class="big-total">Rp {{ number_format($lastTransaction->total, 0, ',', '.') }}</div>
            </div>

            {{-- Barcode --}}
            <div class="barcode-box"></div>
            <div class="text-center" style="font-size: 10px;">{{ $lastTransaction->invoice }}</div>

            <div class="dashed-line" style="margin-top: 10px;"></div>

            {{-- Footer --}}
            <div class="footer">
                <div>*** TERIMA KASIH ***</div>
                <div style="margin-top: 5px;">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm:ss') }}</div>
            </div>

            {{-- Buttons (Screen Only) --}}
            <div class="action-buttons no-print">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer"></i> Print
                </button>
                @php
                $thermalRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.transactions.thermalReceipt' : 'transactions.thermalReceipt';
                $transactionRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.transactions.index' : 'transactions.index';
                @endphp
                <a href="{{ route($thermalRoute, $lastTransaction->id) }}" target="_blank" class="btn btn-success btn-sm">
                    <i class="bi bi-receipt"></i> Cetak Thermal
                </a>
                <a href="{{ route($transactionRoute) }}" class="btn btn-secondary btn-sm" id="backBtn">
                    <i class="bi bi-arrow-left"></i> Kembali / Transaksi Baru
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    window.onload = function() {
        // Customer Display Integration
        const cfdChannel = new BroadcastChannel('kasirku-customer-display');
        
        // Show persistent "Thank You" when receipt is loaded
        cfdChannel.postMessage({ type: 'show-thank-you' });

        // Reset display when "Kembali" is clicked
        const backBtn = document.getElementById('backBtn');
        if (backBtn) {
            backBtn.addEventListener('click', function() {
                cfdChannel.postMessage({ type: 'reset-display' });
            });
        }

        // Auto-print logic
        const receiptCard = document.getElementById('receiptCard');
        const hasSuccess = receiptCard ? receiptCard.dataset.print === 'true' : false;

        if (hasSuccess) {
            setTimeout(() => {
                window.print();
            }, 800);
        }
    }
</script>
@endsection