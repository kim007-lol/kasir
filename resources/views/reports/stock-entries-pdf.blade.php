<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Stok Masuk</title>
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

        .badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        <h1>Laporan Riwayat Stok Masuk Gudang</h1>
        @if($startDate && $endDate)
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @else
        <p>Semua Data</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <strong>Total Record:</strong> {{ $entries->count() }} entri
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Qty Masuk:</strong> {{ number_format($entries->sum('quantity'), 0, ',', '.') }} unit
    </div>

    @if($entries->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Tanggal Masuk</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Supplier</th>
                <th class="text-center">Qty Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $index => $entry)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                <td>{{ $entry->warehouseItem->code }}</td>
                <td>{{ $entry->warehouseItem->name }}</td>
                <td>{{ $entry->warehouseItem->category->name ?? '-' }}</td>
                <td>{{ $entry->supplier->name ?? '-' }}</td>
                <td class="text-center"><span class="badge">+{{ $entry->quantity }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #666; padding: 30px;">Tidak ada data riwayat stok masuk.</p>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Kasirku</p>
    </div>
</body>

</html>
