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
            ->appends($request->query());

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

        $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';

        try {
            $result = DB::transaction(function () use ($validated) {
                // Perbaikan Race Condition: Lock row untuk memastikan konsistensi selama transaksi
                $item = CashierItem::lockForUpdate()->findOrFail($validated['cashier_item_id']);

                $stockBefore = $item->stock;

                if ($validated['type'] === 'decrease' && $item->stock < $validated['quantity']) {
                    throw new \Exception("Stok tidak cukup! Stok saat ini: {$item->stock}");
                }

                if ($validated['type'] === 'increase') {
                    $item->increment('stock', $validated['quantity']);
                } else {
                    $item->decrement('stock', $validated['quantity']);
                }

                // Refresh untuk mendapatkan nilai setelah decrement/increment diproses db
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

                return [
                    'name' => $item->name,
                    'stockBefore' => $stockBefore,
                    'stockAfter' => $item->stock
                ];
            });

            return redirect()->route($routePrefix . 'stock-adjustments.index')
                ->with('success', "Penyesuaian stok berhasil. {$result['name']}: {$result['stockBefore']} → {$result['stockAfter']}");
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            // Handle unique message vs unknown exceptions
            if (!str_starts_with($errorMessage, 'Stok tidak cukup!')) {
                $errorMessage = 'Terjadi kesalahan: ' . $errorMessage;
            }

            return redirect()->route($routePrefix . 'stock-adjustments.create')
                ->with('error', $errorMessage)
                ->withInput();
        }
    }
}
