# Panduan Frontend: Sistem Booking Pelanggan 🎨

Halo! Backend untuk sistem booking sudah selesai. Tugas kamu adalah membuat **Tampilan (UI)** untuk sisi pelanggan.

Semua logic (Controller, Model, Database, Route) sudah siap. Kamu hanya perlu membuat file **Blade View** di folder `resources/views/booking/`.

## 📂 File yang Perlu Dibuat

Folder target: `resources/views/booking/`

| Filename             | Fungsi                     | Route URL              |
| -------------------- | -------------------------- | ---------------------- |
| `menu.blade.php`     | Halaman utama/katalog menu | `/booking/menu`        |
| `cart.blade.php`     | Keranjang belanja          | `/booking/cart`        |
| `checkout.blade.php` | Form konfirmasi pesanan    | `/booking/checkout`    |
| `status.blade.php`   | Tracking status pesanan    | `/booking/status/{id}` |
| `history.blade.php`  | Riwayat pesanan pelanggan  | `/booking/history`     |

---

## Layout Utama

Gunakan layout yang sudah disediakan agar tampilan konsisten (ada Navbar & Footer).
Setiap file harus dimulai dengan:

```blade
@extends('layouts.booking')

@section('content')
    {{-- Kodemu di sini --}}
@endsection
```

---

## 1. Halaman Menu (`menu.blade.php`)

Menampilkan daftar makanan yang tersedia.

**Data yang tersedia (Variables):**

- `$items` (List menu dari database)
- `$categories` (List kategori untuk filter)
- `$cart` (Isi keranjang saat ini)
- `$isOpen` (Boolean: `true` jika toko buka, `false` jika tutup)

**Fitur yang harus ada:**

1. **Looping Menu Item:**
   Tampilkan gambar, nama, dan harga.
2. **Form Add to Cart:**
   Setiap item harus punya form untuk masuk ke keranjang.

**Contoh Snippet Form Add to Cart:**

```blade
<form action="{{ route('booking.cart.add') }}" method="POST">
    @csrf
    <input type="hidden" name="cashier_item_id" value="{{ $item->id }}">

    <!-- Input Quantity -->
    <input type="number" name="qty" value="1" min="1" class="form-control mb-2">

    <!-- Tombol Submit -->
    <!-- Cek stok dulu -->
    @if($item->stock > 0)
        <button type="submit" class="btn btn-primary">Pesan</button>
    @else
        <button disabled class="btn btn-secondary">Habis</button>
    @endif
</form>
```

---

## 2. Halaman Keranjang (`cart.blade.php`)

Menampilkan item yang sudah dipilih sebelum checkout.

**Data yang tersedia:**

- `$cart` (Array session keranjang)
- `$total` (Total harga sementara)

**Fitur yang harus ada:**

1. **List Item:** Loop `$cart`.
2. **Hapus Item:** Tombol delete per item.
3. **Tombol Lanjut Checkout:** Link ke `/booking/checkout`.

**Contoh Snippet Hapus Item:**

```blade
<form action="{{ route('booking.cart.remove', $index) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
</form>
```

---

## 3. Halaman Checkout (`checkout.blade.php`)

Halaman terakhir untuk konfirmasi dan memilih jam ambil.

**Data yang tersedia:**

- `$cart`, `$total`
- `$user` (Data user yang login)

**Fitur yang harus ada:**

1. **Ringkasan Pesanan:** Tampilkan total harga.
2. **Form Checkout:**

**Contoh Snippet Form Checkout:**

```blade
<form action="{{ route('booking.placeOrder') }}" method="POST">
    @csrf

    <!-- Input Jam Ambil (Wajib) -->
    <label>Jam Ambil (Antara 07:00 - 15:00)</label>
    <input type="time" name="pickup_time" class="form-control" required>

    <!-- Catatan (Opsional) -->
    <label>Catatan Tambahan</label>
    <textarea name="notes" class="form-control"></textarea>

    <h4 class="mt-3">Total Bayar: Rp. {{ number_format($total) }}</h4>

    <button type="submit" class="btn btn-success w-100">Buat Pesanan</button>
</form>
```

---

## 4. Halaman Status (`status.blade.php`)

Menampilkan status pesanan setelah checkout. Halaman ini akan di-refresh otomatis (polling) atau manual oleh user.

**Data yang tersedia:**

- `$booking` (Object model booking)
    - `$booking->booking_code`
    - `$booking->status` (`pending`, `confirmed`, `processing`, `ready`, `completed`, `cancelled`)
    - `$booking->bookingItems` (List item yang dipesan)

**Logic Tampilan:**

- Jika status `pending`: "Menunggu Konfirmasi Kasir"
- Jika status `processing`: "Sedang Disiapkan"
- Jika status `ready`: "Siap Diambil!"

---

## 5. Halaman Riwayat (`history.blade.php`)

List semua pesanan yang pernah dibuat user tersebut (selesai atau batal).

**Data yang tersedia:**

- `$bookings` (List pesanan customer)

**Fitur:**

- Tabel sederhana berisi Tanggal, Kode Booking, Total Harga, Status.
- Tombol "Lihat Detail" mengarah ke `route('booking.status', $booking->id)`.

---

## Tips Tambahan 💡

- **Toko Tutup:** Di halaman menu, cek variable `$isOpen`. Jika `false`, disable tombol pesan atau tampilkan alert "Toko Tutup".
- **Styling:** Gunakan Bootstrap 5 (sudah include di layout).
- **Icons:** Gunakan Bootstrap Icons (misal `<i class="bi bi-cart"></i>`).

---

# Dokumentasi Backend (Routes, Models, Controllers)

Berikut adalah detail teknis untuk memahami alur data di backend.

## 1. Routes (`routes/web.php`)

| Method | URL                            | Route Name            | Controller Method | Deskripsi                      |
| ------ | ------------------------------ | --------------------- | ----------------- | ------------------------------ |
| GET    | `/booking/menu`                | `booking.menu`        | `menu`            | Halaman daftar menu            |
| POST   | `/booking/cart/add`            | `booking.cart.add`    | `addToCart`       | Tambah item ke keranjang       |
| POST   | `/booking/cart/update`         | `booking.cart.update` | `updateCart`      | Update qty/catatan item        |
| DELETE | `/booking/cart/remove/{index}` | `booking.cart.remove` | `removeFromCart`  | Hapus item dari keranjang      |
| GET    | `/booking/cart`                | `booking.cart`        | `cart`            | Halaman keranjang              |
| GET    | `/booking/checkout`            | `booking.checkout`    | `checkout`        | Halaman checkout               |
| POST   | `/booking/checkout`            | `booking.placeOrder`  | `placeOrder`      | Proses checkout & simpan ke DB |
| GET    | `/booking/status/{id}`         | `booking.status`      | `status`          | Halaman status pesanan         |
| GET    | `/booking/api/status/{id}`     | `booking.api.status`  | `apiStatus`       | API Polling status (JSON)      |
| GET    | `/booking/history`             | `booking.history`     | `history`         | Halaman riwayat pesanan        |

## 2. Models & Database

### `Booking` (Tabel: `bookings`)

Model utama untuk pesanan pelanggan.

- **Relasi:**
    - `user()`: Milik User (Pelanggan)
    - `bookingItems()`: Punya banyak item pesanan
    - `transaction()`: Terhubung ke transaksi (jika sudah selesai/dibayar)
- **Status Enum:** `pending`, `confirmed`, `processing`, `ready`, `completed`, `cancelled`

### `BookingItem` (Tabel: `booking_items`)

Detail item dalam setiap pesanan.

- **Kolom Penting:** `booking_id`, `cashier_item_id`, `qty`, `single_price`, `total_price`
- **Relasi:** `item()` ke `CashierItem` (stok gudang)

## 3. Controller Logic (`BookingController.php`)

Controller ini menangani logic untuk pelanggan.

- **Session Cart (`booking_cart`):**
  Keranjang belanja disimpan sementara di **Session PHP** (bukan database) sampai user melakukan Checkout.
  Struktur Session:

    ```php
    [
        [
            'id' => 1, // ID CashierItem
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'qty' => 2,
            'notes' => 'Pedas'
        ],
        ...
    ]
    ```

- **`isShopOpen()`:** Helper function untuk mengecek jam operasional (07:00 - 15:00). Jika toko tutup, fungsi `addToCart` dan `placeOrder` akan menolak request.

- **`placeOrder()`:**
    1. Validasi stok item lagi (mencegah race condition).
    2. Buat record `Booking`.
    3. Pindahkan item dari Session Cart ke tabel `booking_items`.
    4. Hapus session cart.
    5. Redirect ke halaman `booking.status`.
