<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .summary-item {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 10px 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .summary-item h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #333;
        }

        .summary-item p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .top-items {
            margin-bottom: 30px;
        }

        .top-items h2 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            background-color: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .footer {
            margin-top: 50px;
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
        <h1>Laporan Transaksi Harian</h1>
        <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <h3>{{ $totalTransactions }}</h3>
            <p>Total Transaksi</p>
        </div>
        <div class="summary-item">
            <h3>Rp. {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p>Pendapatan</p>
        </div>
        <div class="summary-item">
            <h3>Rp. {{ number_format($totalNetProfit, 0, ',', '.') }}</h3>
            <p>Keuntungan</p>
        </div>
        <div class="summary-item">
            <h3>{{ $totalItemsSold }}</h3>
            <p>Produk</p>
        </div>
        <div class="summary-item">
            <h3>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
            <p>Tanggal</p>
        </div>
    </div>

    @if($topSellingItems->count() > 0)
    <div class="top-items">
        <h2>5 Produk Terlaris</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Produk</th>
                    <th>Kode</th>
                    <th style="width: 120px;" class="text-center">Jumlah Terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSellingItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item->name ?? '[Item Dihapus]' }}</td>
                    <td><span class="badge">{{ $item->item->code ?? '-' }}</span></td>
                    <td class="text-center">{{ $item->total_qty }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <h2>Detail Transaksi</h2>
    @if($transactions->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Invoice</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Untung</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="badge">{{ $transaction->invoice }}</span></td>
                <td>{{ $transaction->customer_name ?? ($transaction->member ? $transaction->member->name : '-') }}</td>
                <td>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($transaction->net_profit, 0, ',', '.') }}</td>
                <td>{{ $transaction->created_at->format('H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>Tidak ada transaksi pada tanggal {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem SMEGABIZ</p>
    </div>
</body>

</html>