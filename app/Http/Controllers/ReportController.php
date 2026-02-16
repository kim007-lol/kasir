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
            $now = now();
            $transactionQuery->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
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
        $transactionIds = $transactionQuery->pluck('id');
        $totalTransactions = $transactionIds->count();
        $totalRevenue = $transactionQuery->sum('total');

        $transactions = $transactionQuery->with(['details.item', 'member'])
            ->latest()
            ->paginate(10);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
        $transactions->withQueryString();

        // Total barang terjual (Efficient)
        $totalItemsSold = TransactionDetail::whereIn('transaction_id', $transactionIds)->sum('qty');

        // Total Pendapatan
        $totalRevenueValue = $totalRevenue;

        // H4 Fix: Efficient Total Cost Calculation (HPP) — menggunakan raw query agar tidak load semua record ke memory
        $totalCost = TransactionDetail::whereIn('transaction_id', $transactionIds)
            ->selectRaw('SUM(qty * CASE WHEN purchase_price > 0 THEN purchase_price ELSE 0 END) as total')
            ->value('total') ?? 0;

        $netProfit = $totalRevenueValue - $totalCost;

        // Efficient Stock Entries Retrieval
        // 1. Get all relevant warehouse_item_ids from the sold items
        $soldItemIds = TransactionDetail::select('item_id')
            ->whereIn('transaction_id', $transactionIds)
            ->distinct()
            ->pluck('item_id');

        $warehouseItemIds = \App\Models\CashierItem::whereIn('id', $soldItemIds)->pluck('warehouse_item_id', 'id');

        // 2. Pre-fetch Stock Entries aggregated by warehouse_item_id
        $stockEntriesQuery = \App\Models\StockEntry::selectRaw('warehouse_item_id, SUM(quantity) as total_quantity')
            ->whereIn('warehouse_item_id', $warehouseItemIds->values());

        if ($filter == 'today' || !$filter) {
            $stockEntriesQuery->whereDate('entry_date', $date);
        } elseif ($filter == 'week') {
            $nowStock = now();
            $stockEntriesQuery->whereBetween('entry_date', [$nowStock->copy()->startOfWeek(), $nowStock->copy()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $stockEntriesQuery->whereMonth('entry_date', now()->month);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $stockEntriesQuery->whereDate('entry_date', '>=', $startDate)->whereDate('entry_date', '<=', $endDate);
        }

        $stockEntriesMap = $stockEntriesQuery->groupBy('warehouse_item_id')
            ->pluck('total_quantity', 'warehouse_item_id');

        // Detail barang terjual dengan stok masuk (Memory Mapping)
        $itemDetails = TransactionDetail::selectRaw('item_id, SUM(qty) as total_sold')
            ->whereIn('transaction_id', $transactionIds)
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
            ->whereIn('transaction_id', $transactionIds)
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
            return $view->fragment('data-container');
        }

        return $view;
    }

    public function exportPdf(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $filter = $request->get('filter', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query berdasarkan filter (sama seperti exportExcel)
        $transactionQuery = Transaction::query();

        if ($filter == 'today' || !$filter) {
            $transactionQuery->whereDate('created_at', $date);
        } elseif ($filter == 'week') {
            $now = now();
            $transactionQuery->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $transactionQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $transactionQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
        }

        // BUG-05 Fix: Eager load details untuk menghindari N+1 query saat menghitung net_profit
        $transactions = $transactionQuery
            ->with(['details.item.warehouseItem', 'member'])
            ->latest()
            ->get();

        $transactionIds = $transactions->pluck('id');

        $totalTransactions = $transactions->count();
        $totalRevenue = $transactions->sum('total');
        $totalNetProfit = $transactions->sum('net_profit');
        $totalItemsSold = TransactionDetail::whereIn('transaction_id', $transactionIds)->sum('qty');

        $topSellingItems = TransactionDetail::selectRaw('item_id, SUM(qty) as total_qty')
            ->whereIn('transaction_id', $transactionIds)
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
            $nowExcel = now();
            $transactionQuery->whereBetween('created_at', [$nowExcel->copy()->startOfWeek(), $nowExcel->copy()->endOfWeek()]);
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
            return $entriesView->fragment('data-container');
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
