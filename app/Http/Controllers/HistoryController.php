<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $filter = $request->get('filter', 'today');
        $search = $request->get('search');
        $paymentMethod = $request->get('payment_method');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaction::with(['details.item.warehouseItem', 'user', 'member', 'booking'])
            ->when(auth()->user()->role === 'kasir', function ($q) {
                // Security: Cashier can only see their own transactions
                $q->where('user_id', auth()->id());
            })
            ->when($filter == 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($filter == 'week', function ($q) {
                $now = now();
                $q->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            })
            ->when($filter == 'month', fn($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
            ->when($filter == 'custom' && $startDate && $endDate, fn($q) => $q->whereBetween('created_at', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]))
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($search, fn($q) => $q->where(fn($sq) => $sq->whereRaw('LOWER(invoice) LIKE ?', ['%' . mb_strtolower($search) . '%'])->orWhereRaw('LOWER(customer_name) LIKE ?', ['%' . mb_strtolower($search) . '%'])))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        $transactions = $query->paginate(10);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('history.index', compact('transactions', 'filter', 'search', 'paymentMethod', 'startDate', 'endDate'));
            return $view->fragment('data-container');
        }

        return view('history.index', compact('transactions', 'filter', 'search', 'paymentMethod', 'startDate', 'endDate'));
    }

    public function show(Transaction $transaction): View|RedirectResponse
    {
        // IDOR Fix: Kasir hanya bisa melihat transaksi miliknya sendiri
        if (auth()->user()->role !== 'admin' && $transaction->user_id !== auth()->id()) {
            $route = auth()->user()->role === 'kasir' ? 'cashier.history.index' : 'history.index';
            return redirect()->route($route)->with('error', 'Anda tidak memiliki akses untuk melihat transaksi ini');
        }

        $details = TransactionDetail::where('transaction_id', $transaction->id)->with('item')->get();
        return view('history.show', compact('transaction', 'details'));
    }
}
