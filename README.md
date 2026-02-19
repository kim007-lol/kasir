# SMEGABIZ - Sistem Manajemen Kasir & Stok (Laravel 12)

Selamat datang di repository **SMEGABIZ**. Ini adalah sistem _Point of Sales_ (POS), Manajemen Stok, dan **Booking Online** modern yang dirancang untuk kecepatan, akurasi data, dan keamanan. Dibangun menggunakan framework **Laravel 12** dan **PostgreSQL**.

---

## 🛠️ Fitur & Fungsi Sistem

### 1. Modul Administrasi (Admin)

- **Dashboard Statistik**: Visualisasi tren penjualan 30 hari terakhir dan ringkasan transaksi harian.
- **Manajemen User**: Kelola akun staf (Admin/Kasir) dengan validasi role yang ketat.
- **Kontrol Stok Gudang**: Kelola stok besar di gudang sebelum ditransfer ke area kasir.
- **Manajemen Supplier & Kategori**: Kelola data vendor dan pengelompokan produk.
- **Laporan Keuangan**: Ekspor laporan penjualan ke format PDF dan Excel dengan perhitungan keuntungan bersih (termasuk diskon).
- **History Global**: Akses penuh untuk melihat dan memantau seluruh transaksi yang terjadi.
- **Pengaturan Toko**: Atur jam buka/tutup dan override manual (buka/tutup paksa).

### 2. Modul Kasir (Cashier)

- **Point of Sale (POS)**: Antarmuka transaksi cepat dengan pencarian produk dinamis dan dukungan barcode scanner.
- **Manajemen Stok Kasir**: Terima barang dari gudang dan kelola stok siap jual.
- **Sistem Barang Titipan (Consignment)**: Input dan pantau barang titipan vendor langsung dari modul kasir.
- **Membership**: Pendataan pelanggan untuk diskon atau pelacakan loyalitas.
- **Print Struk & PDF**: Cetak struk belanja atau unduh dalam format PDF.
- **Manajemen Booking Online**: Terima, proses, dan selesaikan pesanan dari pelanggan online.

### 3. Modul Pelanggan (Online Booking)

- **Menu Online**: Lihat daftar item yang tersedia berdasarkan kategori.
- **Keranjang & Checkout**: Pilih item, atur qty, dan checkout dengan pilihan pickup/delivery.
- **Status Pesanan**: Lacak status pesanan secara real-time (pending → confirmed → processing → completed).
- **Riwayat Pesanan**: Lihat riwayat semua pesanan yang pernah dibuat.

### 4. Keamanan & Stabilitas

- **Access Control (ACL)**: Pembatasan akses halaman berdasarkan role (admin, kasir, pelanggan).
- **Rate Limiting**: Login dan registrasi dibatasi 5 percobaan per menit.
- **Soft Deletes**: Data penting tidak benar-benar hilang, menjaga integritas history.
- **Validasi Stok Real-time**: Stock locking (`lockForUpdate`) mencegah overselling dan race condition.
- **IDOR Protection**: Kasir hanya bisa akses data miliknya sendiri.
- **Audit Trail**: Nama kasir dipaksa dari server, tidak bisa dipalsukan.
- **Anti Stale Price**: Harga selalu dihitung ulang dari database saat checkout.
- **CSRF Protection**: Semua form dilindungi token CSRF.

---

## 📖 Panduan Pengguna (User Guide)

### Peran: ADMIN

1.  **Setup Awal**: Masuk ke menu **Gudang** untuk memasukkan produk baru.
2.  **Tambah User**: Jika memiliki staf baru, masuk ke menu **Kelola User** untuk membuat akun Kasir.
3.  **Monitoring**: Gunakan **Dashboard** untuk memantau performa toko secara real-time.

### Peran: KASIR

1.  **Mulai Transaksi**: Cari barang atau scan barcode. Barang otomatis masuk keranjang.
2.  **Input Member**: Jika pelanggan adalah member, pilih nama member agar tercatat di sistem.
3.  **Finalisasi**: Masukkan jumlah uang tunai, sistem akan menghitung kembalian otomatis. Klik "Bayar" untuk menyimpan.
4.  **Cek Riwayat**: Gunakan menu **History** untuk melihat transaksi-transaksi terakhir Anda.
5.  **Kelola Booking**: Cek pesanan masuk di menu **Booking** → Accept/Proses/Selesaikan.

### Peran: PELANGGAN

1.  **Register**: Buat akun di halaman registrasi pelanggan.
2.  **Pilih Menu**: Browse item berdasarkan kategori, tambahkan ke keranjang.
3.  **Checkout**: Pilih metode (pickup/delivery), isi detail, dan kirim pesanan.
4.  **Lacak Status**: Pantau status pesanan secara real-time.

---

## 🚀 Panduan Setup & Instalasi (Untuk Tim)

### 1. Persiapan Lingkungan

- **PHP**: Minimal versi 8.2 (Direkomendasikan 8.3).
- **Composer**: Pastikan sudah terinstall.
- **Database**: PostgreSQL (Direkomendasikan) atau SQLite.

### 2. Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/kim007-lol/kasir.git
cd kasir

# 2. Install dependensi
composer install

# 3. Setup Environment
cp .env.example .env
php artisan key:generate

# 4. Migrasi Database (Otomatis membuat user default)
php artisan migrate:fresh
```

### 3. Menjalankan Aplikasi

```bash
php artisan serve
```

Akses di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🧪 Data Dummy & Pengujian

Kami telah menyediakan seeder khusus untuk mengisi aplikasi dengan data simulasi (20+ record per tabel).

**PENTING**: Data ini tidak jalan otomatis saat migrate. Untuk mengisinya, jalankan:

```bash
php artisan db:seed --class=DummyDataSeeder
```

### Akun Login Default

Setelah migrasi, gunakan akun berikut untuk masuk:

| Role      | Username / Email                | Password        |
| :-------- | :------------------------------ | :-------------- |
| **Admin** | `admin` / `devidiana@gmail.com` | `adminsmegabiz` |
| **Kasir** | `kasir` / `kasir123@gmail.com`  | `kasir123`      |

---

## 🧑‍💻 Informasi Teknis (Untuk Developer)

- **Logic Transaksi**: `App\Http\Controllers\TransactionController` — termasuk validasi stok, diskon, harga terkini.
- **Logic Booking**: `App\Http\Controllers\BookingController` — termasuk lockForUpdate dan stock decrement.
- **Validasi History**: `App\Http\Controllers\HistoryController` — pembatasan akses berdasarkan role.
- **Frontend**: Blade + Vanilla JS untuk responsivitas maksimal tanpa library berat.
- **Database**: PostgreSQL dengan `lockForUpdate` untuk mencegah race condition.
- **Testing**: Gunakan panduan testing manual di atas untuk validasi fitur.

---

© 2026 SMEGABIZ Team.
