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
        $query = CashierItem::select('id', 'code', 'name', 'stock', 'selling_price', 'discount', 'category_id', 'warehouse_item_id')
            ->with(['category:id,name', 'warehouseItem:id,stock'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('code', 'ilike', '%' . $search . '%');
            })
            ->orderBy('code', 'asc');

        $cashierItems = $query->paginate(15);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('cashier-items.index', compact('cashierItems', 'search'));
            return $view->fragment('data-container');
        }

        return view('cashier-items.index', compact('cashierItems', 'search'));
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
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Lock warehouse item untuk update
                $warehouse = WarehouseItem::lockForUpdate()->findOrFail($validated['warehouse_item_id']);

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

                    // Sync harga dan diskon jika ada perubahan
                    $cashierItem->update([
                        'selling_price' => $warehouse->final_price,
                        'name' => $warehouse->name,
                        'code' => $warehouse->code,
                        'discount' => $warehouse->discount,
                    ]);
                } else {
                    // Buat item kasir baru
                    CashierItem::create([
                        'warehouse_item_id' => $warehouse->id,
                        'category_id' => $warehouse->category_id,
                        'supplier_id' => $warehouse->supplier_id,
                        'code' => $warehouse->code,
                        'name' => $warehouse->name,
                        'selling_price' => $warehouse->final_price,
                        'discount' => $warehouse->discount,
                        'stock' => $validated['quantity']
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
            'stock' => 'required|integer|min:0'
        ]);

        $cashierItem->update($validated);

        return redirect()->route('cashier-items.index')->with('success', 'Stok kasir berhasil diperbarui');
    }

    public function destroy(CashierItem $cashierItem): RedirectResponse
    {
        DB::transaction(function () use ($cashierItem) {
            // Kembalikan stok ke gudang
            $warehouse = $cashierItem->warehouseItem;
            $warehouse->increment('stock', $cashierItem->stock);

            // Hapus item kasir
            $cashierItem->delete();
        });

        return redirect()->route('cashier-items.index')->with('success', 'Item kasir berhasil dihapus dan stok dikembalikan ke gudang');
    }
}
