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

        $query = WarehouseItem::select('id', 'code', 'name', 'category_id', 'supplier_id', 'purchase_price', 'selling_price', 'discount', 'stock', 'exp_date')
            ->with(['category:id,name', 'supplier:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('code', 'ilike', '%' . $search . '%')
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'ilike', '%' . $search . '%');
                    });
            })
            ->orderBy('code', 'asc');

        $warehouseItems = $query->paginate(15);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('warehouse.index', compact('warehouseItems', 'search'));
            return $view->fragment('data-container');
        }

        return view('warehouse.index', compact('warehouseItems', 'search'));
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
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'exp_date' => 'nullable|date'
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;

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
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'exp_date' => 'nullable|date'
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;

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
        $warehouse->delete();
        return redirect()->route('warehouse.index')->with('success', 'Barang gudang berhasil dihapus');
    }
}
