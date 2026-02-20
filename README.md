# Kasirku — Modern Point of Sale (POS) System

**Kasirku** adalah aplikasi kasir berbasis web yang dirancang untuk toko retail, minimarket, atau usaha kecil-menengah. Aplikasi ini tidak hanya menangani transaksi di kasir, tetapi juga mendukung pemesanan online oleh pelanggan (Booking Order) dan manajemen gudang yang lengkap.

## 🚀 Fitur Utama

### 1. 🖥️ Kasir (Point of Sale)

Halaman khusus untuk staf kasir dengan antarmuka yang optimal untuk kecepatan transaksi.

- **Transaksi Cepat**: Scan barcode atau cari nama barang.
- **Support Hardware**: Barcode Scanner, Printer Thermal (58mm/80mm), dan **Dual Monitor**.
- **Customer-Facing Display (Baru!)**: Tampilan layar kedua khusus untuk pelanggan. Menampilkan keranjang belanja dan total bayar secara real-time.
- **Multi-Cart**: Bisa melayani beberapa pelanggan sekaligus (simpan keranjang sementara).
- **Pembayaran Fleksibel**: Tunai, QRIS.
- **Manajemen Stok Kasir**: Request barang ke gudang, terima barang, dan stok opname harian.
- **Barang Titipan (Konsinyasi)**: Kelola barang titipan dari supplier luar.
- **Mode Booking**: Terima dan proses pesanan yang masuk dari pelanggan online.

### 2. 📱 Pelanggan (Online Booking)

Antarmuka mobile-friendly untuk pelanggan.

- **Katalog Online**: Lihat daftar barang dan harga dari HP.
- **Booking Order**: Pesan barang dari rumah, ambil di toko.
- **Status Pesanan**: Pantau status pesanan (Diterima, Diproses, Siap Diambil).
- **Riwayat Belanja**: Lihat kembali apa yang pernah dibeli.

### 3. 🛡️ Admin (Back Office)

Panel kendali penuh untuk pemilik toko atau manajer.

- **Dashboard**: Statistik penjualan harian/bulanan, grafik pendapatan, produk terlaris.
- **Manajemen Produk**: Kategori, Supplier, Harga Beli/Jual, Stok Gudang.
- **Manajemen Pengguna**: Tambah/Hapus staf (Kasir, Gudang).
- **Laporan Lengkap**:
    - Laporan Penjualan (Harian/Bulanan).
    - Laporan Stok Masuk/Keluar.
    - Laporan Barang Titipan.
    - Export ke PDF & Excel.
- **Pengaturan Toko**: Atur jam buka/tutup toko (otomatis menolak booking di luar jam operasional).

---

## 🛠️ Stack Teknologi & Libraries

Berikut adalah detail teknologi dan library yang digunakan dalam proyek ini:

### **Backend (Laravel 12)**

- **Framework**: Laravel 12.x (PHP 8.2+) — Framework PHP modern yang aman dan scalable.
- **Database**: PostgreSQL — Database relasional yang handal.
- **Auth**: `laravel/ui` & `laravel/sanctum` — Untuk sistem login & autentikasi API.
- **Laporan PDF**: `barryvdh/laravel-dompdf` — Library untuk generate cetak laporan ke PDF.
- **Export Excel**: `maatwebsite/excel` — Library untuk download laporan dalam format Excel (.xlsx).
- **WebSocket Server**: `laravel/reverb` — Server real-time untuk notifikasi pesanan instan.

### **Frontend**

- **UI Framework**: Bootstrap 5.3 — Untuk tampilan responsif dan rapi.
- **Build Tool**: Vite — Compiler aset modern yang sangat cepat.
- **Grafik**: `chart.js` — Menampilkan grafik statistik penjualan di dashboard admin.
- **Real-Time Client**: `laravel-echo` & `pusher-js` — Menerima notifikasi dari server Reverb.
- **Dual Monitor**: **BroadcastChannel API** — Teknologi browser native (tanpa library tambahan) untuk sinkronisasi layar kasir dan pelanggan.

---

## 🛠️ Persyaratan Sistem (Requirements)

Sebelum menginstall, pastikan komputer Anda memiliki:

- **PHP**: Versi 8.1 atau lebih baru (pastikan ekstensi `php_pgsql` aktif).
- **Composer**: Untuk install library PHP.
- **Node.js & NPM**: Untuk compile aset frontend (CSS/JS).
- **Database**: PostgreSQL (v12+ disarankan).
- **Web Browser**: Google Chrome / Microsoft Edge (terbaru).

---

## 📦 Panduan Instalasi (Setup)

Ikuti langkah ini agar program berjalan tanpa error:

### 1. Clone Project

```bash
git clone https://github.com/username/kasirku.git
cd kasirku
```

### 2. Install Dependencies

Install library PHP dan JavaScript yang dibutuhkan:

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

Duplikat file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buka file `.env` dan atur koneksi database:

```ini
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kasirku
DB_USERNAME=postgres
DB_PASSWORD=password_anda
```

### 4. Generate Key & Link Storage

```bash
php artisan key:generate
php artisan storage:link
```

_Note: `storage:link` penting agar gambar produk bisa muncul._

### 5. Setup Database (Migrate & Seed)

Jalankan perintah ini untuk membuat tabel dan mengisi data awal (akun admin, produk contoh):

```bash
php artisan migrate:fresh --seed
```

### 6. Build Frontend

Compile file CSS dan JS:

```bash
npm run build
```

---

## ▶️ Cara Menjalankan

1.  Jalankan server lokal Laravel:
    ```bash
    php artisan serve
    ```
2.  Buka browser dan akses: `http://127.0.0.1:8000`

---

## 🔑 Akun Default (Login)

Gunakan akun ini untuk masuk ke sistem:

| Role              | Email              | Password   | Akses                    |
| ----------------- | ------------------ | ---------- | ------------------------ |
| **Administrator** | `admin@tokoku.com` | `password` | Full Akses (Back Office) |
| **Kasir**         | `kasir@tokoku.com` | `password` | POS & Transaksi          |
| **Pelanggan**     | `user@example.com` | `password` | Booking Online           |

_Note: Anda bisa membuat akun pelanggan baru melalui menu "Daftar" di halaman depan._

---

## ❓ Troubleshooting (Masalah Umum)

**1. Gambar produk tidak muncul?**

- Pastikan sudah menjalankan `php artisan storage:link`.
- Jika masih error di Windows, coba hapus folder `public/storage` lalu jalankan perintah link ulang.

**2. Tampilan berantakan / CSS hilang?**

- Jalankan `npm run build` untuk memproses ulang file CSS/JS.

**3. Reset Data Transaksi?**

- Jika ingin menghapus semua data transaksi dan mulai dari nol, jalankan ulang: `php artisan migrate:fresh --seed`. **PERHATIAN: Ini akan menghapus semua data!**

**4. Printer Thermal tidak mencetak otomatis?**

- Pastikan browser tidak memblokir pop-up.
- Di jendela print browser, pilih printer thermal Anda dan atur ukuran kertas ke 80mm atau 58mm sesuai printer. Menghilangkan header/footer browser di pengaturan print juga disarankan.

**5. Layar Pelanggan (Customer Display) tidak update?**

- Pastikan kedua tab (Kasir & Layar Pelanggan) dibuka di browser yang sama (karena menggunakan fitur BroadcastChannel browser).

---

## ℹ️ Teknologi Real-Time (Hybrid Architecture)

Aplikasi ini menggunakan pendekatan **Hybrid Real-Time** yang cerdas untuk memastikan fitur tetap jalan di berbagai kondisi:

### 1. Booking Online (Notifikasi Kasir)

Fitur ini menggunakan **WebSocket (Laravel Reverb)** sebagai prioritas utama.

- **Jika Server WebSocket Nyala (`php artisan reverb:start`)**: Kasir akan menerima notifikasi pesanan masuk secara **INSTAN** (tanpa delay).
- **Jika Server WebSocket Mati (Hanya `php artisan serve`)**: Jangan khawatir! Sistem otomatis beralih ke mode **Smart Polling**. Kasir akan mengecek ke server setiap 10-30 detik untuk mencari pesanan baru.
- **Kesimpulan**: Anda **TIDAK WAJIB** menjalankan server WebSocket. Program tetap jalan normal dengan metode polling (hanya ada sedikit delay notifikasi).

### 2. Layar Pelanggan (Customer Display)

Fitur ini **TIDAK** menggunakan WebSocket sama sekali, melainkan **BroadcastChannel API**.

- Komunikasi terjadi langsung antar-tab browser di komputer yang sama.
- **Keuntungan**: Sangat ringan, tidak butuh internet, tidak butuh server tambahan, dan nol latensi.
- **Syarat**: Halaman Kasir dan Layar Pelanggan harus dibuka di browser yang sama (misal: Chrome dengan 2 tab).

---
