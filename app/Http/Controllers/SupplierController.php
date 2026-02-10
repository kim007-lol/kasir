<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $search = $request->get('search');
        $query = Supplier::select('id', 'name', 'phone', 'email', 'address', 'contract_date', 'deleted_at')
            ->withTrashed()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('phone', 'ilike', '%' . $search . '%')
                    ->orWhere('email', 'ilike', '%' . $search . '%')
                    ->orWhere('address', 'ilike', '%' . $search . '%');
            })
            ->orderBy('contract_date', 'asc');

        $suppliers = $query->paginate(15);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('suppliers.index', compact('suppliers', 'search'));
            return $view->fragment('data-container');
        }

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('suppliers')->where(function ($query) use ($request) {
                    return $query->where('address', $request->address);
                })
            ],
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100|unique:suppliers,email',
            'address' => 'required|string',
            'contract_date' => 'required|date'
        ], [
            'name.unique' => 'Supplier dengan nama dan alamat yang sama sudah ada.',
            'email.unique' => 'Email ini sudah terdaftar untuk supplier lain.'
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('suppliers')->where(function ($query) use ($request) {
                    return $query->where('address', $request->address);
                })->ignore($supplier->id)
            ],
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100|unique:suppliers,email,' . $supplier->id,
            'address' => 'required|string',
            'contract_date' => 'required|date'
        ], [
            'name.unique' => 'Supplier dengan nama dan alamat yang sama sudah ada.',
            'email.unique' => 'Email ini sudah terdaftar untuk supplier lain.'
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil di non-aktifkan');
    }

    public function restore($id): RedirectResponse
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);
        $supplier->restore();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diaktifkan kembali');
    }
}
