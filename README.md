# SMEGABIZ - Sistem Manajemen Kasir & Stok (Laravel 12)

Selamat datang di repository **SMEGABIZ**. Ini adalah sistem _Point of Sales_ (POS) dan Manajemen Stok modern yang dirancang untuk kecepatan, akurasi data, dan keamanan. Dibangun menggunakan framework **Laravel 12** dan **PostgreSQL/SQLite**.

---

## 🛠️ Fitur & Fungsi Sistem

Sistem ini dibagi menjadi dua modul utama berdasarkan peran pengguna:

### 1. Modul Administrasi (Admin)

- **Dashboard Statistik**: Visualisasi tren penjualan 7 hari terakhir dan ringkasan transaksi harian.
- **Manajemen User**: Kelola akun staf (Admin/Kasir) dengan validasi role yang ketat.
- **Kontrol Stok Gudang**: Kelola stok besar di gudang sebelum ditransfer ke area kasir.
- **Manajemen Supplier & Kategori**: Kelola data vendor dan pengelompokan produk.
- **Laporan Keuangan**: Ekspor laporan penjualan ke format PDF dan Excel dengan perhitungan keuntungan bersih.
- **History Global**: Akses penuh untuk melihat dan memantau seluruh transaksi yang terjadi.

### 2. Modul Kasir (Cashier)

- **Point of Sale (POS)**: Antarmuka transaksi cepat dengan fitur pencarian produk dinamis dan dukungan barcode scanner.
- **Manajemen Stok Kasir**: Terima barang dari gudang dan kelola stok siap jual.
- **Sistem Barang Titipan (Consignment)**: Input dan pantau barang titipan vendor langsung dari modul kasir.
- **Membership**: Pendataan pelanggan untuk memberikan diskon atau pelacakan loyalitas.
- **Print Struk & PDF**: Cetak struk belanja atau unduh dalam format PDF segera setelah transaksi selesai.

### 3. Keamanan & Stabilitas

- **Access Control (ACL)**: Pembatasan akses halaman berdasarkan role. Kasir hanya bisa melihat history milik mereka sendiri.
- **Soft Deletes**: Data penting (kategori, produk) yang dihapus tidak benar-benar hilang dari database, menjaga integritas history transaksi.
- **Validasi Stok**: Mencegah penjualan barang jika stok tidak mencukupi di level sistem.

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

---

## 🚀 Panduan Setup & Instalasi (Untuk Tim)

Ikuti langkah-langkah ini untuk menjalankan project di lingkungan lokal:

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

- **Logic Transaksi**: Berada di `App\Http\Controllers\TransactionController`.
- **Validasi History**: Logic pembatasan akses riwayat ada di `App\Http\Controllers\HistoryController`.
- **Frontend**: Menggunakan Blade + Vanilla JS untuk responsivitas maksimal tanpa library berat.
- **Testing**: Gunakan perintah `php artisan test` untuk menjalankan pengujian otomatis.

---

© 2026 SMEGABIZ Team.
