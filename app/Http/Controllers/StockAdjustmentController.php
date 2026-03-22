<?php

namespace App\Http\Controllers;

use App\Models\CashierItem;
use App\Models\WarehouseItem;
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
        $target = $request->get('target'); // cashier, warehouse, or null for all

        $adjustments = StockAdjustment::with(['cashierItem', 'warehouseItem', 'user'])
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->where(function ($q) use ($searchLower) {
                    // Search in cashier items
                    $q->whereHas('cashierItem', function ($sub) use ($searchLower) {
                        $sub->withTrashed()
                            ->where(function ($s) use ($searchLower) {
                                $s->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                                    ->orWhereRaw('LOWER(code) LIKE ?', [$searchLower]);
                            });
                    })
                    // Search in warehouse items
                    ->orWhereHas('warehouseItem', function ($sub) use ($searchLower) {
                        $sub->where(function ($s) use ($searchLower) {
                            $s->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                                ->orWhereRaw('LOWER(code) LIKE ?', [$searchLower]);
                        });
                    });
                });
            })
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($target, fn($q) => $q->where('target', $target))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('stock-adjustments.index', compact('adjustments', 'search', 'startDate', 'endDate', 'type', 'target'));
    }

    public function create(): View
    {
        $cashierItems = CashierItem::select('id', 'code', 'name', 'stock')
            ->orderBy('name')
            ->get();

        $warehouseItems = WarehouseItem::select('id', 'code', 'name', 'stock')
            ->orderBy('name')
            ->get();

        return view('stock-adjustments.create', compact('cashierItems', 'warehouseItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target' => 'required|in:cashier,warehouse',
            'cashier_item_id' => 'required_if:target,cashier|nullable|exists:cashier_items,id',
            'warehouse_item_id' => 'required_if:target,warehouse|nullable|exists:warehouse_items,id',
            'type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'cashier_item_id.required_if' => 'Pilih item kasir yang ingin disesuaikan.',
            'warehouse_item_id.required_if' => 'Pilih item gudang yang ingin disesuaikan.',
        ]);

        $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';

        try {
            $result = DB::transaction(function () use ($validated) {
                $target = $validated['target'];

                if ($target === 'warehouse') {
                    // ===== PENYESUAIAN STOK GUDANG =====
                    $item = WarehouseItem::lockForUpdate()->findOrFail($validated['warehouse_item_id']);
                    $stockBefore = $item->stock;

                    if ($validated['type'] === 'decrease' && $item->stock < $validated['quantity']) {
                        throw new \Exception("Stok gudang tidak cukup! Stok saat ini: {$item->stock}");
                    }

                    if ($validated['type'] === 'increase') {
                        $item->increment('stock', $validated['quantity']);
                    } else {
                        $item->decrement('stock', $validated['quantity']);
                    }

                    $item->refresh();

                    $adjustment = StockAdjustment::create([
                        'target' => 'warehouse',
                        'warehouse_item_id' => $item->id,
                        'cashier_item_id' => null,
                        'type' => $validated['type'],
                        'quantity' => $validated['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $item->stock,
                        'reason' => $validated['reason'],
                        'notes' => $validated['notes'] ?? null,
                    ]);
                    // SEC: user_id set from auth — not via mass assignment
                    $adjustment->user_id = auth()->id();
                    $adjustment->save();

                    return [
                        'name' => $item->name,
                        'target_label' => 'Gudang',
                        'stockBefore' => $stockBefore,
                        'stockAfter' => $item->stock
                    ];
                } else {
                    // ===== PENYESUAIAN STOK KASIR (existing logic) =====
                    $item = CashierItem::lockForUpdate()->findOrFail($validated['cashier_item_id']);
                    $stockBefore = $item->stock;

                    if ($validated['type'] === 'decrease' && $item->stock < $validated['quantity']) {
                        throw new \Exception("Stok kasir tidak cukup! Stok saat ini: {$item->stock}");
                    }

                    if ($validated['type'] === 'increase') {
                        $item->increment('stock', $validated['quantity']);
                    } else {
                        $item->decrement('stock', $validated['quantity']);
                    }

                    $item->refresh();

                    $adjustment = StockAdjustment::create([
                        'target' => 'cashier',
                        'cashier_item_id' => $item->id,
                        'warehouse_item_id' => null,
                        'type' => $validated['type'],
                        'quantity' => $validated['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $item->stock,
                        'reason' => $validated['reason'],
                        'notes' => $validated['notes'] ?? null,
                    ]);
                    // SEC: user_id set from auth — not via mass assignment
                    $adjustment->user_id = auth()->id();
                    $adjustment->save();

                    return [
                        'name' => $item->name,
                        'target_label' => 'Kasir',
                        'stockBefore' => $stockBefore,
                        'stockAfter' => $item->stock
                    ];
                }
            });

            return redirect()->route($routePrefix . 'stock-adjustments.index')
                ->with('success', "Penyesuaian stok {$result['target_label']} berhasil. {$result['name']}: {$result['stockBefore']} → {$result['stockAfter']}");
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (!str_starts_with($errorMessage, 'Stok')) {
                $errorMessage = 'Terjadi kesalahan: ' . $errorMessage;
            }

            return redirect()->route($routePrefix . 'stock-adjustments.create')
                ->with('error', $errorMessage)
                ->withInput();
        }
    }
}
