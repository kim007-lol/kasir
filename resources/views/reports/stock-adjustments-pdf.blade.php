<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Penyesuaian Stok (Opname)</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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
            font-size: 20px;
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
            padding: 5px 6px;
            text-align: left;
        }

        th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .type-increase {
            color: #28a745;
            font-weight: bold;
        }

        .type-decrease {
            color: #dc3545;
            font-weight: bold;
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
        <h1>Laporan Penyesuaian Stok (Opname)</h1>
        @if($startDate && $endDate)
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @else
        <p>Semua Data</p>
        @endif
        @if($target)
        <p>Target: {{ $target === 'warehouse' ? 'Gudang' : 'Kasir' }}</p>
        @endif
        @if($type)
        <p>Tipe: {{ $type === 'increase' ? 'Penambahan' : 'Pengurangan' }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <strong>Total Record:</strong> {{ $adjustments->count() }} penyesuaian
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Qty:</strong> {{ number_format($adjustments->sum('quantity'), 0, ',', '.') }} unit
    </div>

    @if($adjustments->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Waktu</th>
                <th>Target</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Tipe</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Sebelum</th>
                <th class="text-center">Sesudah</th>
                <th>Alasan</th>
                <th>User</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $reasonLabels = [
                    'hilang' => 'Hilang',
                    'rusak' => 'Rusak',
                    'salah_input' => 'Salah Input',
                    'stock_opname' => 'Stock Opname',
                    'lainnya' => 'Lainnya',
                ];
            @endphp
            @foreach($adjustments as $index => $adj)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $adj->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $adj->target === 'warehouse' ? 'Gudang' : 'Kasir' }}</td>
                <td>{{ $adj->item_code }}</td>
                <td>{{ $adj->item_name }}</td>
                <td class="{{ $adj->type === 'increase' ? 'type-increase' : 'type-decrease' }}">
                    {{ $adj->type === 'increase' ? '▲ Tambah' : '▼ Kurang' }}
                </td>
                <td class="text-center"><strong>{{ $adj->quantity }}</strong></td>
                <td class="text-center">{{ $adj->stock_before }}</td>
                <td class="text-center"><strong>{{ $adj->stock_after }}</strong></td>
                <td>{{ $reasonLabels[$adj->reason] ?? $adj->reason }}</td>
                <td>{{ $adj->user->name ?? '-' }}</td>
                <td>{{ Str::limit($adj->notes ?? '-', 40) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #666; padding: 30px;">Belum ada data penyesuaian stok.</p>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Kasirku</p>
    </div>
</body>

</html>
