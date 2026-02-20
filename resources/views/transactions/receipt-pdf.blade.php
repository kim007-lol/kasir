<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->invoice }}</title>
    <style>
        /* General Styles for Receipt */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #f5f5f5;
            /* Light gray background for screen */
            padding: 20px;
        }

        .receipt-container {
            width: 100%;
            max-width: 350px;
            /* Standard thermal width approx 80mm */
            margin: 0 auto;
            background: white;
            padding: 15px;
            color: #000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .text-green {
            color: green;
            font-weight: bold;
        }

        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            padding: 8px 16px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            font-family: sans-serif;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
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
                margin: 0;
            }

            /* Hide everything else */
            .action-buttons {
                display: none !important;
            }

            /* Ensure texts are black */
            * {
                color: #000 !important;
            }

            .text-green {
                color: #000 !important;
                /* Force black for thermal printing */
            }
        }
    </style>
    <!-- Bootstrap Icons for buttons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="receipt-container">

        {{-- Header --}}
        <div class="shop-name">SMEGABIZ</div>
        <div class="shop-address">Surabaya</div>

        <div class="receipt-title">STRUK PEMBELIAN</div>
        <div class="text-center" style="font-size: 10px; margin-bottom: 5px; text-align: center;">SMEGABIZ</div>

        <div class="dashed-line"></div>

        {{-- Info Transaksi --}}
        <div class="info-row">
            <span>No. Trans:</span>
            <span>{{ $transaction->invoice }}</span>
        </div>
        <div class="info-row">
            <span>Tgl:</span>
            <span>{{ $transaction->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</span>
        </div>
        <div class="info-row">
            <span>Customer:</span>
            <span>{{ $transaction->customer_name }}</span>
        </div>
        <div class="info-row">
            <span>Kasir:</span>
            {{-- Priority: Manual Input Name > User Name > Admin --}}
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
            <span>Potongan Struk</span>
            <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="total-row">
            <span>Metode Bayar</span>
            <span>{{ strtoupper($transaction->payment_method) }}</span>
        </div>
        <div class="total-row">
            <span>Tunai</span>
            <span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row" style="font-weight: bold;">
            <span class="{{ $transaction->change_amount > 0 ? 'text-green' : '' }}">Kembalian</span>
            <span class="{{ $transaction->change_amount > 0 ? 'text-green' : '' }}">{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>

        <div class="dashed-line"></div>
        <div class="dashed-line"></div>

        {{-- Big Grand Total --}}
        <div style="text-align: right;">
            <small style="font-size: 10px;">{{ strtoupper($transaction->payment_method) }}</small>
            <div class="big-total">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
        </div>

        {{-- Barcode --}}
        <div class="barcode-box"></div>
        <div style="text-align: center; font-size: 10px;">{{ $transaction->invoice }}</div>

        <div class="dashed-line" style="margin-top: 10px;"></div>

        {{-- Footer --}}
        <div class="footer">
            <div>*** TERIMA KASIH ***</div>
            <div style="margin-top: 5px;">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm:ss') }}</div>
        </div>

        {{-- Buttons (Screen Only) --}}
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print Struk
            </button>
            @php
            $thermalRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.transactions.thermalReceipt' : 'transactions.thermalReceipt';
            $historyRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.history.index' : 'history.index';
            @endphp
            <a href="{{ route($thermalRoute, $transaction->id) }}" target="_blank" class="btn btn-success">
                <i class="bi bi-receipt"></i> Cetak Thermal
            </a>
            <a href="{{ route($historyRoute) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke History
            </a>
        </div>
    </div>
</body>

</html>