<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $search = $request->get('search');
        $query = Category::select('id', 'name', 'deleted_at')
            ->withTrashed()
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->whereRaw('LOWER(name) LIKE ?', [$searchLower]);
            })
            ->orderBy('name', 'asc');

        $categories = $query->paginate(15);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('categories.index', compact('categories', 'search'));
            return $view->fragment('data-container');
        }
        return view('categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', \Illuminate\Validation\Rule::unique('categories', 'name')->withoutTrashed()]
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', \Illuminate\Validation\Rule::unique('categories', 'name')->withoutTrashed()->ignore($category->id)]
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil di non-aktifkan');
    }

    public function restore($id): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diaktifkan kembali');
    }
}
