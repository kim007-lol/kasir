# SMEGABIZ - Sistem Manajemen Kasir & Stok Laravel

Halo! Selamat datang di **SMEGABIZ**. Ini adalah sistem kasir (POS) modern yang dirancang khusus untuk kemudahan manajemen stok dan transaksi. Dibangun menggunakan **Laravel 12**, sistem ini fokus pada kecepatan, akurasi data, dan pengalaman pengguna yang nyaman.

---

## 🚀 Perubahan Terbaru (Changelog)

Berikut adalah beberapa perbaikan besar yang baru saja kami terapkan:

1.  **Sistem "Anti-Gantung" Keranjang**: Sekarang, keranjang belanja akan otomatis kosong jika halaman di-_refresh_. Ini mencegah kesalahan input atau data stok yang tertahan.
2.  **Tampilan Keranjang Lebih Rapi**: Daftar keranjang sekarang bisa di-_scroll_ jika produk sangat banyak. Tombol pembayaran tetap terlihat di posisi yang pas.
3.  **Perbaikan Navbar (Layar Mengambang)**: Menu navigasi di atas sekarang tetap di depan (Z-Index 1050), sehingga konten produk tidak lagi menabrak atau menutupi menu saat di-scroll.
4.  **Sistem "Jejak Digital" (Soft Deletes)**: Jika Anda menghapus kategori, supplier, atau barang, sistem hanya menyembunyikannya. Di riwayat transaksi, nama barang tersebut **tetap muncul** dengan benar.
5.  **Validasi Stok Real-time**: Kasir tidak akan bisa menjual barang melebihi stok yang ada. Pesan error akan muncul jika stok tidak cukup.
    16: 6. **Keamanan Privasi (History Access Control)**: Kasir hanya dapat melihat riwayat transaksi mereka sendiri untuk mencegah kebocoran data penjualan toko secara keseluruhan. Admin tetap memiliki akses penuh.

---

## 🛠️ Fitur & Sistem Utama

Sistem ini mendukung pengelolaan yang lengkap:

- **Dashboard**: Statistik penjualan harian dan grafik tren 7 hari terakhir.
- **Sinkronisasi Gudang & Kasir**: Pemisahan antara stok gudang (stok besar) dan stok kasir (siap jual).
- **Manajemen Barang Titipan (Consignment)**: Input barang titipan langsung dari sisi kasir.
- **Membership**: Sistem pendataan member untuk loyalitas pelanggan.
- **Laporan**: Ekspor laporan harian/periode ke PDF dan Excel.

---

## 📖 Panduan Pengguna (User Guide)

### Untuk Admin

- **Input Barang**: Masuk ke menu **Gudang**, tambah barang baru, dan tentukan supplier serta harganya.
- **Transfer Stok**: Untuk mengisi stok kasir, buka menu **Stok Item Kasir**, lalu pilih "Tambah dari Gudang".
- **Laporan**: Download laporan penjualan di menu **Laporan** untuk melihat keuntungan bersih.

### Untuk Kasir

- **Transaksi Cepat**: Cari produk atau scan barcode. Tekan Enter untuk masuk ke keranjang.
- **Pembayaran**: Pilih metode Cash atau QRIS. Gunakan tombol angka cepat untuk menghitung kembalian.
- **Riwayat**: Cek menu **Histori Transaksi** untuk melihat struk-struk sebelumnya.

---

## 🧑‍💻 Tour Untuk Tim (Developer Guide)

- **Logic Transaksi**: Lihat di `App\Http\Controllers\TransactionController`.
- **Logic Stok & Jejak Data**: Lihat `App\Models\WarehouseItem` dan `App\Models\CashierItem`. Kami menggunakan Trait `SoftDeletes` agar data referensi tetap aman.
- **Frontend Logic**: Cek JavaScript di `resources/views/transactions/index.blade.php` untuk fitur keranjang dan pencarian dinamis.

---

## ⚙️ Persyaratan & Instalasi Teknikal

### Persyaratan Sistem

- **PHP >= 8.2** (Sudah diuji pada PHP 8.3)
- **Composer**
- **Database**: SQLite / MySQL / PostgreSQL (Atur di `.env`)
- **Node.js & NPM** (Opsional untuk aset)

### Instalasi Awal

1. **Clone Repo**:
    ```bash
    git clone https://github.com/kim007-lol/kasir.git
    cd kasir
    ```
2. **Install Library**:
    ```bash
    composer install
    ```
3. **Konfigurasi Environment**:
   Salin `.env.example` menjadi `.env`, lalu atur koneksi database Anda.
4. **Generate Key & Database**:
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
5. **Jalankan Aplikasi**:
    ```bash
    php artisan serve
    ```
    Akses di [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Akun Login Default

Jika Anda baru saja menjalankan migrasi dengan seeder (`php artisan migrate --seed`), gunakan akun berikut:

| Peran     | Username / Email                   | Password        |
| :-------- | :--------------------------------- | :-------------- |
| **Admin** | `admin` atau `devidiana@gmail.com` | `adminsmegabiz` |
| **Kasir** | `kasir` atau `kasir123@gmail.com`  | `kasir123`      |

---

## 🔐 Panduan Login

Untuk masuk ke sistem, ikuti langkah-langkah berikut:

1.  **Akses Halaman Login**: Buka [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login) di browser Anda.
2.  **Masukkan Kredensial**:
    - Anda bisa menggunakan **Username** atau **Email** di kolom pertama.
    - Masukkan **Password** yang sesuai dengan peran Anda.
3.  **Klik Login**: Sistem akan mendeteksi peran Anda secara otomatis:
    - **Dashboard Admin**: Fokus pada manajemen stok gudang, supplier, kategori, dan laporan keuangan.
    - **Dashboard Kasir**: Fokus pada transaksi cepat, manajemen stok kasir, dan barang titipan.
4.  **Logout**: Klik tombol Logout di sidebar atau navbar untuk keluar dengan aman.

---

## 🧪 Data Dummy & Testing

Jika Anda ingin mencoba aplikasi dengan data yang sudah terisi (produk, supplier, member, dan riwayat transaksi), kami menyediakan seeder khusus:

1. **Jalankan Seeder Dummy**:

    ```bash
    php artisan db:seed --class=DummyDataSeeder
    ```

    _Note: Seeder ini akan mengisi data kategori, supplier, member, item gudang, item kasir, dan riwayat transaksi selama 60 hari terakhir._

2. **Reset Ulang Semua Data**:
   Jika ingin membersihkan database dan memulai dari nol:
    ```bash
    php artisan migrate:fresh --seed
    ```

---

**Selamat Bekerja!** Jika ada kendala teknis atau bug, silakan hubungi tim pengembang.
