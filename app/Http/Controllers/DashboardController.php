<?php

namespace App\Http\Controllers;

use App\Models\WarehouseItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(): \Illuminate\Http\JsonResponse
    {
        $today = Carbon::today();

        return response()->json([
            'totalItems' => WarehouseItem::count(),
            'totalTransactions' => Transaction::whereDate('created_at', $today)->count(),
            'totalProductsSoldToday' => TransactionDetail::whereHas('transaction', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })->sum('qty'),
            'totalRevenueToday' => Transaction::whereDate('created_at', $today)->sum('total')
        ]);
    }

    public function index(): View
    {
        $today = Carbon::today();

        $totalItems = WarehouseItem::count();
        $totalTransactions = Transaction::whereDate('created_at', $today)->count();

        $totalProductsSoldToday = TransactionDetail::whereHas('transaction', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('qty');

        $totalRevenueToday = Transaction::whereDate('created_at', $today)->sum('total');

        // Data untuk grafik 30 hari terakhir
        $salesData = Transaction::selectRaw('CAST(created_at AS DATE) as date, COUNT(*) as count, SUM(total) as total')
            ->whereDate('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with zero values
        $salesChart = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesChart->push([
                'date' => $date,
                'count' => $salesData->get($date)->count ?? 0,
                'total' => $salesData->get($date)->total ?? 0,
            ]);
        }

        // Low Stock Alert
        $lowStockItems = \App\Models\CashierItem::where('stock', '>', 0)
            ->where('stock', '<', 5)
            ->orderBy('stock')
            ->get(['id', 'name', 'stock', 'code']);

        return view('dashboard.index', compact(
            'totalItems',
            'totalTransactions',
            'totalProductsSoldToday',
            'totalRevenueToday',
            'salesChart',
            'lowStockItems'
        ));
    }
}
