<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $filter = $request->get('filter', 'today');
        $date = $request->get('date', now()->toDateString());
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query berdasarkan filter
        $transactionQuery = Transaction::query();

        if ($filter == 'today') {
            $transactionQuery->whereDate('created_at', $date);
        } elseif ($filter == 'week') {
            $transactionQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $transactionQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $transactionQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }
        // If filter is 'all', we don't apply any date constraints

        // Efficient Aggregates
        $totalTransactions = $transactionQuery->count();
        $totalRevenue = $transactionQuery->sum('total');

        $transactions = $transactionQuery->with(['details.item', 'member'])
            ->latest()
            ->paginate(10);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
        $transactions->withQueryString();

        // Total barang terjual (Efficient)
        $totalItemsSold = TransactionDetail::whereHas('transaction', function ($q) use ($date, $filter, $startDate, $endDate) {
            if ($filter == 'today' || !$filter) {
                $q->whereDate('created_at', $date);
            } elseif ($filter == 'week') {
                $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter == 'month') {
                $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            } elseif ($filter == 'custom' && $startDate && $endDate) {
                $q->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
        })->sum('qty');

        // Perhitungan keuntungan kotor dan bersih
        $grossProfit = $totalRevenue; // Keuntungan kotor = total penjualan (Revenue)

        // Hitung total harga beli (HPP) dari item yang terjual
        // Note: usage of original $transactionQuery might be affected if we don't clone? 
        // Query builder is mutable? No, usually fine unless we call get().
        // Actually, we called count() and sum() which reset binding? No.
        // But to be safe, let's rebuild or clone if needed. 
        // Transaction::query() returns a new builder.
        // But $transactionQuery is reused.

        // Efficient Total Cost Calculation using Joins
        $totalCost = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('cashier_items', 'transaction_details.item_id', '=', 'cashier_items.id')
            ->join('warehouse_items', 'cashier_items.warehouse_item_id', '=', 'warehouse_items.id')
            ->whereIn('transactions.id', $transactionQuery->pluck('id'))
            ->sum(DB::raw('transaction_details.qty * warehouse_items.purchase_price'));

        $netProfit = $grossProfit - $totalCost;

        // Efficient Stock Entries Retrieval
        // 1. Get all relevant warehouse_item_ids from the sold items
        $soldItemIds = TransactionDetail::select('item_id')
            ->whereHas('transaction', function ($query) use ($transactionQuery) {
                $query->whereIn('id', $transactionQuery->pluck('id'));
            })
            ->distinct()
            ->pluck('item_id');

        $warehouseItemIds = \App\Models\CashierItem::whereIn('id', $soldItemIds)->pluck('warehouse_item_id', 'id');

        // 2. Pre-fetch Stock Entries aggregated by warehouse_item_id
        $stockEntriesQuery = \App\Models\StockEntry::selectRaw('warehouse_item_id, SUM(quantity) as total_quantity')
            ->whereIn('warehouse_item_id', $warehouseItemIds->values());

        if ($filter == 'today' || !$filter) {
            $stockEntriesQuery->whereDate('entry_date', $date);
        } elseif ($filter == 'week') {
            $stockEntriesQuery->whereBetween('entry_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $stockEntriesQuery->whereMonth('entry_date', now()->month);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $stockEntriesQuery->whereDate('entry_date', '>=', $startDate)->whereDate('entry_date', '<=', $endDate);
        }

        $stockEntriesMap = $stockEntriesQuery->groupBy('warehouse_item_id')
            ->pluck('total_quantity', 'warehouse_item_id');

        // Detail barang terjual dengan stok masuk (Memory Mapping)
        $itemDetails = TransactionDetail::selectRaw('item_id, SUM(qty) as total_sold')
            ->whereHas('transaction', function ($query) use ($transactionQuery) {
                $query->whereIn('id', $transactionQuery->pluck('id'));
            })
            ->with(['item.category']) // Eager load category
            ->groupBy('item_id')
            ->get()
            ->map(function ($detail) use ($stockEntriesMap, $warehouseItemIds) {
                $item = $detail->item;

                if (!$item) {
                    return [
                        'code' => 'N/A',
                        'name' => '[Item Dihapus]',
                        'total_sold' => $detail->total_sold,
                        'stock_in' => 0,
                        'current_stock' => 0,
                    ];
                }

                // Map using pre-fetched data
                $warehouseItemId = $item->warehouse_item_id;
                $stockIn = $stockEntriesMap[$warehouseItemId] ?? 0;

                return [
                    'code' => $item->code,
                    'name' => $item->name, // Ensure relationship is loaded or accessible
                    'total_sold' => $detail->total_sold,
                    'stock_in' => $stockIn,
                    'current_stock' => $item->stock,
                ];
            });

        // Top Selling Items
        $topSellingItems = TransactionDetail::select('item_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('transaction', function ($query) use ($transactionQuery) {
                $query->whereIn('id', $transactionQuery->pluck('id'));
            })
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $view = view('reports.index', compact(
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

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view->fragment('data-container');
            return $view;
        }

        return $view;
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

        return $pdf->download('laporan-transaksi-detail-' . $date . '.pdf');
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
            ->paginate(10);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $entriesView */
            $entriesView = view('reports.stock-entries', compact('entries', 'startDate', 'endDate'));
            $entriesView->fragment('data-container');
            return $entriesView;
        }

        return view('reports.stock-entries', compact('entries', 'startDate', 'endDate'));
    }
    public function transferHistory(Request $request): View
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $logs = \App\Models\StockTransferLog::with(['warehouseItem', 'cashierItem', 'user'])
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->latest()
            ->paginate(15);

        return view('reports.transfer-history', compact('logs', 'startDate', 'endDate'));
    }
}
