<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'today');
        $date = $request->get('date', now()->toDateString());
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query berdasarkan filter
        $transactionQuery = Transaction::query();

        if ($filter == 'today' || !$filter) {
            $transactionQuery->whereDate('created_at', $date);
        } elseif ($filter == 'week') {
            $transactionQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $transactionQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $transactionQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $transactionQuery->with(['details.item', 'member'])->latest()->get();

        $totalTransactions = $transactions->count();
        $totalRevenue = $transactions->sum('total');

        // Total barang terjual
        $totalItemsSold = $transactions->flatMap->details->sum('qty');

        // Perhitungan keuntungan kotor dan bersih
        $grossProfit = $totalRevenue; // Keuntungan kotor = total penjualan (Revenue)

        // Hitung total harga beli (HPP) dari item yang terjual
        $totalCost = $transactionQuery->pluck('id')->chunk(100)->sum(function ($chunk) {
            return TransactionDetail::whereIn('transaction_id', $chunk)
                ->with('item.warehouseItem')
                ->get()
                ->sum(function ($detail) {
                    // Jika item atau warehouse item tidak ditemukan, asumsikan cost 0 (profit full)
                    return $detail->qty * ($detail->item?->warehouseItem?->purchase_price ?? 0);
                });
        });

        $netProfit = $grossProfit - $totalCost;

        // Detail barang terjual dengan stok masuk
        $itemDetails = TransactionDetail::selectRaw('item_id, SUM(qty) as total_sold')
            ->whereHas('transaction', function ($query) use ($transactionQuery) {
                // Apply same filter as transactions
                $query->whereIn('id', $transactionQuery->pluck('id'));
            })
            ->with('item.category')
            ->groupBy('item_id')
            ->get()
            ->map(function ($detail) use ($date, $filter, $startDate, $endDate) {
                $item = $detail->item;

                // Jika item sudah dihapus dari sistem
                if (!$item) {
                    return [
                        'code' => 'N/A',
                        'name' => '[Item Dihapus]',
                        'total_sold' => $detail->total_sold,
                        'stock_in' => 0,
                        'current_stock' => 0,
                    ];
                }

                // Get stock entries untuk periode yang sama
                $stockEntryQuery = \App\Models\StockEntry::where('warehouse_item_id', $item->warehouse_item_id);

                if ($filter == 'today' || !$filter) {
                    $stockEntryQuery->whereDate('entry_date', $date);
                } elseif ($filter == 'week') {
                    $stockEntryQuery->whereBetween('entry_date', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($filter == 'month') {
                    $stockEntryQuery->whereMonth('entry_date', now()->month);
                } elseif ($filter == 'custom' && $startDate && $endDate) {
                    $stockEntryQuery->whereDate('entry_date', '>=', $startDate)->whereDate('entry_date', '<=', $endDate);
                }

                $stockIn = $stockEntryQuery->sum('quantity');

                return [
                    'code' => $item->code,
                    'name' => $item->name,
                    'total_sold' => $detail->total_sold,
                    'stock_in' => $stockIn,
                    'current_stock' => $item->stock,
                ];
            });

        // Top Selling Items (limit 5) - pastikan kita handle item yang dihapus
        $topSellingItems = TransactionDetail::selectRaw('item_id, SUM(qty) as total_qty')
            ->whereHas('transaction', function ($query) use ($transactionQuery) {
                // Apply same filter as transactions
                $query->whereIn('id', $transactionQuery->pluck('id'));
            })
            ->with('item')
            ->groupBy('item_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        return view('reports.index', compact(
            'transactions',
            'totalTransactions',
            'totalRevenue',
            'totalItemsSold',
            'grossProfit',
            'netProfit',
            'itemDetails',
            'topSellingItems',
            'filter',
            'date',
            'startDate',
            'endDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $transactions = Transaction::whereDate('created_at', $date)
            ->with(['details.item.warehouseItem', 'member'])
            ->latest()
            ->get();

        $totalTransactions = $transactions->count();
        $totalRevenue = $transactions->sum('total');
        $totalNetProfit = $transactions->sum('net_profit');
        $totalItemsSold = TransactionDetail::whereHas('transaction', function ($query) use ($date) {
            $query->whereDate('created_at', $date);
        })->sum('qty');

        $topSellingItems = TransactionDetail::selectRaw('item_id, SUM(qty) as total_qty')
            ->whereHas('transaction', function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->with('item')
            ->groupBy('item_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        $pdf = Pdf::loadView('reports.pdf', compact(
            'transactions',
            'totalTransactions',
            'totalRevenue',
            'totalNetProfit',
            'totalItemsSold',
            'topSellingItems',
            'date'
        ));

        return $pdf->download('laporan-transaksi-' . $date . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $filter = $request->get('filter', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query based on filter (reuse logic or refactor)
        // For simplicity, reusing the logic here
        $transactionQuery = Transaction::query();

        if ($filter == 'today' || !$filter) {
            $transactionQuery->whereDate('created_at', $date);
        } elseif ($filter == 'week') {
            $transactionQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $transactionQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $transactionQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $transactionQuery->with(['details.item.warehouseItem', 'member'])->latest()->get();

        return Excel::download(new ReportsExport($transactions, $date), 'laporan-transaksi-' . $date . '.xlsx');
    }

    public function stockEntriesHistory(Request $request): View
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $entries = \App\Models\StockEntry::with(['warehouseItem', 'supplier'])
            ->when($startDate, fn($q) => $q->whereDate('entry_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('entry_date', '<=', $endDate))
            ->latest('entry_date')
            ->paginate(50);

        return view('reports.stock-entries', compact('entries', 'startDate', 'endDate'));
    }
}
