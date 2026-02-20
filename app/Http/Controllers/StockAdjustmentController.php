<?php

namespace App\Http\Controllers;

use App\Models\CashierItem;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type'); // increase, decrease, or null for all

        $adjustments = StockAdjustment::with(['cashierItem', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('cashierItem', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($type, fn($q) => $q->where('type', $type))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock-adjustments.index', compact('adjustments', 'search', 'startDate', 'endDate', 'type'));
    }

    public function create(): View
    {
        $items = CashierItem::select('id', 'code', 'name', 'stock')
            ->orderBy('name')
            ->get();

        return view('stock-adjustments.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cashier_item_id' => 'required|exists:cashier_items,id',
            'type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item = CashierItem::findOrFail($validated['cashier_item_id']);

        $stockBefore = $item->stock;

        if ($validated['type'] === 'decrease' && $item->stock < $validated['quantity']) {
            $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
            return redirect()->route($routePrefix . 'stock-adjustments.create')
                ->with('error', "Stok tidak cukup! Stok saat ini: {$item->stock}")
                ->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $item, $stockBefore) {
                if ($validated['type'] === 'increase') {
                    $item->increment('stock', $validated['quantity']);
                } else {
                    $item->decrement('stock', $validated['quantity']);
                }

                $item->refresh();

                StockAdjustment::create([
                    'cashier_item_id' => $validated['cashier_item_id'],
                    'user_id' => auth()->id(),
                    'type' => $validated['type'],
                    'quantity' => $validated['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $item->stock,
                    'reason' => $validated['reason'],
                    'notes' => $validated['notes'] ?? null,
                ]);
            });

            $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
            return redirect()->route($routePrefix . 'stock-adjustments.index')
                ->with('success', "Penyesuaian stok berhasil. {$item->name}: {$stockBefore} → {$item->stock}");
        } catch (\Exception $e) {
            $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
            return redirect()->route($routePrefix . 'stock-adjustments.create')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
