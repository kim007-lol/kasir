<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        $search = $request->get('search');
        $query = Member::select('id', 'name', 'phone', 'address', 'user_id', 'created_at', 'deleted_at')
            ->withTrashed()
            ->with('user:id,username')
            ->withSum('transactions', 'total')
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(phone) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(address) LIKE ?', [$searchLower]);
                });
            })
            ->orderByRaw('transactions_sum_total DESC NULLS LAST')
            ->orderBy('name', 'asc');

        $members = $query->paginate(15);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('members.index', compact('members', 'search'));
            return $view->fragment('data-container');
        }
        return view('members.index', compact('members', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Member::create($validated);

        $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
        return redirect()->route($routePrefix . 'members.index')->with('success', 'Member berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): View
    {
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $member->update($validated);

        $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
        return redirect()->route($routePrefix . 'members.index')->with('success', 'Member berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
        return redirect()->route($routePrefix . 'members.index')->with('success', 'Member berhasil dihapus (Nonaktif)');
    }

    public function restore($id): RedirectResponse
    {
        $member = Member::withTrashed()->findOrFail($id);
        $member->restore();

        $routePrefix = auth()->user()->role === 'kasir' ? 'cashier.' : '';
        return redirect()->route($routePrefix . 'members.index')->with('success', 'Member berhasil dipulihkan (Aktif)');
    }
}
