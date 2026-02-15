<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #333;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10px;
            color: #666;
            margin-bottom: 0;
        }

        .store-info {
            text-align: center;
            margin-bottom: 10px;
        }

        .store-info h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .store-info p {
            font-size: 9px;
            color: #888;
            margin: 0;
        }

        .info {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ccc;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .info-label {
            font-weight: 500;
        }

        .info-value {
            text-align: right;
        }

        .items {
            margin-bottom: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 6px 0;
            border-bottom: 1px dotted #ddd;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 500;
            font-size: 11px;
        }

        .item-details {
            font-size: 10px;
            color: #666;
        }

        .item-subtotal {
            font-weight: 600;
            text-align: right;
        }

        .divider {
            border: none;
            border-top: 2px dashed #333;
            margin: 8px 0;
        }

        .payment-section {
            margin-bottom: 10px;
            padding-top: 8px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .payment-label {
            font-size: 11px;
        }

        .payment-value {
            font-size: 11px;
            text-align: right;
        }

        .change-row {
            font-weight: bold;
            color: #28a745;
        }

        .total-section {
            text-align: right;
            margin: 10px 0;
            padding-top: 8px;
            border-top: 2px dashed #333;
        }

        .total-label {
            font-size: 12px;
            font-weight: 600;
        }

        .total-amount {
            font-size: 18px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }

        .footer p {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }

        .barcode-area {
            text-align: center;
            margin: 15px 0;
        }

        .barcode-line {
            height: 30px;
            background: repeating-linear-gradient(90deg,
                    #000 0px,
                    #000 2px,
                    transparent 2px,
                    transparent 4px);
            margin-bottom: 5px;
        }

        .print-btn {
            display: none;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-btn {
                display: none !important;
            }
        }

        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px;
            }

            .receipt {
                background: white;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .print-btn {
                display: block;
                text-align: center;
                margin-top: 20px;
            }

            .print-btn button,
            .print-btn a {
                padding: 12px 24px;
                margin: 5px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                text-decoration: none;
                display: inline-block;
            }

            .btn-print {
                background: #5b9dd9;
                color: white;
            }

            .btn-new {
                background: #48bb78;
                color: white;
            }

            .btn-back {
                background: #6c757d;
                color: white;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="store-info">
            <h3>SMEGABIZ</h3>
            <p>Surabaya</p>
        </div>

        <div class="header">
            <h2>STRUK PEMBELIAN</h2>
            <p>SMEGABIZ</p>
        </div>

        <div class="info">
            <div class="info-row">
                <span class="info-label">No. Trans:</span>
                <span class="info-value">{{ $transaction->invoice }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tgl:</span>
                <span class="info-value">{{ $transaction->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</span>
            </div>
            @if ($transaction->customer_name)
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $transaction->customer_name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Kasir:</span>
                <span class="info-value">{{ auth()->user()->name ?? 'Admin' }}</span>
            </div>
        </div>

        <hr class="divider">

        <div class="items">
            @foreach ($details as $detail)
            <div class="item">
                <div>
                    <div class="item-name">{{ $detail->item->name }}</div>
                    <div class="item-details">
                        {{ $detail->qty }} x {{ number_format($detail->price, 0, ',', '.') }}
                    </div>
                </div>
                <div class="item-subtotal">
                    {{ number_format($detail->subtotal, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>

        <hr class="divider">

        {{-- Payment Summary --}}
        <div class="payment-section">
            <div class="payment-row">
                <span class="payment-label">Total Belanja</span>
                <span class="payment-value">{{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
            <div class="payment-row">
                <span class="payment-label">Metode Bayar</span>
                <span class="payment-value">{{ strtoupper($transaction->payment_method) }}</span>
            </div>
            <div class="payment-row">
                <span class="payment-label">Tunai</span>
                <span class="payment-value">{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="payment-row change-row">
                <span class="payment-label">Kembalian</span>
                <span class="payment-value">{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr class="divider">

        <div class="total-section">
            <div class="total-label">TUNAI</div>
            <div class="total-amount">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</div>
        </div>

        {{-- Barcode Simulation --}}
        <div class="barcode-area">
            <div class="barcode-line"></div>
            <small style="font-size: 9px; color: #666;">{{ $transaction->invoice }}</small>
        </div>

        <div class="footer">
            <p>*** TERIMA KASIH ***</p>
            <p style="margin-top: 8px; font-size: 9px;">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm:ss') }}</p>
        </div>
    </div>

    <div class="print-btn">
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-printer"></i> Print Struk
        </button>
        @php
        $transactionRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.transactions.index' : 'transactions.index';
        $historyRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.history.index' : 'history.index';
        @endphp
        <a href="{{ route($transactionRoute) }}" class="btn-new">
            <i class="bi bi-plus-circle"></i> Transaksi Baru
        </a>
        <a href="{{ route($historyRoute) }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</body>

</html>