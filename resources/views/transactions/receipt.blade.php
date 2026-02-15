@extends(auth()->check() && auth()->user()->role === 'kasir' ? 'layouts.cashier' : 'layouts.app')

@section('title', 'Struk Transaksi')

@section('content')
<style>
    @media print {
        @page {
            margin: 0;
            size: 58mm auto;
        }

        body {
            background: white !important;
        }

        .sidebar,
        .navbar,
        .btn,
        .content>.d-flex:first-child,
        .alert,
        .card-header {
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
            font-size: 11px;
        }

        .table-sm td,
        .table-sm th {
            padding: 2px 4px !important;
        }

        hr {
            margin: 4px 0 !important;
            border-top: 1px dashed #666 !important;
        }

        .border-bottom {
            border-bottom: 1px solid #ddd !important;
        }

        h4 {
            font-size: 14px;
            margin-bottom: 4px !important;
        }

        h3 {
            font-size: 16px;
        }

        h5 {
            font-size: 12px;
        }

        h6 {
            font-size: 11px;
        }

        small {
            font-size: 9px;
        }

        .text-muted {
            color: #666 !important;
        }

        .d-flex.justify-content-center {
            display: none !important;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0" id="receiptCard" data-print="{{ session()->has('success') ? 'true' : 'false' }}">
            {{-- Header Receipt Style --}}
            <div class="card-header bg-dark text-white text-center py-2">
                <h6 class="mb-0 fw-bold">STRUK PEMBELIAN</h6>
            </div>
            <div class="card-body p-3">
                {{-- Store Info --}}
                <div class="text-center mb-2">
                    <h5 class="fw-bold mb-0">SMEGABIZ</h5>
                    <small class="text-muted">Surabaya</small>
                </div>

                <hr style="border-top: 1px dashed #999; margin: 8px 0;">

                {{-- Transaction Info --}}
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">No: {{ $lastTransaction->invoice }}</small>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">{{ $lastTransaction->created_at->isoFormat('dddd, D MMMM Y HH:mm') }}</small>
                    </div>
                </div>

                @if ($lastTransaction->customer_name)
                <div class="mb-2">
                    <small class="text-muted">Customer: {{ $lastTransaction->customer_name }}</small>
                </div>
                @endif

                <div class="mb-2">
                    <small class="text-muted">Kasir: {{ auth()->user()->name ?? 'Admin' }}</small>
                </div>

                <hr style="border-top: 1px dashed #999; margin: 8px 0;">

                {{-- Items Table --}}
                <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                    <thead>
                        <tr class="text-muted" style="border-bottom: 1px solid #ddd;">
                            <th class="text-start ps-0">Produk</th>
                            <th class="text-center p-0" style="width: 30px;">Qty</th>
                            <th class="text-end p-0" style="width: 70px;"> Harga</th>
                            <th class="text-end p-0" style="width: 80px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                        <tr class="border-bottom" style="border-bottom: 1px dotted #ddd;">
                            <td class="text-start ps-0 py-1">
                                {{ $detail->item->name }}
                                @if($detail->discount > 0)
                                <br><small class="text-muted" style="font-size: 0.65rem;">(Potongan: Rp {{ number_format($detail->discount, 0, ',', '.') }})</small>
                                @endif
                            </td>
                            <td class="text-center py-1">{{ $detail->qty }}</td>
                            <td class="text-end py-1">
                                <strong style="font-size: 0.85rem;">{{ number_format($detail->price, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end py-1">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <hr style="border-top: 1px dashed #999; margin: 8px 0;">

                {{-- Payment Summary --}}
                <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                    <tr>
                        <td class="text-start ps-0">Total Belanja</td>
                        <td class="text-end pe-0 fw-bold">Rp {{ number_format($lastTransaction->total + ($lastTransaction->discount_amount ?? 0), 0, ',', '.') }}</td>
                    </tr>
                    @if(($lastTransaction->discount_amount ?? 0) > 0)
                    <tr>
                        <td class="text-start ps-0">Potongan</td>
                        <td class="text-end pe-0 text-danger">-Rp {{ number_format($lastTransaction->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-start ps-0 fw-bold">Grand Total</td>
                        <td class="text-end pe-0 fw-bold">Rp {{ number_format($lastTransaction->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-0">Metode Bayar</td>
                        <td class="text-end pe-0">{{ strtoupper($lastTransaction->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td class="text-start ps-0">Tunai</td>
                        <td class="text-end pe-0">Rp {{ number_format($lastTransaction->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td class="text-start ps-0">Kembalian</td>
                        <td class="text-end pe-0 text-success">Rp {{ number_format($lastTransaction->change_amount, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <hr style="border-top: 1px dashed #999; margin: 8px 0;">

                {{-- Footer --}}
                <div class="text-center mb-3">
                    <small class="text-muted d-block">TERIMA KASIH</small>

                </div>

                {{-- Barcode Simulation --}}
                <div class="text-center mb-3">
                    <div style="height: 30px; background: repeating-linear-gradient(90deg, #000 0px, #000 2px, transparent 2px, transparent 4px);"></div>
                    <small class="text-muted">{{ $lastTransaction->invoice }}</small>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-center gap-2 flex-wrap no-print">
                    <button onclick="window.print()" class="btn btn-primary btn-sm">
                        <i class="bi bi-printer"></i> Print
                    </button>
                    <button onclick="goBack()" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    @php
                    $transactionRoute = auth()->check() && auth()->user()->role === 'kasir' ? 'cashier.transactions.index' : 'transactions.index';
                    @endphp
                    <a href="{{ route($transactionRoute) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Transaksi Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
    }

    .table-borderless td,
    .table-borderless th {
        border: none;
    }

    .border-bottom {
        border-bottom: 1px solid #e9ecef !important;
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 15px !important;
        }

        .d-flex.gap-2 {
            gap: 8px !important;
        }

        .btn {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
</style>

<script>
    function goBack() {
        window.location.href = "{{ route($transactionRoute) }}";
    }

    window.onload = function() {
        // Auto-print only if redirected from checkout (has success message)
        const receiptCard = document.getElementById('receiptCard');
        const hasSuccess = receiptCard ? receiptCard.dataset.print === 'true' : false;
        if (hasSuccess) {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    }
</script>
@endsection