@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Laporan
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('transactions.downloadReceipt', $transaction->id) }}" target="_blank" class="btn btn-info">
                    <i class="bi bi-printer"></i> Print Struk
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0" id="receiptCard">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-1">STRUK PEMBELIAN</h4>
                    <p class="text-muted mb-0">Toko Makmur</p>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="col-6">
                        <p class="mb-1"><strong>Invoice:</strong></p>
                        <p class="text-muted mb-2">{{ $transaction->invoice }}</p>

                        <p class="mb-1"><strong>Metode Pembayaran:</strong></p>
                        <p class="text-muted">
                            <span class="badge {{ $transaction->payment_method == 'qris' ? 'bg-info' : 'bg-success' }}">
                                {{ strtoupper($transaction->payment_method) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-1"><strong>Tanggal:</strong></p>
                        <p class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>

                @if ($transaction->customer_name)
                <div class="row">
                    <div class="col-12">
                        <p class="mb-1"><strong>Nama Pembeli:</strong></p>
                        <p class="text-muted">{{ $transaction->customer_name }}</p>
                    </div>
                </div>
                @endif

                <hr class="my-3">

                <table class="table table-sm table-borderless mb-0">
                    <thead class="border-bottom">
                        <tr>
                            <th class="text-start">No</th>
                            <th class="text-start">Produk</th>
                            <th class="text-center" style="width: 50px;">Qty</th>
                            <th class="text-end" style="width: 100px;">Harga</th>
                            <th class="text-end" style="width: 120px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($details as $index => $detail)
                        <tr class="border-bottom">
                            <td class="text-start">{{ $index + 1 }}</td>
                            <td class="text-start">
                                {{ $detail->item->name }}
                                @if(isset($detail->item->code))
                                <br><small class="text-muted">{{ $detail->item->code }}</small>
                                @endif
                                @if($detail->discount > 0)
                                <br><small class="text-warning">(Diskon {{ $detail->discount }}%)</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $detail->qty }}</td>
                            <td class="text-end">
                                @if($detail->discount > 0)
                                <small class="text-muted" style="text-decoration: line-through;">
                                    Rp. {{ number_format($detail->original_price, 0, ',', '.') }}
                                </small><br>
                                @endif
                                Rp. {{ number_format($detail->price, 0, ',', '.') }}
                            </td>
                            <td class="text-end">Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <hr class="my-3">

                <div class="row">
                    <div class="col-12 text-end">
                        <h5 class="mb-1">Total Pembayaran:</h5>
                        <h3 class="fw-bold text-primary">Rp. {{ number_format($transaction->total, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <hr class="my-3">

                <div class="text-center mt-4">
                    <p class="mb-4 text-muted">Terima kasih telah berbelanja</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            margin: 0.5cm;
            size: 80mm auto;
        }

        body {
            background: white !important;
        }

        .sidebar,
        .navbar,
        .btn,
        .d-flex.justify-content-between,
        .alert {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .row {
            margin: 0 !important;
        }

        .col-md-8 {
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
        }

        .card-body {
            padding: 10px !important;
        }

        .table {
            font-size: 12px;
        }

        .table-sm td,
        .table-sm th {
            padding: 4px 8px !important;
        }

        hr {
            margin: 8px 0 !important;
        }

        .border-bottom {
            border-bottom: 1px solid #ddd !important;
        }

        h4 {
            font-size: 16px;
            margin-bottom: 5px !important;
        }

        h3 {
            font-size: 20px;
        }

        h5 {
            font-size: 14px;
        }

        small {
            font-size: 10px;
        }

        .text-muted {
            color: #666 !important;
        }
    }

    .card {
        border-radius: 12px;
    }

    .table-borderless td,
    .table-borderless th {
        border: none;
    }

    .border-bottom {
        border-bottom: 1px solid #e9ecef !important;
    }
</style>
@endsection