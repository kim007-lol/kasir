<?php

namespace App\Http\Controllers;

use App\Models\CashierItem;
use App\Models\WarehouseItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CashierItemController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = CashierItem::select('id', 'code', 'name', 'stock', 'selling_price', 'discount', 'category_id', 'warehouse_item_id', 'is_consignment')
            ->with(['category:id,name', 'warehouseItem:id,stock'])
            ->where(function ($q) {
                // Non-consignment items: always show
                $q->where(function ($sub) {
                    $sub->where('is_consignment', false)->orWhereNull('is_consignment');
                })
                    // Consignment items: only show today's
                    ->orWhere(function ($sub) {
                        $sub->where('is_consignment', true)->whereDate('created_at', today());
                    });
            })
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(code) LIKE ?', [$searchLower]);
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('code', 'asc');

        $cashierItems = $query->paginate(15);
        $categories = \App\Models\Category::orderBy('name')->get();

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('cashier-items.index', compact('cashierItems', 'search', 'categories', 'categoryId'));
            return $view->fragment('data-container');
        }

        return view('cashier-items.index', compact('cashierItems', 'search', 'categories', 'categoryId'));
    }

    public function create(): View
    {
        $warehouseItems = WarehouseItem::with(['category', 'supplier'])
            ->where('stock', '>', 0)
            ->get();

        return view('cashier-items.create', compact('warehouseItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_item_id' => 'required|exists:warehouse_items,id',
            'quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $inputDiscount = $request->input('discount');

        try {
            DB::transaction(function () use ($validated, $inputDiscount) {
                // Lock warehouse item untuk update
                $warehouse = WarehouseItem::lockForUpdate()->findOrFail($validated['warehouse_item_id']);

                // Cek apakah diskon melebihi atau sama dengan harga jual
                if ($inputDiscount !== null && $inputDiscount >= $warehouse->final_price) {
                    throw new \Exception('Potongan harga (Rp ' . number_format($inputDiscount, 0, ',', '.') . ') tidak boleh melebihi atau sama dengan harga jual barang (Rp ' . number_format($warehouse->final_price, 0, ',', '.') . ').');
                }

                // Cek apakah stok cukup
                if ($warehouse->stock < $validated['quantity']) {
                    throw new \Exception('Stok gudang tidak cukup. Stok tersedia: ' . $warehouse->stock);
                }

                // Kurangi stok gudang
                $warehouse->decrement('stock', $validated['quantity']);

                // Cek apakah item sudah ada di kasir
                $cashierItem = CashierItem::where('warehouse_item_id', $warehouse->id)->first();

                if ($cashierItem) {
                    // Update stok kasir yang sudah ada
                    $cashierItem->increment('stock', $validated['quantity']);

                    // Siapkan array data update
                    $updateData = [
                        'selling_price' => $warehouse->final_price,
                        'name' => $warehouse->name,
                        'code' => $warehouse->code,
                    ];

                    // Jika user (admin) spesifik mengisi angka diskon, update nilai diskonnya
                    if ($inputDiscount !== null) {
                        $updateData['discount'] = (float) $inputDiscount;
                    }

                    // Sync harga dan atribut
                    $cashierItem->update($updateData);

                    // Log Transfer In
                    \App\Models\StockTransferLog::create([
                        'warehouse_item_id' => $warehouse->id,
                        'cashier_item_id' => $cashierItem->id,
                        'item_name' => $warehouse->name,
                        'item_code' => $warehouse->code,
                        'quantity' => $validated['quantity'],
                        'type' => 'transfer_in',
                        'notes' => 'Transfer dari Gudang (Admin)',
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    // Buat item kasir baru
                    $cashierItem = CashierItem::create([
                        'warehouse_item_id' => $warehouse->id,
                        'category_id' => $warehouse->category_id,
                        'supplier_id' => $warehouse->supplier_id,
                        'code' => $warehouse->code,
                        'name' => $warehouse->name,
                        'selling_price' => $warehouse->final_price,
                        'discount' => $inputDiscount !== null ? (float) $inputDiscount : 0,
                        'stock' => $validated['quantity']
                    ]);

                    // Log Transfer In (New)
                    \App\Models\StockTransferLog::create([
                        'warehouse_item_id' => $warehouse->id,
                        'cashier_item_id' => $cashierItem->id,
                        'item_name' => $warehouse->name,
                        'item_code' => $warehouse->code,
                        'quantity' => $validated['quantity'],
                        'type' => 'transfer_in',
                        'notes' => 'Transfer dari Gudang (Admin - Item Baru)',
                        'user_id' => auth()->id(),
                    ]);
                }
            });

            return redirect()->route('cashier-items.index')->with('success', 'Stok kasir berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(CashierItem $cashierItem): View
    {
        return view('cashier-items.edit', compact('cashierItem'));
    }

    public function update(Request $request, CashierItem $cashierItem): RedirectResponse
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $newStock = (int) $validated['stock'];

        // Default to current discount if not provided, or update if provided
        $newDiscount = isset($validated['discount']) ? (float)$validated['discount'] : $cashierItem->discount;
        $newExpiryDate = $validated['expiry_date'] ?? null;

        try {
            DB::transaction(function () use ($cashierItem, $newStock, $newDiscount, $newExpiryDate) {
                // Perbaikan Race Condition: Lock row cashierItem terlebih dahulu
                $lockedCashierItem = \App\Models\CashierItem::lockForUpdate()->find($cashierItem->id);

                $currentStock = (int) $lockedCashierItem->stock;
                $difference = $newStock - $currentStock;

                // Jika tidak ada perubahan stok (hanya diskon/tanggal), update dan selesai
                if ($difference === 0) {
                    $lockedCashierItem->update(['discount' => $newDiscount, 'expiry_date' => $newExpiryDate]);
                    return;
                }

                // Jika titipan atau tidak ada relasi gudang, langsung berbarui
                if ($lockedCashierItem->is_consignment || !$lockedCashierItem->warehouse_item_id) {
                    $lockedCashierItem->update([
                        'stock' => $newStock,
                        'discount' => $newDiscount,
                        'expiry_date' => $newExpiryDate
                    ]);
                    return;
                }

                // Kasus: Ada relasi gudang & ada perbedaan stok
                $warehouse = WarehouseItem::where('id', $lockedCashierItem->warehouse_item_id)
                    ->lockForUpdate()
                    ->first();

                if (!$warehouse) {
                    throw new \Exception('Item gudang tidak ditemukan.');
                }

                if ($difference > 0) {
                    // INCREASING cashier stock → must take from warehouse
                    if ($warehouse->stock < $difference) {
                        throw new \Exception(
                            "Stok gudang tidak cukup. Tersedia: {$warehouse->stock}, dibutuhkan: {$difference}"
                        );
                    }
                    $warehouse->decrement('stock', $difference);

                    // Log Edit Increase
                    \App\Models\StockTransferLog::create([
                        'warehouse_item_id' => $warehouse->id,
                        'cashier_item_id' => $lockedCashierItem->id,
                        'item_name' => $lockedCashierItem->name,
                        'item_code' => $lockedCashierItem->code,
                        'quantity' => $difference,
                        'type' => 'edit_increase',
                        'notes' => 'Edit stok manual (+)',
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    // DECREASING cashier stock → return to warehouse
                    $returnQty = abs($difference);
                    $warehouse->increment('stock', $returnQty);

                    // Log Edit Decrease
                    \App\Models\StockTransferLog::create([
                        'warehouse_item_id' => $warehouse->id,
                        'cashier_item_id' => $lockedCashierItem->id,
                        'item_name' => $lockedCashierItem->name,
                        'item_code' => $lockedCashierItem->code,
                        'quantity' => $returnQty,
                        'type' => 'edit_decrease',
                        'notes' => 'Edit stok manual (-)',
                        'user_id' => auth()->id(),
                    ]);
                }

                $lockedCashierItem->update([
                    'stock' => $newStock,
                    'discount' => $newDiscount,
                    'expiry_date' => $newExpiryDate
                ]);
            });

            return redirect()->route('cashier-items.index')->with('success', 'Data item dan stok kasir berhasil diperbarui dan disinkronkan dengan gudang jika relevan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(CashierItem $cashierItem): RedirectResponse
    {
        DB::transaction(function () use ($cashierItem) {
            // Kembalikan stok ke gudang
            $warehouse = $cashierItem->warehouseItem;
            if ($warehouse) {
                // Log Delete Return
                \App\Models\StockTransferLog::create([
                    'warehouse_item_id' => $warehouse->id,
                    'cashier_item_id' => $cashierItem->id,
                    'item_name' => $cashierItem->name,
                    'item_code' => $cashierItem->code,
                    'quantity' => $cashierItem->stock,
                    'type' => 'delete_return',
                    'notes' => 'Penghapusan item kasir',
                    'user_id' => auth()->id(),
                ]);

                $warehouse->increment('stock', $cashierItem->stock);
            }

            // Hapus item kasir
            $cashierItem->delete();
        });

        return redirect()->route('cashier-items.index')->with('success', 'Item kasir berhasil dihapus dan stok dikembalikan ke gudang');
    }
    public function cashierIndex(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = CashierItem::select('id', 'code', 'name', 'stock', 'selling_price', 'discount', 'category_id', 'warehouse_item_id', 'expiry_date', 'is_consignment', 'consignment_source')
            ->with(['category:id,name', 'warehouseItem:id,stock'])
            ->where(function ($q) {
                // Non-consignment items: always show
                $q->where(function ($sub) {
                    $sub->where('is_consignment', false)->orWhereNull('is_consignment');
                })
                    // Consignment items: only show today's
                    ->orWhere(function ($sub) {
                        $sub->where('is_consignment', true)->whereDate('created_at', today());
                    });
            })
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(code) LIKE ?', [$searchLower]);
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderByRaw('created_at DESC');

        $cashierItems = $query->paginate(15);
        $categories = \App\Models\Category::orderBy('name')->get();

        // Get warehouse items with stock > 0 for adding to cashier stock
        $warehouseItems = WarehouseItem::with(['category', 'supplier'])
            ->where('stock', '>', 0)
            ->orderBy('name', 'asc')
            ->get();

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('cashier.stock.index', compact('cashierItems', 'search', 'warehouseItems', 'categories', 'categoryId'));
            return $view->fragment('data-container');
        }

        return view('cashier.stock.index', compact('cashierItems', 'search', 'warehouseItems', 'categories', 'categoryId'));
    }

    public function storeFromWarehouse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_item_id' => 'required|exists:warehouse_items,id',
            'quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $inputDiscount = $request->input('discount');

        try {
            DB::transaction(function () use ($validated, $inputDiscount) {
                // Lock warehouse item for update
                $warehouse = WarehouseItem::lockForUpdate()->findOrFail($validated['warehouse_item_id']);

                // Cek apakah diskon melebihi atau sama dengan harga jual
                if ($inputDiscount !== null && $inputDiscount >= $warehouse->final_price) {
                    throw new \Exception('Potongan harga (Rp ' . number_format($inputDiscount, 0, ',', '.') . ') tidak boleh melebihi atau sama dengan harga jual barang (Rp ' . number_format($warehouse->final_price, 0, ',', '.') . ').');
                }

                // Check if stock is sufficient
                if ($warehouse->stock < $validated['quantity']) {
                    throw new \Exception('Stok gudang tidak cukup. Stok tersedia: ' . $warehouse->stock);
                }

                // Reduce warehouse stock
                $warehouse->decrement('stock', $validated['quantity']);

                // Check if item already exists in cashier
                $cashierItem = CashierItem::where('warehouse_item_id', $warehouse->id)->first();

                if ($cashierItem) {
                    // Update existing cashier stock
                    $cashierItem->increment('stock', $validated['quantity']);

                    // Siapkan array data update
                    $updateData = [
                        'selling_price' => $warehouse->final_price,
                        'name' => $warehouse->name,
                        'code' => $warehouse->code,
                    ];

                    // Jika user mengisi angka diskon, update nilai diskonnya
                    if ($inputDiscount !== null) {
                        $updateData['discount'] = (float) $inputDiscount;
                    }

                    // Sync harga dan atribut
                    $cashierItem->update($updateData);

                    // Log Transfer In
                    \App\Models\StockTransferLog::create([
                        'warehouse_item_id' => $warehouse->id,
                        'cashier_item_id' => $cashierItem->id,
                        'item_name' => $warehouse->name,
                        'item_code' => $warehouse->code,
                        'quantity' => $validated['quantity'],
                        'type' => 'transfer_in',
                        'notes' => 'Transfer dari Gudang (Kasir)',
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    // Create new cashier item
                    $cashierItem = CashierItem::create([
                        'warehouse_item_id' => $warehouse->id,
                        'category_id' => $warehouse->category_id,
                        'supplier_id' => $warehouse->supplier_id,
                        'code' => $warehouse->code,
                        'name' => $warehouse->name,
                        'selling_price' => $warehouse->final_price,
                        'discount' => $inputDiscount !== null ? (float) $inputDiscount : 0,
                        'stock' => $validated['quantity']
                    ]);

                    // Log Transfer In (New)
                    \App\Models\StockTransferLog::create([
                        'warehouse_item_id' => $warehouse->id,
                        'cashier_item_id' => $cashierItem->id,
                        'item_name' => $warehouse->name,
                        'item_code' => $warehouse->code,
                        'quantity' => $validated['quantity'],
                        'type' => 'transfer_in',
                        'notes' => 'Transfer dari Gudang (Kasir - Item Baru)',
                        'user_id' => auth()->id(),
                    ]);
                }
            });

            return redirect()->route('cashier.stock.index')->with('success', 'Stok kasir berhasil ditambahkan dari gudang');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeConsignment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'selling_price' => 'required|numeric|min:0|gte:cost_price',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'consignment_source' => 'required|string|max:150',
        ], [
            'selling_price.gte' => 'Harga jual tidak boleh lebih rendah dari harga modal.',
        ]);

        do {
            $code = 'CSG-' . date('ymdHis') . '-' . rand(10, 99);
        } while (CashierItem::where('code', $code)->exists());

        CashierItem::create([
            'code' => $code,
            'name' => $validated['name'],
            'selling_price' => $validated['selling_price'],
            'cost_price' => $validated['cost_price'],
            'stock' => $validated['stock'],
            'consignment_source' => $validated['consignment_source'],
            'is_consignment' => true,
            'discount' => 0,
            'warehouse_item_id' => null,
            'category_id' => null,
            'supplier_id' => null,
        ]);

        return redirect()->back()->with('success', 'Barang titipan berhasil ditambahkan');
    }
    public function getStockStatus(): \Illuminate\Http\JsonResponse
    {
        $stocks = CashierItem::select('id', 'stock')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('is_consignment', false)->orWhereNull('is_consignment');
                })
                    ->orWhere(function ($sub) {
                        $sub->where('is_consignment', true)->whereDate('created_at', today());
                    });
            })
            ->get();
        return response()->json($stocks);
    }

    public function consignmentIndex(Request $request): View
    {
        $filterDate = $request->get('date', today()->toDateString());
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = CashierItem::where('is_consignment', true);

        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
            $filterDate = null; // range mode
        } else {
            $query->whereDate('created_at', $filterDate);
        }

        $consignmentItems = $query->orderBy('created_at', 'asc')->paginate(15);

        return view('cashier.consignment.index', compact('consignmentItems', 'filterDate', 'startDate', 'endDate'));
    }

    public function editConsignment(CashierItem $cashierItem): View|RedirectResponse
    {
        if (!$cashierItem->is_consignment) {
            abort(404);
        }

        if (!$cashierItem->created_at->isToday()) {
            return redirect()->route('cashier.consignment.index')->with('error', 'Item titipan hari lalu tidak dapat diedit.');
        }

        return view('cashier.consignment.edit', compact('cashierItem'));
    }

    public function updateConsignment(Request $request, CashierItem $cashierItem): RedirectResponse
    {
        if (!$cashierItem->is_consignment) {
            abort(404);
        }

        if (!$cashierItem->created_at->isToday()) {
            return redirect()->route('cashier.consignment.index')->with('error', 'Item titipan hari lalu tidak dapat diubah.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'selling_price' => 'required|numeric|min:0|gte:cost_price',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'consignment_source' => 'required|string|max:150',
        ], [
            'selling_price.gte' => 'Harga jual tidak boleh lebih rendah dari harga modal.',
        ]);

        $cashierItem->update($validated);

        return redirect()->route('cashier.consignment.index')->with('success', 'Barang titipan berhasil diperbarui');
    }

    public function destroyConsignment(CashierItem $cashierItem): RedirectResponse
    {
        if (!$cashierItem->is_consignment) {
            abort(404);
        }

        if (!$cashierItem->created_at->isToday()) {
            return redirect()->route('cashier.consignment.index')->with('error', 'Item titipan hari lalu tidak dapat dihapus.');
        }

        $cashierItem->delete();

        return redirect()->route('cashier.consignment.index')->with('success', 'Barang titipan berhasil dihapus');
    }
}
