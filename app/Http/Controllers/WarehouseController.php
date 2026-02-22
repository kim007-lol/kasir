<?php

namespace App\Http\Controllers;

use App\Models\WarehouseItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = WarehouseItem::select('id', 'code', 'name', 'category_id', 'supplier_id', 'purchase_price', 'selling_price', 'discount', 'stock', 'exp_date')
            ->with(['category:id,name', 'supplier:id,name'])
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(code) LIKE ?', [$searchLower])
                        ->orWhereHas('supplier', function ($sub) use ($searchLower) {
                            $sub->whereRaw('LOWER(name) LIKE ?', [$searchLower]);
                        });
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('code', 'asc');

        $warehouseItems = $query->paginate(15);
        $categories = Category::orderBy('name')->get();

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('warehouse.index', compact('warehouseItems', 'search', 'categories', 'categoryId'));
            return $view->fragment('data-container');
        }

        return view('warehouse.index', compact('warehouseItems', 'search', 'categories', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('warehouse.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'code' => 'required|string|max:50|unique:warehouse_items,code',
            'name' => 'required|string|max:150',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            // 'discount' => 'nullable|numeric|min:0', // Disabled 
            'stock' => 'required|integer|min:0',
            'exp_date' => 'nullable|date'
        ]);

        $validated['discount'] = 0; // Force discount to 0

        DB::transaction(function () use ($validated) {
            $warehouseItem = WarehouseItem::create($validated);

            // Catat stock entry jika ada stok awal
            if ($validated['stock'] > 0) {
                StockEntry::create([
                    'warehouse_item_id' => $warehouseItem->id,
                    'supplier_id' => $validated['supplier_id'],
                    'quantity' => $validated['stock'],
                    'entry_date' => now()->toDateString()
                ]);
            }
        });

        return redirect()->route('warehouse.index')->with('success', 'Barang gudang berhasil ditambahkan');
    }

    public function edit(WarehouseItem $warehouse): View
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('warehouse.edit', compact('warehouse', 'categories', 'suppliers'));
    }

    public function update(Request $request, WarehouseItem $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'code' => 'required|string|max:50|unique:warehouse_items,code,' . $warehouse->id,
            'name' => 'required|string|max:150',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            // 'discount' => 'nullable|numeric|min:0', // Disabled
            'stock' => 'required|integer|min:0',
            'exp_date' => 'nullable|date'
        ]);

        $validated['discount'] = 0; // Force discount to 0

        DB::transaction(function () use ($validated, $warehouse) {
            $oldStock = $warehouse->stock;
            $warehouse->update($validated);

            // Jika ada penambahan stok, catat di stock_entries
            $stockDifference = $validated['stock'] - $oldStock;
            if ($stockDifference > 0) {
                StockEntry::create([
                    'warehouse_item_id' => $warehouse->id,
                    'supplier_id' => $validated['supplier_id'],
                    'quantity' => $stockDifference,
                    'entry_date' => now()->toDateString()
                ]);
            }
        });

        return redirect()->route('warehouse.index')->with('success', 'Barang gudang berhasil diperbarui');
    }

    public function destroy(WarehouseItem $warehouse): RedirectResponse
    {
        // H5 Fix: Cek apakah ada item kasir aktif yang merujuk warehouse item ini
        $activeCashierItem = \App\Models\CashierItem::where('warehouse_item_id', $warehouse->id)
            ->where('stock', '>', 0)
            ->first();

        if ($activeCashierItem) {
            return redirect()->route('warehouse.index')->with(
                'error',
                "Tidak bisa menghapus. Item kasir \"{$activeCashierItem->name}\" masih memiliki stok ({$activeCashierItem->stock}). Kosongkan stok kasir terlebih dahulu."
            );
        }

        $warehouse->delete();
        return redirect()->route('warehouse.index')->with('success', 'Barang gudang berhasil dihapus');
    }
    public function getStockStatus(): \Illuminate\Http\JsonResponse
    {
        $stocks = WarehouseItem::select('id', 'stock')->get();
        return response()->json($stocks);
    }
}
