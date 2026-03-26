<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan History Transaksi</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .summary-item h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #333;
        }

        .summary-item p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }

        .badge-cash {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-qris {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-pos {
            background-color: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-online {
            background-color: #0d6efd;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan History Transaksi</h1>
        @if($filterLabel)
        <p>Periode: {{ $filterLabel }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <h3>{{ $transactions->count() }}</h3>
            <p>Total Transaksi</p>
        </div>
        <div class="summary-item">
            <h3>Rp. {{ number_format($transactions->sum('total'), 0, ',', '.') }}</h3>
            <p>Total Pendapatan</p>
        </div>
        <div class="summary-item">
            <h3>{{ $transactions->where('payment_method', 'cash')->count() }}</h3>
            <p>Tunai (CASH)</p>
        </div>
        <div class="summary-item">
            <h3>{{ $transactions->where('payment_method', 'qris')->count() }}</h3>
            <p>QRIS</p>
        </div>
    </div>

    @if($transactions->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Invoice</th>
                <th>Pembeli</th>
                <th class="text-right">Total</th>
                <th class="text-center">Metode</th>
                <th class="text-center">Sumber</th>
                <th>Kasir</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->invoice }}</td>
                <td>{{ $transaction->customer_name ?? '-' }}</td>
                <td class="text-right"><strong>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
                <td class="text-center">
                    <span class="{{ $transaction->payment_method == 'qris' ? 'badge-qris' : 'badge-cash' }}">
                        {{ strtoupper($transaction->payment_method) }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="{{ ($transaction->source ?? 'pos') == 'online' ? 'badge-online' : 'badge-pos' }}">
                        {{ strtoupper($transaction->source ?? 'POS') }}
                    </span>
                </td>
                <td>{{ $transaction->cashier_name ?? $transaction->user->name ?? '-' }}</td>
                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #666; padding: 30px;">Tidak ada data transaksi.</p>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Kasirku</p>
    </div>
</body>

</html>
