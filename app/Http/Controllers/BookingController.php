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

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        if ($search) {
            // BUG-06: Gunakan LOWER() LIKE agar portable (tidak hanya PostgreSQL)
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($search) . '%']);
        }

        $items = $query->orderBy('name')->get();
        $isOpen = $this->isShopOpen();
        $cart = session('booking_cart', []);

        return view('booking.menu', compact('items', 'categories', 'selectedCategory', 'search', 'isOpen', 'cart'));
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

        $item = CashierItem::findOrFail($request->cashier_item_id);

        if ($item->stock < $request->qty) {
            return back()->with('error', "Stok {$item->name} tidak cukup. Tersedia: {$item->stock}");
        }

        $cart = session('booking_cart', []);

        // Check if item already in cart
        $found = false;
        foreach ($cart as $key => $cartItem) {
            if ($cartItem['cashier_item_id'] == $item->id) {
                $newQty = $cart[$key]['qty'] + $request->qty;
                if ($newQty > $item->stock) {
                    return back()->with('error', "Total qty melebihi stok yang tersedia ({$item->stock})");
                }
                $cart[$key]['qty'] = $newQty;
                $cart[$key]['subtotal'] = $newQty * $cart[$key]['price'];
                $found = true;
                break;
            }
        }

        if (!$found) {
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

        $total = collect($cart)->sum('subtotal');
        $user = Auth::user();

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
            'notes' => 'nullable|string|max:500',
        ], [
            'delivery_type.required' => 'Pilih metode pengambilan',
            'delivery_address.required_if' => 'Alamat pengiriman harus diisi untuk delivery',
            'pickup_time.required_if' => 'Jam ambil harus diisi',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $total = 0;

            // SEC-05 + BUG-03: Validasi stok & harga dari DB dengan lock
            $validatedCart = [];
            foreach ($cart as $cartItem) {
                $item = CashierItem::where('id', $cartItem['cashier_item_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$item || $item->stock < $cartItem['qty']) {
                    DB::rollBack();
                    return back()->with('error', "Stok {$cartItem['name']} tidak mencukupi. Tersedia: " . ($item->stock ?? 0));
                }

                // SEC-05: Gunakan harga terkini dari database
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

                // BUG-04: Kurangi stok saat order dibuat
                $item->decrement('stock', $cartItem['qty']);
            }

            // Prepare notes with time info
            $notes = $request->notes;
            if ($request->delivery_type === 'pickup' && $request->pickup_time) {
                $notes = trim("{$notes}\n[Jam Ambil: {$request->pickup_time}]");
            } elseif ($request->delivery_type === 'delivery') {
                $estimasi = now()->addMinutes(10)->format('H:i') . ' - ' . now()->addMinutes(15)->format('H:i');
                $notes = trim("{$notes}\n[Estimasi Sampai: {$estimasi}]");
            }

            // Create booking
            $booking = Booking::create([
                'booking_code' => Booking::generateBookingCode(),
                'user_id' => $user->id,
                'customer_name' => $user->member?->name ?? $user->name,
                'customer_phone' => $user->member?->phone ?? '',
                'delivery_type' => $request->delivery_type,
                'delivery_address' => $request->delivery_type === 'delivery' ? $request->delivery_address : null,
                'notes' => $notes,
                'total' => $total,
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
}
