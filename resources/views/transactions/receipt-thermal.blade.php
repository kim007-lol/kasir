<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Thermal - {{ $transaction->invoice }}</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #fff;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
        }

        .shop-name {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .shop-address {
            font-size: 10px;
            text-align: center;
            margin-bottom: 3px;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin: 6px 0;
            text-transform: uppercase;
        }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            font-size: 11px;
        }

        .item-row {
            margin-bottom: 4px;
        }

        .item-name {
            font-weight: bold;
            display: block;
            font-size: 11px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 1px;
            font-size: 11px;
        }

        .big-total {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }

        .barcode-box {
            height: 30px;
            width: 85%;
            margin: 6px auto;
            background: repeating-linear-gradient(90deg,
                    #000 0px, #000 2px,
                    transparent 2px, transparent 4px,
                    #000 4px, #000 6px,
                    transparent 6px, transparent 8px);
        }

        /* Print Optimization */
        @media print {
            body {
                width: 100%;
                padding: 0;
                margin: 0;
            }

            @page {
                margin: 0;
                size: 80mm auto;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Screen-only: loading indicator */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
            font-family: sans-serif;
            font-size: 14px;
            color: #555;
        }

        .loading-overlay .spinner {
            border: 3px solid #eee;
            border-top: 3px solid #ff6b6b;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 0.8s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    {{-- Loading overlay (disappears on print) --}}
    <div class="loading-overlay no-print" id="loadingOverlay">
        <div class="spinner"></div>
        <span>Mempersiapkan cetak...</span>
    </div>

    {{-- Header --}}
    <div class="shop-name">SMEGABIZ</div>
    <div class="shop-address">Surabaya</div>

    <div class="receipt-title">STRUK PEMBELIAN</div>

    <div class="dashed-line"></div>

    {{-- Info Transaksi --}}
    <div class="info-row">
        <span>No. Trans:</span>
        <span>{{ $transaction->invoice }}</span>
    </div>
    <div class="info-row">
        <span>Tgl:</span>
        <span>{{ $transaction->created_at->isoFormat('DD/MM/YY HH:mm') }}</span>
    </div>
    <div class="info-row">
        <span>Customer:</span>
        <span>{{ $transaction->customer_name }}</span>
    </div>
    <div class="info-row">
        <span>Kasir:</span>
        <span>{{ $transaction->cashier_name ?? $transaction->user->name ?? 'Admin' }}</span>
    </div>

    <div class="dashed-line"></div>

    {{-- Item List --}}
    @foreach ($details as $detail)
    <div class="item-row">
        <span class="item-name">{{ $detail->item->name }}</span>
        <div class="item-details">
            <span>{{ $detail->qty }} x {{ number_format($detail->price, 0, ',', '.') }}</span>
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
        <span>{{ number_format($transaction->total + ($transaction->discount_amount ?? 0), 0, ',', '.') }}</span>
    </div>

    @if(($transaction->discount_amount ?? 0) > 0)
    <div class="total-row">
        <span>Potongan</span>
        <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
    </div>
    @endif

    <div class="total-row">
        <span>{{ strtoupper($transaction->payment_method) }}</span>
        <span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
    </div>
    <div class="total-row" style="font-weight: bold;">
        <span>Kembalian</span>
        <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>

    <div class="dashed-line"></div>

    {{-- Grand Total --}}
    <div style="text-align: right;">
        <small style="font-size: 9px;">{{ strtoupper($transaction->payment_method) }}</small>
        <div class="big-total">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
    </div>

    {{-- Barcode --}}
    <div class="barcode-box"></div>
    <div style="text-align: center; font-size: 9px;">{{ $transaction->invoice }}</div>

    <div class="dashed-line" style="margin-top: 6px;"></div>

    {{-- Footer --}}
    <div class="footer">
        <div>*** TERIMA KASIH ***</div>
        <div style="margin-top: 3px;">{{ $transaction->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</div>
    </div>

    <script>
        window.addEventListener('load', function () {
            // Hide loading overlay
            var overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'none';

            // Auto-print after slight delay
            setTimeout(function () {
                window.print();
            }, 300);

            // Close tab after print dialog (both complete and cancel)
            window.addEventListener('afterprint', function () {
                setTimeout(function () {
                    window.close();
                }, 500);
            });
        });
    </script>
</body>

</html>
