<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CashierItem;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierBookingController extends Controller
{
    /**
     * List all bookings with tab filter
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = Booking::with(['items', 'user'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15);
        $pendingCount = Booking::pending()->count();

        // Counts per status for tab badges
        $statusCounts = [
            'pending' => Booking::pending()->count(),
            'confirmed' => Booking::confirmed()->count(),
            'processing' => Booking::processing()->count(),
            'ready' => Booking::ready()->count(),
        ];

        return view('cashier.bookings.index', compact('bookings', 'status', 'pendingCount', 'statusCounts'));
    }

    /**
     * Show booking detail
     */
    public function show(Booking $booking)
    {
        $booking->load(['items.cashierItem', 'user.member']);
        return view('cashier.bookings.show', compact('booking'));
    }

    /**
     * Accept a pending booking → confirmed + deduct stock
     */
    public function accept(Booking $booking)
    {
        if (!$booking->canBeAccepted()) {
            return back()->with('error', 'Pesanan ini tidak bisa diterima (status: ' . $booking->status_label . ')');
        }

        try {
            DB::transaction(function () use ($booking) {
                // FIX BUG-R2 #2: Lock booking untuk mencegah double-accept
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                if (!$lockedBooking || !$lockedBooking->canBeAccepted()) {
                    throw new \Exception('Pesanan ini sudah diproses oleh kasir lain (status: ' . ($lockedBooking ? $lockedBooking->status_label : 'Tidak Ditemukan') . ')');
                }

                // Verifikasi stok masih valid
                foreach ($lockedBooking->items as $bookingItem) {
                    $item = CashierItem::find($bookingItem->cashier_item_id);
                    if ($item && $item->stock < 0) {
                        throw new \Exception("Stok {$item->name} bermasalah (negatif: {$item->stock}). Hubungi admin.");
                    }
                }

                $lockedBooking->update(['status' => 'confirmed']);
            });

            return back()->with('success', "Pesanan {$booking->booking_code} diterima!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menerima pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Reject a booking → cancelled + restore stock if was confirmed
     */
    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ], [
            'cancel_reason.required' => 'Alasan penolakan harus diisi',
        ]);

        try {
            DB::transaction(function () use ($booking, $request) {
                // SEC-08 Fix: Re-fetch booking with lock to prevent race condition double-cancel
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                if (!$lockedBooking || !$lockedBooking->canBeCancelled()) {
                    throw new \Exception('Pesanan ini sudah tidak bisa ditolak (status: ' . ($lockedBooking ? $lockedBooking->status_label : 'Tidak Ditemukan') . ')');
                }

                // FIX BUG-REPORT #1: Stok sudah dikurangi saat placeOrder,
                // jadi SELALU kembalikan stok saat pesanan ditolak/dibatalkan
                foreach ($lockedBooking->items as $bookingItem) {
                    $cashierItem = CashierItem::find($bookingItem->cashier_item_id);
                    if ($cashierItem) {
                        $cashierItem->increment('stock', $bookingItem->qty);
                    }
                }

                $lockedBooking->update([
                    'status' => 'cancelled',
                    'cancel_reason' => $request->cancel_reason,
                ]);
            });

            return back()->with('success', "Pesanan {$booking->booking_code} ditolak. Stok telah dikembalikan.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Move to processing
     */
    public function process(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Pesanan harus dikonfirmasi terlebih dahulu');
        }

        try {
            DB::transaction(function () use ($booking) {
                $locked = Booking::where('id', $booking->id)->lockForUpdate()->first();
                if (!$locked || $locked->status !== 'confirmed') {
                    throw new \Exception('Pesanan sudah diproses oleh kasir lain.');
                }
                $locked->update(['status' => 'processing']);
            });
            return back()->with('success', "Pesanan {$booking->booking_code} sedang diproses.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark as ready → creates Transaction + TransactionDetail records and triggers receipt (for Delivery only)
     */
    public function ready(Request $request, Booking $booking)
    {
        if ($booking->status !== 'processing') {
            return back()->with('error', 'Pesanan harus sedang diproses terlebih dahulu');
        }

        if ($booking->delivery_type === 'pickup') {
            try {
                DB::transaction(function () use ($booking) {
                    $locked = Booking::where('id', $booking->id)->lockForUpdate()->first();
                    if (!$locked || $locked->status !== 'processing') {
                        throw new \Exception('Pesanan sudah diproses oleh kasir lain.');
                    }
                    $locked->update(['status' => 'ready']);
                });
                return back()->with('success', "Pesanan {$booking->booking_code} siap diambil pelanggan.");
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // Delivery Flow
        $request->validate([
            'assignee_name' => 'required|string|max:100',
        ], [
            'assignee_name.required' => 'Nama kurir harus diisi.',
        ]);

        try {
            $transaction = DB::transaction(function () use ($booking, $request) {
                // FIX BUG-R2 #3: Lock booking + cek duplikat transaksi
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                if (!$lockedBooking || $lockedBooking->status !== 'processing') {
                    throw new \Exception('Pesanan sudah diproses oleh kasir lain (status: ' . ($lockedBooking ? $lockedBooking->status_label : 'Tidak Ditemukan') . ')');
                }

                // Cegah duplikat transaksi
                if (Transaction::where('booking_id', $lockedBooking->id)->exists()) {
                    throw new \Exception('Transaksi untuk pesanan ini sudah pernah dibuat.');
                }

                // Generate invoice
                $invoice = 'INV-' . date('YmdHis') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                // Find linked member via user
                $member = $lockedBooking->user?->member;

                $paymentMethod = $lockedBooking->payment_method ?? 'cash';
                $paidAmount = $paymentMethod === 'cash' ? ($lockedBooking->amount_paid ?? $lockedBooking->total) : $lockedBooking->total;
                $changeAmount = $paymentMethod === 'cash' ? ($paidAmount - $lockedBooking->total) : 0;

                // Create Transaction record
                $transaction = Transaction::create([
                    'invoice' => $invoice,
                    'customer_name' => $lockedBooking->customer_name,
                    'member_id' => $member?->id,
                    'total' => $lockedBooking->total,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'user_id' => Auth::id(),
                    'cashier_name' => $request->assignee_name,
                    'source' => 'online',
                    'booking_id' => $lockedBooking->id,
                ]);

                // Create TransactionDetail for each booking item
                foreach ($lockedBooking->items as $bookingItem) {
                    $cashierItem = CashierItem::find($bookingItem->cashier_item_id);

                    $purchasePrice = 0;
                    if ($cashierItem) {
                        if ($cashierItem->is_consignment) {
                            $purchasePrice = $cashierItem->cost_price ?? 0;
                        } else {
                            $purchasePrice = $cashierItem->warehouseItem?->purchase_price ?? 0;
                        }
                    }

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $bookingItem->cashier_item_id,
                        'price' => $bookingItem->price,
                        'original_price' => $bookingItem->price,
                        'discount' => 0,
                        'qty' => $bookingItem->qty,
                        'subtotal' => $bookingItem->subtotal,
                        'purchase_price' => $purchasePrice,
                    ]);
                }

                $lockedBooking->update([
                    'status' => 'ready',
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $paidAmount,
                ]);

                return $transaction;
            });

            return back()->with('success', "Pesanan {$booking->booking_code} siap diantar! Struk telah dicetak untuk kurir.")
                ->with('print_transaction_id', $transaction->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Complete booking → Just updates status to complete for Delivery, creates Transaction for Pickup
     */
    public function complete(Request $request, Booking $booking)
    {
        if (!$booking->canBeCompleted()) {
            return back()->with('error', 'Pesanan belum siap untuk diselesaikan (status: ' . $booking->status_label . ')');
        }

        if ($booking->delivery_type === 'delivery') {
            try {
                DB::transaction(function () use ($booking) {
                    $locked = Booking::where('id', $booking->id)->lockForUpdate()->first();
                    if (!$locked || !$locked->canBeCompleted()) {
                        throw new \Exception('Pesanan sudah diselesaikan oleh kasir lain.');
                    }
                    $locked->update(['status' => 'completed']);
                });
                return back()->with('success', "Pesanan {$booking->booking_code} telah diserahkan (Selesai).");
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        // Pickup Flow: Process Payment and Complete
        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'assignee_name' => 'required|string|max:100',
            'paid_amount' => 'required_if:payment_method,cash|numeric|min:' . $booking->total,
        ], [
            'assignee_name.required' => 'Nama kasir harus diisi.',
            'paid_amount.required_if' => 'Jumlah uang tunai wajib diisi jika metode pembayaran Cash.',
            'paid_amount.min' => 'Jumlah uang tunai tidak boleh kurang dari total pesanan.',
        ]);

        try {
            $transaction = DB::transaction(function () use ($booking, $request) {
                // FIX BUG-R2 #7: Lock booking + cek duplikat transaksi
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                if (!$lockedBooking || !$lockedBooking->canBeCompleted()) {
                    throw new \Exception('Pesanan sudah diselesaikan oleh kasir lain (status: ' . ($lockedBooking ? $lockedBooking->status_label : 'Tidak Ditemukan') . ')');
                }

                // Cegah duplikat transaksi
                if (Transaction::where('booking_id', $lockedBooking->id)->exists()) {
                    throw new \Exception('Transaksi untuk pesanan ini sudah pernah dibuat.');
                }

                // Generate invoice
                $invoice = 'INV-' . date('YmdHis') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                // Find linked member via user
                $member = $lockedBooking->user?->member;

                $paymentMethod = $request->payment_method;
                $paidAmount = $paymentMethod === 'cash' ? $request->paid_amount : $lockedBooking->total;
                $changeAmount = $paymentMethod === 'cash' ? ($paidAmount - $lockedBooking->total) : 0;

                // Create Transaction record
                $transaction = Transaction::create([
                    'invoice' => $invoice,
                    'customer_name' => $lockedBooking->customer_name,
                    'member_id' => $member?->id,
                    'total' => $lockedBooking->total,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'user_id' => Auth::id(),
                    'cashier_name' => $request->assignee_name,
                    'source' => 'online',
                    'booking_id' => $lockedBooking->id,
                ]);

                // Create TransactionDetail for each booking item
                foreach ($lockedBooking->items as $bookingItem) {
                    $cashierItem = CashierItem::find($bookingItem->cashier_item_id);

                    $purchasePrice = 0;
                    if ($cashierItem) {
                        if ($cashierItem->is_consignment) {
                            $purchasePrice = $cashierItem->cost_price ?? 0;
                        } else {
                            $purchasePrice = $cashierItem->warehouseItem?->purchase_price ?? 0;
                        }
                    }

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $bookingItem->cashier_item_id,
                        'price' => $bookingItem->price,
                        'original_price' => $bookingItem->price,
                        'discount' => 0,
                        'qty' => $bookingItem->qty,
                        'subtotal' => $bookingItem->subtotal,
                        'purchase_price' => $purchasePrice,
                    ]);
                }

                $lockedBooking->update([
                    'status' => 'completed',
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $paidAmount,
                ]);

                return $transaction;
            });

            return back()->with('success', "Pesanan {$booking->booking_code} telah dibayar & selesai!")
                ->with('print_transaction_id', $transaction->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Booking history (completed + cancelled)
     */
    public function history(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $search = $request->get('search');

        $query = Booking::whereIn('status', ['completed', 'cancelled'])
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Date filters
        switch ($filter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'custom':
                if ($request->start_date) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->end_date) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }
                break;
                // 'all' — no date filter
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15);

        return view('cashier.booking-history.index', compact(
            'bookings',
            'filter',
            'search'
        ));
    }

    /**
     * API: Pending count for AJAX polling badge
     */
    public function pendingCount()
    {
        return response()->json([
            'pending_count' => Booking::pending()->count(),
            'active_count' => Booking::active()->count(),
        ]);
    }

    /**
     * Manually trigger the auto-cancel command
     */
    public function runAutoCancel()
    {
        try {
            Artisan::call('bookings:auto-cancel');
            $output = Artisan::output();

            // Perintah mengembalikan teks seperti "Successfully auto-cancelled 0 expired pickup bookings."
            return back()->with('success', 'Pengecekan selesai! ' . trim($output));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan pembersihan otomatis: ' . $e->getMessage());
        }
    }
}
