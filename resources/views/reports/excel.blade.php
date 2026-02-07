<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold; font-size: 14px;">Laporan Transaksi</th>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center;">Tanggal: {{ $date }}</td>
        </tr>
        <tr>
            <th>No</th>
            <th>Invoice</th>
            <th>Waktu</th>
            <th>Pelanggan</th>
            <th>Total Belanja</th>
            <th>Keuntungan Bersih</th>
            <th>Metode Pembayaran</th>
            <th>Item</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $index => $transaction)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $transaction->invoice }}</td>
            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $transaction->customer_name ?? '-' }}</td>
            <td>{{ $transaction->total }}</td>
            <td>{{ $transaction->net_profit }}</td>
            <td>{{ strtoupper($transaction->payment_method) }}</td>
            <td>
                @foreach($transaction->details as $detail)
                {{ $detail->item->name ?? '[Item Dihapus]' }} ({{ $detail->qty }} x {{ $detail->price }}),
                @endforeach
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Total Pendapatan</td>
            <td style="font-weight: bold;">{{ $transactions->sum('total') }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Total Keuntungan Bersih</td>
            <td style="font-weight: bold;">{{ $transactions->sum('net_profit') }}</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>