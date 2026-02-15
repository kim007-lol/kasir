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

class TransactionController extends Controller
{
    public function index(Request $request): View|\Illuminate\Support\HtmlString|string
    {
        // Clear cart on fresh page load (Refresh / Navigation) unless we have flash messages (redirects)
        if (
            !$request->ajax() && empty($request->query()) &&
            !session()->has('success') && !session()->has('error') &&
            !session()->has('warning') && !session()->has('info')
        ) {
            session()->forget('cart');
            session()->forget('last_transaction_id');
        }

        $search = $request->get('search');

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
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', '%' . $search . '%')
                        ->orWhere('code', 'ilike', '%' . $search . '%');
                });
            })
            ->orderBy('code', 'asc');

        $items = $query->paginate(15);
        $members = Member::select('id', 'name')->orderBy('name')->get();
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('transactions.index', compact('items', 'members', 'cart', 'total', 'search'));
            return $view->fragment('product-list');
        }

        return view('transactions.index', compact('items', 'members', 'cart', 'total', 'search'));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:cashier_items,id',
            'qty' => 'required|integer|min:1'
        ]);

        $item = CashierItem::find($validated['item_id']);

        if (!$item) {
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        if ($item->is_consignment && !$item->created_at->isToday()) {
            $route = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';
            return redirect()->route($route)->with('error', 'Item titipan kadaluarsa.');
        }

        $cart = session()->get('cart', []);
        $itemId = $validated['item_id'];

        $currentQty = isset($cart[$itemId]) ? $cart[$itemId]['qty'] : 0;
        $newQty = $validated['qty'];

        if ($item->stock < ($currentQty + $newQty)) {
            $route = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';
            return redirect()->route($route)->with('error', "Stok tidak cukup. Sisa stok: {$item->stock}. Di keranjang: {$currentQty}");
        }

        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] += $validated['qty'];
        } else {
            $originalPrice = $item->selling_price + $item->discount;

            $cart[$itemId] = [
                'item_id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'price' => $item->selling_price, // Final price after discount
                'original_price' => $originalPrice,
                'discount' => $item->discount,
                'qty' => $validated['qty']
            ];
        }

        session()->put('cart', $cart);

        $route = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';
        return redirect()->route($route)->with('success', 'Item ditambahkan ke keranjang');
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

            // Cek stok
            if ($item->stock < $qty) {
                $errors[] = "{$item->name}: Stok tidak cukup (tersedia: {$item->stock})";
                continue;
            }

            // Tambah ke keranjang
            $itemId = $itemData['item_id'];
            if (isset($cart[$itemId])) {
                $cart[$itemId]['qty'] += $qty;
            } else {
                $originalPrice = $item->selling_price + $item->discount;

                $cart[$itemId] = [
                    'item_id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'price' => $item->selling_price, // Final price after discount
                    'original_price' => $originalPrice,
                    'discount' => $item->discount,
                    'qty' => $qty
                ];
            }
            $addedCount++;
        }

        session()->put('cart', $cart);

        $route = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';

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
        $cart = session()->get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }

        $route = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';
        return redirect()->route($route)->with('success', 'Item dihapus dari keranjang');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris',
            'discount_amount' => 'nullable|numeric|min:0', // Optional global discount in Rupiah
        ]);

        $cart = session()->get('cart', []);

        $routeIndex = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';

        if (empty($cart)) {
            return redirect()->route($routeIndex)->with('error', 'Keranjang kosong');
        }

        $grossTotal = $this->calculateTotal($cart);

        // Calculate Discount
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $netTotal = $grossTotal - $discountAmount;

        $paidAmount = (float) $validated['paid_amount'];
        $changeAmount = $paidAmount - $netTotal;

        // Validasi stok sebelum memulai transaksi database
        $stockErrors = [];
        foreach ($cart as $itemId => $item) {
            $product = CashierItem::find($itemId);
            if (!$product || $product->stock < $item['qty']) {
                $stockErrors[] = ($product ? $product->name : 'Unknown Item') . ': stok tidak cukup (tersedia: ' . ($product ? $product->stock : 0) . ')';
            }
        }

        if (!empty($stockErrors)) {
            return redirect()->route($routeIndex)
                ->with('error', implode(', ', $stockErrors));
        }

        // Validasi pembayaran
        if ($paidAmount < $netTotal) {
            return redirect()->route($routeIndex)
                ->with('error', 'Uang pembayaran kurang! Total: Rp ' . number_format($netTotal, 0, ',', '.') . ', Dibayar: Rp ' . number_format($paidAmount, 0, ',', '.'));
        }

        try {
            $invoice = 'INV-' . date('YmdHis');

            $transactionId = null;

            DB::transaction(function () use ($cart, $validated, $invoice, $grossTotal, $netTotal, $discountAmount, $paidAmount, $changeAmount, &$transactionId) {
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
                    'user_id' => auth()->id()
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

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $item['item_id'],
                        'price' => $item['price'], // Final price
                        'original_price' => $item['original_price'] ?? $item['price'],
                        'discount' => $item['discount'] ?? 0,
                        'qty' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty']
                    ]);

                    $product->decrement('stock', $item['qty']);
                }

                session()->forget('cart');
            });

            session()->put('last_transaction_id', $transactionId);

            $routeReceipt = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.receipt' : 'transactions.receipt';
            return redirect()->route($routeReceipt)->with('success', 'Transaksi berhasil disimpan');
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
            $routeIndex = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';
            return redirect()->route($routeIndex)->with('error', 'Tidak ada transaksi');
        }

        $details = TransactionDetail::where('transaction_id', $lastTransaction->id)->with('item')->get();

        return view('transactions.receipt', compact('lastTransaction', 'details'));
    }

    public function downloadReceipt($id): View|RedirectResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            $routeIndex = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.transactions.index' : 'transactions.index';
            return redirect()->route($routeIndex)->with('error', 'Transaksi tidak ditemukan');
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
