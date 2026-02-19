<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CashierItem;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
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

        // BUG-04: Stok sudah dikurangi saat placeOrder, jadi accept hanya update status
        $booking->update(['status' => 'confirmed']);

        return back()->with('success', "Pesanan {$booking->booking_code} diterima!");
    }

    /**
     * Reject a booking → cancelled + restore stock if was confirmed
     */
    public function reject(Request $request, Booking $booking)
    {
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Pesanan ini tidak bisa ditolak (status: ' . $booking->status_label . ')');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ], [
            'cancel_reason.required' => 'Alasan penolakan harus diisi',
        ]);

        try {
            DB::transaction(function () use ($booking, $request) {
                // BUG-04: Stok dikurangi saat placeOrder, jadi selalu kembalikan saat reject/cancel
                foreach ($booking->items as $bookingItem) {
                    $cashierItem = CashierItem::find($bookingItem->cashier_item_id);
                    if ($cashierItem) {
                        $cashierItem->increment('stock', $bookingItem->qty);
                    }
                }

                $booking->update([
                    'status' => 'cancelled',
                    'cancel_reason' => $request->cancel_reason,
                ]);
            });

            return back()->with('success', "Pesanan {$booking->booking_code} ditolak. Stok telah dikembalikan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

        $booking->update(['status' => 'processing']);
        return back()->with('success', "Pesanan {$booking->booking_code} sedang diproses.");
    }

    /**
     * Mark as ready
     */
    public function ready(Booking $booking)
    {
        if ($booking->status !== 'processing') {
            return back()->with('error', 'Pesanan harus sedang diproses terlebih dahulu');
        }

        $booking->update(['status' => 'ready']);
        return back()->with('success', "Pesanan {$booking->booking_code} siap diambil/dikirim!");
    }

    /**
     * Complete booking → creates Transaction + TransactionDetail records
     */
    public function complete(Request $request, Booking $booking)
    {
        if (!$booking->canBeCompleted()) {
            return back()->with('error', 'Pesanan belum siap untuk diselesaikan (status: ' . $booking->status_label . ')');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,qris',
        ]);

        try {
            DB::transaction(function () use ($booking, $request) {
                // Generate invoice
                $invoice = 'INV-' . date('YmdHis') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                // Find linked member via user
                $member = $booking->user?->member;

                // Create Transaction record
                $transaction = Transaction::create([
                    'invoice' => $invoice,
                    'customer_name' => $booking->customer_name,
                    'member_id' => $member?->id,
                    'total' => $booking->total,
                    'paid_amount' => $booking->total, // Fully paid
                    'change_amount' => 0,
                    'payment_method' => $request->payment_method,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'user_id' => Auth::id(), // Kasir who completed it
                    'cashier_name' => Auth::user()->name,
                    'source' => 'online',
                    'booking_id' => $booking->id,
                ]);

                // Create TransactionDetail for each booking item
                foreach ($booking->items as $bookingItem) {
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
                        'original_price' => $bookingItem->price, // snapshot from booking
                        'discount' => 0,
                        'qty' => $bookingItem->qty,
                        'subtotal' => $bookingItem->subtotal,
                        'purchase_price' => $purchasePrice,
                    ]);
                }

                // Mark booking as completed
                $booking->update([
                    'status' => 'completed',
                    'payment_method' => $request->payment_method,
                ]);
            });

            return back()->with('success', "Pesanan {$booking->booking_code} selesai! Transaksi otomatis tercatat.");
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
}
