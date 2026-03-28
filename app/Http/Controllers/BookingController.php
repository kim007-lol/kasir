<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CashierItem;
use App\Models\Category;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Check if shop is currently open (reads from ShopSetting)
     */
    private function isShopOpen(): bool
    {
        return ShopSetting::isShopOpen();
    }

    /**
     * Display the booking menu with available items
     */
    public function menu(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = $request->get('category');
        $search = $request->get('search');
        $consignmentFilter = $request->get('consignment');

        $query = CashierItem::where('stock', '>', 0)
            ->with('category')
            ->where(function ($q) {
                // Exclude consignment items that are NOT from today
                $q->where('is_consignment', false)
                    ->orWhere(function ($q2) {
                        $q2->where('is_consignment', true)
                            ->whereDate('created_at', today());
                    });
            });

        // Filter by TITIPAN (consignment) items only
        if ($consignmentFilter) {
            $query->where('is_consignment', true);
        } elseif ($selectedCategory) {
            $query->where(function ($q) use ($selectedCategory) {
                $q->where('category_id', $selectedCategory)
                    ->orWhereHas('warehouseItem', function ($sub) use ($selectedCategory) {
                        $sub->where('category_id', $selectedCategory);
                    });
            });
        }

        if ($search) {
            // BUG-06: Gunakan LOWER() LIKE agar portable (tidak hanya PostgreSQL)
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($search) . '%']);
        }

        $items = $query->orderBy('name')->get();
        $isOpen = $this->isShopOpen();
        $cart = session('booking_cart', []);

        return view('booking.menu', compact('items', 'categories', 'selectedCategory', 'search', 'isOpen', 'cart', 'consignmentFilter'));
    }

    /**
     * Add item to session cart
     */
    public function addToCart(Request $request)
    {
        if (!$this->isShopOpen()) {
            return back()->with('error', 'Maaf, toko sedang tutup. Jam operasional: 07:00 - 15:00 WIB');
        }

        $request->validate([
            'cashier_item_id' => 'required|exists:cashier_items,id',
            'qty' => 'required|integer|min:1',
        ]);

        // SEC-06: Lock item saat cek stok untuk mencegah race condition
        $item = CashierItem::where('id', $request->cashier_item_id)->lockForUpdate()->first();

        if (!$item) {
            return back()->with('error', 'Item tidak ditemukan.');
        }

        $cart = session('booking_cart', []);

        // Hitung total qty di cart + qty baru
        $existingQty = 0;
        $existingKey = null;
        foreach ($cart as $key => $cartItem) {
            if ($cartItem['cashier_item_id'] == $item->id) {
                $existingQty = $cart[$key]['qty'];
                $existingKey = $key;
                break;
            }
        }

        $totalQty = $existingQty + $request->qty;

        if ($totalQty > $item->stock) {
            return back()->with('error', "Stok {$item->name} tidak cukup. Tersedia: {$item->stock}" . ($existingQty > 0 ? " (sudah {$existingQty} di keranjang)" : ''));
        }

        if ($existingKey !== null) {
            $cart[$existingKey]['qty'] = $totalQty;
            $cart[$existingKey]['price'] = $item->final_price;
            $cart[$existingKey]['subtotal'] = $totalQty * $item->final_price;
        } else {
            $cart[] = [
                'cashier_item_id' => $item->id,
                'name' => $item->name,
                'price' => $item->final_price,
                'qty' => $request->qty,
                'subtotal' => $request->qty * $item->final_price,
                'notes' => '',
                'max_stock' => $item->stock,
            ];
        }

        session(['booking_cart' => $cart]);

        return back()->with('success', "{$item->name} ditambahkan ke keranjang!");
    }

    /**
     * Update cart item qty/notes
     */
    public function updateCart(Request $request)
    {
        $cart = session('booking_cart', []);
        $updates = $request->input('cart', []);
        $errors = [];

        foreach ($updates as $index => $data) {
            if (isset($cart[$index])) {
                $qty = max(1, (int) ($data['qty'] ?? 1));

                // SEC-04: Validasi stok dari database
                $item = CashierItem::find($cart[$index]['cashier_item_id']);
                if ($item && $qty > $item->stock) {
                    $qty = $item->stock; // Clamp ke stok tersedia
                    $errors[] = "{$cart[$index]['name']}: qty disesuaikan ke stok tersedia ({$item->stock})";
                }

                // Re-validate harga dari DB agar tidak stale
                if ($item) {
                    $cart[$index]['price'] = $item->final_price;
                }

                $cart[$index]['qty'] = $qty;
                $cart[$index]['subtotal'] = $qty * $cart[$index]['price'];
                $cart[$index]['notes'] = $data['notes'] ?? '';
            }
        }

        session(['booking_cart' => $cart]);

        if (!empty($errors)) {
            return back()->with('warning', implode('. ', $errors));
        }

        return back()->with('success', 'Keranjang diperbarui!');
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($index)
    {
        $cart = session('booking_cart', []);

        if (isset($cart[$index])) {
            $name = $cart[$index]['name'];
            unset($cart[$index]);
            $cart = array_values($cart); // re-index
            session(['booking_cart' => $cart]);
            return back()->with('success', "{$name} dihapus dari keranjang");
        }

        return back()->with('error', 'Item tidak ditemukan');
    }

    /**
     * Show cart page
     */
    public function cart()
    {
        $cart = session('booking_cart', []);
        $total = collect($cart)->sum('subtotal');
        $isOpen = $this->isShopOpen();

        return view('booking.cart', compact('cart', 'total', 'isOpen'));
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $cart = session('booking_cart', []);

        if (empty($cart)) {
            return redirect()->route('booking.menu')->with('error', 'Keranjang masih kosong');
        }

        if (!$this->isShopOpen()) {
            return redirect()->route('booking.menu')->with('error', 'Maaf, toko sedang tutup. Jam operasional: 07:00 - 15:00 WIB');
        }

        // Cek apakah harga atau ketersediaan item berubah sejak ditambahkan ke cart
        $priceChanged = false;
        $changedItems = [];
        $unavailableItems = [];

        foreach ($cart as $cartItem) {
            $dbItem = CashierItem::find($cartItem['cashier_item_id']);
            if (!$dbItem) {
                $unavailableItems[] = $cartItem['name'];
                continue;
            }
            if (abs($dbItem->final_price - $cartItem['price']) > 0.01) {
                $priceChanged = true;
                $changedItems[] = "{$cartItem['name']} (Rp " . number_format($cartItem['price'], 0, ',', '.') . " → Rp " . number_format($dbItem->final_price, 0, ',', '.') . ")";
            }
        }

        if (!empty($unavailableItems) || $priceChanged) {
            // Hapus cart dan suruh user pesan ulang
            session()->forget('booking_cart');

            $messages = [];
            if (!empty($unavailableItems)) {
                $messages[] = 'Item berikut sudah tidak tersedia: ' . implode(', ', $unavailableItems);
            }
            if ($priceChanged) {
                $messages[] = 'Harga item berikut telah berubah: ' . implode(', ', $changedItems);
            }
            $messages[] = 'Keranjang telah direset. Silakan pesan ulang dengan harga terbaru.';

            return redirect()->route('booking.menu')->with('error', implode('. ', $messages));
        }

        $total = collect($cart)->sum('subtotal');
        $user = Auth::user();
        $user->load('member');

        return view('booking.checkout', compact('cart', 'total', 'user'));
    }

    /**
     * Place the order
     */
    public function placeOrder(Request $request)
    {
        if (!$this->isShopOpen()) {
            return redirect()->route('booking.menu')->with('error', 'Maaf, toko sedang tutup.');
        }

        $cart = session('booking_cart', []);
        if (empty($cart)) {
            return redirect()->route('booking.menu')->with('error', 'Keranjang masih kosong');
        }

        $request->validate([
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string|max:500',
            'pickup_time' => 'required_if:delivery_type,pickup|nullable|date_format:H:i',
            'payment_method' => 'required_if:delivery_type,delivery|nullable|in:cash,qris',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ], [
            'delivery_type.required' => 'Pilih metode pengambilan',
            'delivery_address.required_if' => 'Alamat pengiriman harus diisi untuk delivery',
            'pickup_time.required_if' => 'Jam ambil harus diisi',
            'payment_method.required_if' => 'Pilih metode pembayaran',
            'amount_paid.numeric' => 'Nominal uang tunai harus berupa angka',
            'amount_paid.min' => 'Nominal uang tunai tidak boleh negatif',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $total = 0;
            $validatedCart = [];

            // SEC-05 + BUG-03: Validasi stok & harga dari DB dengan lock LAKUKAN PALING AWAL
            $changedPrices = [];
            foreach ($cart as $cartItem) {
                $item = CashierItem::where('id', $cartItem['cashier_item_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    DB::rollBack();
                    session()->forget('booking_cart');
                    return redirect()->route('booking.menu')->with('error', "Item {$cartItem['name']} sudah tidak tersedia. Keranjang telah direset, silakan pesan ulang.");
                }

                if ($item->stock < $cartItem['qty']) {
                    DB::rollBack();
                    return back()->with('error', "Stok {$cartItem['name']} tidak mencukupi. Tersedia: {$item->stock}");
                }

                // Deteksi perubahan harga
                if (abs($item->final_price - $cartItem['price']) > 0.01) {
                    $changedPrices[] = "{$cartItem['name']} (Rp " . number_format($cartItem['price'], 0, ',', '.') . " → Rp " . number_format($item->final_price, 0, ',', '.') . ")";
                }

                // Gunakan harga terkini yang DILOCK dari database
                $currentPrice = $item->final_price;
                $subtotal = $currentPrice * $cartItem['qty'];
                $total += $subtotal;

                $validatedCart[] = [
                    'cashier_item_id' => $item->id,
                    'name' => $item->name,
                    'qty' => $cartItem['qty'],
                    'price' => $currentPrice,
                    'subtotal' => $subtotal,
                    'notes' => $cartItem['notes'] ?? null,
                ];

                // FIX BUG-REPORT #1: Langsung kurangi stok saat pesanan dibuat
                // agar tidak terjadi overselling ke pelanggan lain / transaksi kasir
                $item->decrement('stock', $cartItem['qty']);
            }

            // Jika ada harga yang berubah, batalkan dan suruh user pesan ulang
            if (!empty($changedPrices)) {
                DB::rollBack();
                session()->forget('booking_cart');
                $msg = 'Harga item berikut telah berubah: ' . implode(', ', $changedPrices) . '. Keranjang telah direset. Silakan pesan ulang dengan harga terbaru.';
                return redirect()->route('booking.menu')->with('error', $msg);
            }

            // Validasi uang tunai secara manual SETELAH semua harga dilock untuk menghindari race condition (perubahan harga dadakan)
            if ($request->delivery_type === 'delivery' && $request->payment_method === 'cash') {
                $amountPaid = (float) $request->amount_paid;
                if (!$request->has('amount_paid') || trim($request->amount_paid) === '') {
                    DB::rollBack();
                    return back()->withErrors(['amount_paid' => 'Nominal uang tunai harus diisi'])->withInput();
                }

                if ($amountPaid < $total) {
                    DB::rollBack();
                    return back()->withErrors(['amount_paid' => 'Nominal uang tunai minimal Rp ' . number_format($total, 0, ',', '.')])->withInput();
                }
            }

            // Prepare notes with time info
            $notes = $request->notes;
            if ($request->delivery_type === 'pickup' && $request->pickup_time) {
                $notes = trim("{$notes}\n[Jam Ambil: {$request->pickup_time}]");
            } elseif ($request->delivery_type === 'delivery') {
                $estimasi = now()->addMinutes(10)->format('H:i') . ' - ' . now()->addMinutes(15)->format('H:i');
                $notes = trim("{$notes}\n[Estimasi Sampai: {$estimasi}]");
            }

            // Format pickup_time to full datetime
            $pickupTimeDt = null;
            if ($request->delivery_type === 'pickup' && $request->pickup_time) {
                $pickupTimeDt = now()->format('Y-m-d') . ' ' . $request->pickup_time . ':00';
            }

            // Create booking
            $booking = Booking::create([
                'booking_code' => Booking::generateBookingCode(),
                'user_id' => $user->id,
                'customer_name' => $user->member?->name ?? $user->name,
                'customer_phone' => $user->member?->phone ?? '',
                'delivery_type' => $request->delivery_type,
                'pickup_time' => $pickupTimeDt,
                'delivery_address' => $request->delivery_type === 'delivery' ? $request->delivery_address : null,
                'notes' => $notes,
                'total' => $total,
                'payment_method' => $request->delivery_type === 'delivery' ? $request->payment_method : null,
                'amount_paid' => ($request->delivery_type === 'delivery' && $request->payment_method === 'cash') ? $request->amount_paid : null,
                'status' => 'pending',
            ]);

            // Create booking items with validated data
            foreach ($validatedCart as $item) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'cashier_item_id' => $item['cashier_item_id'],
                    'name' => $item['name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'],
                ]);
            }

            // Clear the cart
            session()->forget('booking_cart');

            DB::commit();

            return redirect()->route('booking.status', $booking)
                ->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi kasir.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
        }
    }

    /**
     * Show booking status tracking page
     */
    public function status(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load('items');

        return view('booking.status', compact('booking'));
    }

    /**
     * API: Get booking status (for polling)
     */
    public function apiStatus(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $booking->status,
            'status_label' => $booking->status_label,
            'status_badge' => $booking->status_badge,
        ]);
    }

    /**
     * Show booking history for this customer
     */
    public function history()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('booking.history', compact('bookings'));
    }

    /**
     * Cancel a pending booking (Customer side)
     */
    public function cancel(Booking $booking)
    {
        // Ensure user can only cancel their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pesanan yang sudah diproses tidak dapat dibatalkan.');
        }

        try {
            DB::transaction(function () use ($booking) {
                // Lock booking to prevent race condition 
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                if (!$lockedBooking || $lockedBooking->status !== 'pending') {
                    throw new \Exception('Pesanan sudah diproses dan tidak bisa dibatalkan.');
                }

                // Return stock for each item
                foreach ($lockedBooking->items as $bookingItem) {
                    $item = \App\Models\CashierItem::find($bookingItem->cashier_item_id);
                    if ($item) {
                        $item->increment('stock', $bookingItem->qty);
                    }
                }

                $lockedBooking->update([
                    'status' => 'cancelled',
                    'cancel_reason' => 'Dibatalkan oleh pelanggan',
                ]);
            });

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
