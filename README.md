# KasirKu - Sistem Manajamen Kasir & Stok Laravel

Sistem Kasir modern yang dibangun dengan Laravel 10, mendukung manajemen stok gudang, stok kasir, membership, dan pelaporan transaksi.

## Fitur Utama

- **Dashboard**: Statistik penjualan harian dan grafik 7 hari terakhir.
- **Manajemen Gudang**: Kelola stok masuk dari supplier dan update harga beli/jual.
- **Stok Kasir**: Pindahkan stok dari gudang ke etalase kasir dengan mudah.
- **Transaksi**: Antarmuka kasir cepat dengan dukungan QRIS dan Tunai.
- **Membership**: Kelola data member untuk loyalitas pelanggan.
- **Laporan**: Laporan harian/periode dalam format PDF dan Excel, lengkap dengan estimasi keuntungan bersih.

## Persyaratan Sistem

- PHP >= 8.1
- Composer
- SQLite / MySQL / PostgreSQL (Sesuai konfigurasi `.env`)
- Node.js & NPM (Opsional, untuk development aset)

## Instalasi Awal

1. **Clone & Install Dependencies**

    ```bash
    composer install
    ```

2. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env`:

    ```bash
    cp .env.example .env
    ```

    Atur koneksi database Anda di dalam file `.env`.

3. **Generate App Key**

    ```bash
    php artisan key:generate
    ```

4. **Migrasi & Seeding**
   Jalankan migrasi database beserta data awal (default user & categories):

    ```bash
    php artisan migrate --seed
    ```

5. **Jalankan Aplikasi**
    ```bash
    php artisan serve
    ```
    Akses aplikasi di [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Akun Default

Setelah menjalankan seeder, Anda dapat login dengan:

- **Email**: `admin@example.com`
- **Password**: `password`
