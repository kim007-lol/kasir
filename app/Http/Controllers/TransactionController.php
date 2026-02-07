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
    public function index(): View
    {
        $items = CashierItem::all();
        $members = Member::all();
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        return view('transactions.index', compact('items', 'members', 'cart', 'total'));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:cashier_items,id',
            'qty' => 'required|integer|min:1'
        ]);

        $item = CashierItem::find($validated['item_id']);

        if ($item->stock < $validated['qty']) {
            return redirect()->route('transactions.index')->with('error', 'Stok tidak cukup');
        }

        $cart = session()->get('cart', []);
        $itemId = $validated['item_id'];

        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] += $validated['qty'];
        } else {
            $cart[$itemId] = [
                'item_id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'price' => $item->selling_price,
                'qty' => $validated['qty']
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('transactions.index')->with('success', 'Item ditambahkan ke keranjang');
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
                $cart[$itemId] = [
                    'item_id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'price' => $item->selling_price,
                    'qty' => $qty
                ];
            }
            $addedCount++;
        }

        session()->put('cart', $cart);

        // Build response message
        if ($addedCount > 0 && empty($errors)) {
            $message = "{$addedCount} item berhasil ditambahkan ke keranjang";
            return redirect()->route('transactions.index')->with('success', $message);
        } elseif ($addedCount > 0 && !empty($errors)) {
            $message = "{$addedCount} item berhasil ditambahkan, tapi beberapa item gagal: " . implode('; ', $errors);
            return redirect()->route('transactions.index')->with('warning', $message);
        } else {
            return redirect()->route('transactions.index')->with('error', 'Tidak ada item yang ditambahkan. ' . implode('; ', $errors));
        }
    }

    public function removeFromCart($itemId): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }

        return redirect()->route('transactions.index')->with('success', 'Item dihapus dari keranjang');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'paid_amount' => 'required|numeric|min:0'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('transactions.index')->with('error', 'Keranjang kosong');
        }

        $total = $this->calculateTotal($cart);
        $paidAmount = (float) $validated['paid_amount'];
        $changeAmount = $paidAmount - $total;

        // Validasi pembayaran
        if ($paidAmount < $total) {
            return redirect()->route('transactions.index')
                ->with('error', 'Uang pembayaran kurang! Total: Rp ' . number_format($total, 0, ',', '.') . ', Dibayar: Rp ' . number_format($paidAmount, 0, ',', '.'));
        }

        try {
            $invoice = 'INV-' . date('YmdHis');

            $transactionId = null;

            DB::transaction(function () use ($cart, $validated, $invoice, $total, $paidAmount, $changeAmount, &$transactionId) {
                // Determine customer name based on member_id
                $customerName = 'Non Member';
                if ($validated['member_id']) {
                    $member = Member::find($validated['member_id']);
                    $customerName = $member->name ?? 'Non Member';
                }

                $transaction = Transaction::create([
                    'invoice' => $invoice,
                    'customer_name' => $customerName,
                    'member_id' => $validated['member_id'] ?? null,
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => 'cash',
                    'user_id' => auth()->id()
                ]);

                $transactionId = $transaction->id;

                foreach ($cart as $itemId => $item) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $item['item_id'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty']
                    ]);

                    CashierItem::find($itemId)->decrement('stock', $item['qty']);
                }

                session()->forget('cart');
            });

            session()->put('last_transaction_id', $transactionId);

            return redirect()->route('transactions.receipt')->with('success', 'Transaksi berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            return redirect()->route('transactions.index')->with('error', 'Tidak ada transaksi');
        }

        $details = TransactionDetail::where('transaction_id', $lastTransaction->id)->with('item')->get();

        return view('transactions.receipt', compact('lastTransaction', 'details'));
    }

    public function downloadReceipt($id): View|RedirectResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return redirect()->route('transactions.index')->with('error', 'Transaksi tidak ditemukan');
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
