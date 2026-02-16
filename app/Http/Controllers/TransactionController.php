<?php

namespace App\Http\Controllers;

use App\Models\CashierItem;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;

class TransactionController extends Controller
{
    // ... existing code ...

    // ... existing code ...
    private function getRoutePrefix(): string
    {
        return (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.' : '';
    }

    private function routeIndex(): string
    {
        return $this->getRoutePrefix() . 'transactions.index';
    }

    private function routeReceipt(): string
    {
        return $this->getRoutePrefix() . 'transactions.receipt';
    }

    public function index(Request $request): View|\Illuminate\Support\HtmlString|string|RedirectResponse|\Illuminate\Http\JsonResponse
    {
        // Clear cart ONLY if it's a clean entry from another page (Referer is different)
        // and there are no flash messages or active search/page queries.
        $referer = $request->headers->get('referer');
        $currentUrl = $request->url();

        // If coming from receipt, we MUST ensure freshness
        $isFromReceipt = $referer && str_contains($referer, 'struk');
        $isForwardNavigation = !$referer || !str_contains($referer, $currentUrl);

        if (
            !$request->ajax() &&
            ($isForwardNavigation || $isFromReceipt) &&
            empty($request->query()) &&
            !session()->has('success') && !session()->has('error') &&
            !session()->has('warning') && !session()->has('info')
        ) {
            session()->forget('cart');
            session()->forget('last_transaction_id');
        }

        $search = trim($request->get('search'));

        // Feature: Barcode Auto-Add (Exact Match)
        if ($search) {
            $exactItem = CashierItem::where('code', $search)
                ->where('stock', '>', 0)
                ->where(function ($q) {
                    $q->where('is_consignment', false)->orWhereNull('is_consignment')
                        ->orWhere(function ($sub) {
                            $sub->where('is_consignment', true)->whereDate('created_at', today());
                        });
                })
                ->first();

            if ($exactItem) {
                $cart = session()->get('cart', []);
                $itemId = $exactItem->id;

                // C4 Fix: Validasi stok sebelum menambah via barcode
                $currentQty = isset($cart[$itemId]) ? $cart[$itemId]['qty'] : 0;
                if ($exactItem->stock < ($currentQty + 1)) {
                    $route = $this->routeIndex();
                    if ($request->ajax()) {
                        return response()->json([
                            'auto_added' => false,
                            'redirect_url' => route($route) . '?error=' . urlencode("Stok tidak cukup. Sisa stok: {$exactItem->stock}")
                        ]);
                    }
                    return redirect()->route($route)->with('error', "Stok tidak cukup. Sisa: {$exactItem->stock}. Di keranjang: {$currentQty}");
                }

                if (isset($cart[$itemId])) {
                    $cart[$itemId]['qty'] += 1;
                } else {
                    $cart[$itemId] = [
                        'item_id' => $exactItem->id,
                        'code' => $exactItem->code,
                        'name' => $exactItem->name,
                        'price' => $exactItem->final_price,
                        'original_price' => $exactItem->selling_price,
                        'discount' => $exactItem->discount,
                        'qty' => 1
                    ];
                }

                session()->put('cart', $cart);

                $route = $this->routeIndex();

                if ($request->ajax()) {
                    return response()->json([
                        'auto_added' => true,
                        'redirect_url' => route($route) . '?success=' . urlencode("Item {$exactItem->name} ditambahkan")
                    ]);
                }

                return redirect()->route($route)->with('success', "Item {$exactItem->name} ditambahkan via Barcode");
            }
        }

        $query = CashierItem::select('id', 'code', 'name', 'stock', 'selling_price', 'discount')
            ->where('stock', '>', 0)
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
            ->orderBy('code', 'asc');

        $items = $query->paginate(15);
        $members = Member::select('id', 'name')->orderBy('name')->get();
        // C5 Feature: Manual Cashier Selection
        $cashiers = User::whereIn('role', ['admin', 'kasir'])->orderBy('name')->get();

        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('transactions.index', compact('items', 'members', 'cashiers', 'cart', 'total', 'search'));
            return $view->fragment('product-list');
        }

        return view('transactions.index', compact('items', 'members', 'cashiers', 'cart', 'total', 'search'));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:cashier_items,id',
            'qty' => 'required|integer|min:1'
        ]);

        $item = CashierItem::find($validated['item_id']);

        if (!$item) {
            return redirect()->route($this->routeIndex())->with('error', 'Item tidak ditemukan.');
        }

        if ($item->is_consignment && !$item->created_at->isToday()) {
            return redirect()->route($this->routeIndex())->with('error', 'Item titipan kadaluarsa.');
        }

        $cart = session()->get('cart', []);
        $itemId = $validated['item_id'];

        $currentQty = isset($cart[$itemId]) ? $cart[$itemId]['qty'] : 0;
        $newQty = $validated['qty'];

        if ($item->stock < ($currentQty + $newQty)) {
            return redirect()->route($this->routeIndex())->with('error', "Stok tidak cukup. Sisa stok: {$item->stock}. Di keranjang: {$currentQty}");
        }

        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] += $validated['qty'];
        } else {
            $cart[$itemId] = [
                'item_id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'price' => $item->final_price, // Final price after discount
                'original_price' => $item->selling_price,
                'discount' => $item->discount,
                'qty' => $validated['qty']
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route($this->routeIndex())->with('success', 'Item ditambahkan ke keranjang');
    }

    /**
     * Menambahkan beberapa item sekaligus ke keranjang
     */
    public function addMultipleToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:cashier_items,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $addedCount = 0;
        $errors = [];

        foreach ($validated['items'] as $itemData) {
            $item = CashierItem::find($itemData['item_id']);
            $qty = (int) $itemData['qty'];

            // H2 Fix: Cek stok termasuk qty yang sudah ada di keranjang
            $currentQty = isset($cart[$itemData['item_id']]) ? $cart[$itemData['item_id']]['qty'] : 0;
            if ($item->stock < ($currentQty + $qty)) {
                $errors[] = "{$item->name}: Stok tidak cukup (tersedia: {$item->stock}, di keranjang: {$currentQty})";
                continue;
            }

            // Cek item titipan kadaluarsa (Refresh harian)
            if ($item->is_consignment && !$item->created_at->isToday()) {
                $errors[] = "{$item->name}: Item titipan kadaluarsa.";
                continue;
            }

            // Tambah ke keranjang
            $itemId = $itemData['item_id'];
            if (isset($cart[$itemId])) {
                $cart[$itemId]['qty'] += $qty;
            } else {
                $cart[$itemId] = [
                    'item_id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'price' => $item->final_price, // Final price after discount
                    'original_price' => $item->selling_price,
                    'discount' => $item->discount,
                    'qty' => $qty
                ];
            }
            $addedCount++;
        }

        session()->put('cart', $cart);

        $route = $this->routeIndex();

        // Build response message
        if ($addedCount > 0 && empty($errors)) {
            $message = "{$addedCount} item berhasil ditambahkan ke keranjang";
            return redirect()->route($route)->with('success', $message);
        } elseif ($addedCount > 0 && !empty($errors)) {
            $message = "{$addedCount} item berhasil ditambahkan, tapi beberapa item gagal: " . implode('; ', $errors);
            return redirect()->route($route)->with('warning', $message);
        } else {
            return redirect()->route($route)->with('error', 'Tidak ada item yang ditambahkan. ' . implode('; ', $errors));
        }
    }

    public function removeFromCart($itemId): RedirectResponse
    {
        // M2 Fix: Validasi itemId sebagai integer
        if (!is_numeric($itemId) || (int) $itemId <= 0) {
            return redirect()->route($this->routeIndex())->with('error', 'Item ID tidak valid.');
        }

        $itemId = (int) $itemId;
        $cart = session()->get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }

        return redirect()->route($this->routeIndex())->with('success', 'Item dihapus dari keranjang');
    }

    public function clearCart(): RedirectResponse
    {
        session()->forget('cart');
        return redirect()->route($this->routeIndex())->with('success', 'Keranjang berhasil dibersihkan');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris',
            'discount_amount' => 'nullable|numeric|min:0',
            'cashier_name' => 'required|string|max:255', // C5 Revert: Manual Cashier Name Input (User Request)
            '_checkout_token' => 'required|string', // M3: Idempotency token
        ]);

        $routeIndex = $this->routeIndex();

        // M3 Fix: Cegah double-submit checkout
        $checkoutToken = $validated['_checkout_token'];
        $sessionTokenKey = 'checkout_token';
        if (session()->get($sessionTokenKey) === $checkoutToken) {
            return redirect()->route($routeIndex)->with('error', 'Transaksi sudah diproses. Silakan buat transaksi baru.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route($routeIndex)->with('error', 'Keranjang kosong');
        }

        // M1 Fix: Hitung grossTotal dari harga database, bukan session
        $grossTotal = 0;
        $stockErrors = [];
        foreach ($cart as $itemId => $item) {
            $product = CashierItem::find($itemId);
            if (!$product) {
                $stockErrors[] = 'Unknown Item: item tidak ditemukan';
                continue;
            }
            if ($product->stock < $item['qty']) {
                $stockErrors[] = "{$product->name}: stok tidak cukup (tersedia: {$product->stock})";
                continue;
            }
            // C4 Fix: Validasi harga jual > 0
            if ($product->selling_price <= 0) {
                $stockErrors[] = "{$product->name}: harga jual tidak valid (Rp 0). Hubungi admin.";
                continue;
            }
            $grossTotal += $product->final_price * $item['qty'];
        }

        if (!empty($stockErrors)) {
            return redirect()->route($routeIndex)
                ->with('error', implode(', ', $stockErrors));
        }

        // Calculate Discount
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);

        // Security: Only Admin can apply discount
        if (auth()->check() && auth()->user()->role !== 'admin') {
            $discountAmount = 0;
        }

        // Security: Discount cannot exceed gross total
        if ($discountAmount > $grossTotal) {
            return redirect()->route($routeIndex)
                ->with('error', 'Diskon tidak boleh melebihi total belanja (Rp ' . number_format($grossTotal, 0, ',', '.') . ')');
        }

        $netTotal = $grossTotal - $discountAmount;

        $paidAmount = (float) $validated['paid_amount'];
        $changeAmount = $paidAmount - $netTotal;

        // Validasi pembayaran
        if ($paidAmount < $netTotal) {
            return redirect()->route($routeIndex)
                ->with('error', 'Uang pembayaran kurang! Total: Rp ' . number_format($netTotal, 0, ',', '.') . ', Dibayar: Rp ' . number_format($paidAmount, 0, ',', '.'));
        }

        try {
            // C2 Fix: Tambah random suffix untuk mencegah invoice collision
            $invoice = 'INV-' . date('YmdHis') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $transactionId = null;

            DB::transaction(function () use ($cart, $validated, $invoice, $grossTotal, $netTotal, $discountAmount, $paidAmount, $changeAmount, &$transactionId, $sessionTokenKey, $checkoutToken) {
                // Determine customer name based on member_id
                $customerName = 'Non Member';
                if (!empty($validated['member_id'])) {
                    $member = Member::find($validated['member_id']);
                    $customerName = $member->name ?? 'Non Member';
                }

                $transaction = Transaction::create([
                    'invoice' => $invoice,
                    'customer_name' => $customerName,
                    'member_id' => $validated['member_id'] ?? null,
                    'total' => $netTotal, // Store Net Total
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $validated['payment_method'],
                    'discount_percent' => 0,
                    'discount_amount' => $discountAmount,
                    'user_id' => auth()->id(), // System Operator
                    'cashier_name' => $validated['cashier_name'] // Manual Input Name
                ]);

                $transactionId = $transaction->id;

                foreach ($cart as $itemId => $item) {
                    // Lock row for update to prevent race conditions
                    $product = CashierItem::where('id', $itemId)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \Exception("Item ID {$itemId} tidak ditemukan.");
                    }

                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stok {$product->name} tidak cukup saat pemrosesan akhir. Sisa: {$product->stock}");
                    }

                    // C3 Fix: Gunakan harga terkini dari database, bukan dari session
                    $currentPrice = $product->final_price;
                    $currentOriginalPrice = $product->selling_price;
                    $currentDiscount = $product->discount;

                    $purchasePrice = 0;
                    if ($product->is_consignment) {
                        $purchasePrice = $product->cost_price ?? 0;
                    } else {
                        $purchasePrice = $product->warehouseItem?->purchase_price ?? 0;
                    }

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $item['item_id'],
                        'price' => $currentPrice,
                        'original_price' => $currentOriginalPrice,
                        'discount' => $currentDiscount,
                        'qty' => $item['qty'],
                        'subtotal' => $currentPrice * $item['qty'],
                        'purchase_price' => $purchasePrice
                    ]);

                    $product->decrement('stock', $item['qty']);
                }

                session()->forget('cart');

                // M3 Fix: Simpan token untuk mencegah double-submit
                session()->put($sessionTokenKey, $checkoutToken);
            });

            session()->put('last_transaction_id', $transactionId);

            return redirect()->route($this->routeReceipt())->with('success', 'Transaksi berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route($routeIndex)->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function receipt(): View|RedirectResponse
    {
        $transactionId = session()->get('last_transaction_id');

        if (!$transactionId) {
            $lastTransaction = Transaction::where('user_id', auth()->id())->latest()->first();
        } else {
            $lastTransaction = Transaction::find($transactionId);
        }

        if (!$lastTransaction) {
            return redirect()->route($this->routeIndex())->with('error', 'Tidak ada transaksi');
        }

        // H1 Fix: Ownership check — kasir hanya bisa lihat transaksi sendiri
        if (auth()->user()->role !== 'admin' && $lastTransaction->user_id !== auth()->id()) {
            return redirect()->route($this->routeIndex())->with('error', 'Anda tidak memiliki akses untuk melihat struk ini.');
        }

        $details = TransactionDetail::where('transaction_id', $lastTransaction->id)->with('item')->get();

        return view('transactions.receipt', compact('lastTransaction', 'details'));
    }

    public function downloadReceipt($id): View|RedirectResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return redirect()->route($this->routeIndex())->with('error', 'Transaksi tidak ditemukan');
        }

        // Keamanan: Cek hak akses (IDOR Fix)
        // Kasir hanya bisa melihat transaksi miliknya sendiri, Admin bisa melihat semua
        if (auth()->user()->role !== 'admin' && $transaction->user_id !== auth()->id()) {
            return redirect()->route($this->routeIndex())->with('error', 'Anda tidak memiliki akses untuk mengunduh struk ini.');
        }


        $details = TransactionDetail::where('transaction_id', $transaction->id)->with('item')->get();

        return view('transactions.receipt-pdf', compact('transaction', 'details'));
    }

    private function calculateTotal($cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}
