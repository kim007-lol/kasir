<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalItems = Item::count();
        $totalTransactions = Transaction::count();

        $today = Carbon::today();

        $totalProductsSoldToday = TransactionDetail::whereHas('transaction', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('qty');

        $totalRevenueToday = Transaction::whereDate('created_at', $today)->sum('total');

        // Data untuk grafik 7 hari terakhir
        $salesChart = Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as total')
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.index', compact(
            'totalItems',
            'totalTransactions',
            'totalProductsSoldToday',
            'totalRevenueToday',
            'salesChart'
        ));
    }
}
