<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\View\View;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $filter = $request->get('filter', 'today');
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaction::with(['details.item.warehouseItem', 'user', 'member'])
            ->when($filter == 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($filter == 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($filter == 'month', fn($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
            ->when($filter == 'custom' && $startDate && $endDate, fn($q) => $q->whereBetween('created_at', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]))
            ->when($search, fn($q) => $q->where(fn($sq) => $sq->where('invoice', 'ilike', "%{$search}%")->orWhere('customer_name', 'ilike', "%{$search}%")))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        $transactions = $query->paginate(10);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('history.index', compact('transactions', 'filter', 'search', 'startDate', 'endDate'));
            return $view->fragment('data-container');
        }

        return view('history.index', compact('transactions', 'filter', 'search', 'startDate', 'endDate'));
    }

    public function show(Transaction $transaction): View
    {
        $details = TransactionDetail::where('transaction_id', $transaction->id)->with('item')->get();
        return view('history.show', compact('transaction', 'details'));
    }
}
