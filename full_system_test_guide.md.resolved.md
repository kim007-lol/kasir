# Panduan Testing Sistem Menyeluruh — Kasirku

Dokumen ini adalah checklist lengkap untuk memastikan **seluruh fitur** aplikasi berjalan dengan baik.

---

## 1. 🏠 Dashboard & Statistik

- [ ] Login sebagai **Admin**. Pastikan masuk ke Dashboard Admin.
- [ ] Cek **Total Penjualan Hari Ini**: Lakukan 1 transaksi, pastikan angka bertambah.
- [ ] Cek **Total Transaksi**: Pastikan jumlahnya sesuai.
- [ ] Cek **Produk Terlaris**: Pastikan produk yang baru dibeli muncul di list.
- [ ] Cek **Stok Menipis**: Pastikan item dengan stok < 5 muncul peringatan.

## 2. 🛒 Transaksi (Kasir)

- [ ] **Scan Barcode**: Scan barang, pastikan masuk keranjang dengan harga benar.
- [ ] **Cari Manual**: Ketik nama barang, pilih dari list, pastikan masuk keranjang.
- [ ] **Edit Quantity**: Ubah jumlah barang di keranjang, total harga harus update otomatis.
- [ ] **Hapus Item**: Hapus satu barang dari keranjang, total harga berkurang.
- [ ] **Reset Keranjang**: Tekan tombol Reset, keranjang harus kosong kembali.
- [ ] **Diskon (Admin Only)**: Masukkan diskon rupiah, pastikan total bayar berkurang.
- [ ] **Pembayaran Tunai**: Masukkan nominal pas/lebih -> Berhasil -> Muncul Kembalian.
- [ ] **Pembayaran QRIS**: Pilih QRIS -> Berhasil.
- [ ] **Nama Kasir Manual**: Isi nama "Shift Pagi" -> Bayar -> Cek nama di struk.
- [ ] **Cetak Struk**: Setelah bayar, halaman struk muncul & bisa di-print.

## 3. 📜 History Transaksi

- [ ] **Filter Hari Ini**: Cek transaksi yang baru dilakukan.
- [ ] **Filter Minggu/Bulan Ini**: Pastikan data lama muncul.
- [ ] **Filter Custom**: Pilih rentang tanggal spesifik -> Data sesuai.
- [ ] **Filter Pembayaran**: Pilih "Tunai" atau "QRIS" -> Data tersaring.
- [ ] **Cari Transaksi**: Ketik nomor invoice atau nama pelanggan -> Ketemu.
- [ ] **Lihat Detail**: Klik tombol mata (View) -> Muncul detail struk & nama kasir benar.

## 4. 📊 Laporan & Keuangan

- [ ] **Grafik Penjualan**: Pastikan grafik muncul dan tidak error.
- [ ] **Laporan Stok Masuk**: Tambah stok di warehouse -> Cek log di sini.
- [ ] **Export PDF**: Klik Export PDF -> File terdownload -> Data sesuai filter.
- [ ] **Export Excel**: Klik Export Excel -> File terdownload -> Data lengkap.

## 5. 📦 Manajemen Produk (Gudang & Etalase)

### Gudang (Warehouse)

- [ ] **Tambah Barang**: Input nama, kode, harga beli -> Berhasil.
- [ ] **Edit Barang**: Ubah harga beli -> Berhasil.
- [ ] **Hapus Barang**: Hapus barang yang belum masuk etalase -> Berhasil.

### Etalase (Cashier Items)

- [ ] **Ambil dari Gudang**: Pilih barang gudang, tentukan harga jual & stok -> Muncul di kasir.
- [ ] **Barang Titipan (Konsinyasi)**: Tambah barang titipan baru -> Berhasil.
- [ ] **Validasi Harga**: Coba input Harga Jual < Harga Modal -> Gagal (Error muncul).
- [ ] **Update Stok**: Tambah stok barang etalase -> Stok bertambah.
- [ ] **Nonaktifkan Barang**: Hapus barang etalase -> Hilang dari kasir tapi data aman (Soft Delete).

## 6. 👥 Manajemen User & Member

### Member

- [ ] **Tambah Member**: Input nama & no HP -> Berhasil.
- [ ] **Edit Member**: Ubah alamat -> Berhasil.
- [ ] **Cek Poin/Total Belanja**: Pastikan total belanja member bertambah setelah transaksi.
- [ ] **Nonaktifkan Member**: Klik Hapus -> Status jadi "Nonaktif".
- [ ] **Restore Member**: Klik Pulihkan -> Status jadi "Aktif".

### User (Kasir/Admin)

- [ ] **Tambah User**: Buat akun kasir baru -> Berhasil.
- [ ] **Edit User**: Ganti password/email -> Berhasil.
- [ ] **Login Kasir**: Logout admin, login pakai akun kasir baru -> Berhasil masuk menu kasir.
- [ ] **Nonaktifkan User**: Hapus user -> Status "Nonaktif".
- [ ] **Blokir Login**: Coba login pakai user nonaktif -> Gagal.
- [ ] **Restore User**: Pulihkan user -> Bisa login lagi.

### A. Modul Autentikasi

| #   | Test Case                     | Langkah                                          | Hasil yang Diharapkan                                              |
| --- | ----------------------------- | ------------------------------------------------ | ------------------------------------------------------------------ |
| 1   | Login Admin                   | Login di `/login` dengan akun admin              | Redirect ke Dashboard Admin                                        |
| 2   | Login Kasir                   | Login di `/login` dengan akun kasir              | Redirect ke Dashboard Kasir                                        |
| 3   | Login Pelanggan via form staf | Login di `/login` dengan akun pelanggan          | **Ditolak** dengan pesan "Akun pelanggan tidak bisa login di sini" |
| 4   | Login user nonaktif           | Soft-delete user di kelola user, lalu coba login | **Ditolak** dengan pesan "Akun sudah dinonaktifkan"                |
| 5   | Brute force login             | Login salah 6x berturut-turut                    | **Diblokir** dengan pesan rate limit                               |
| 6   | Brute force registrasi        | Register 6x berturut-turut                       | **Diblokir** dengan pesan rate limit                               |

### B. Modul Transaksi POS

| #   | Test Case             | Langkah                                | Hasil yang Diharapkan                       |
| --- | --------------------- | -------------------------------------- | ------------------------------------------- |
| 1   | Tambah item ke cart   | Scan barcode atau cari item            | Item masuk ke keranjang                     |
| 2   | Checkout normal       | Isi nama kasir, pilih cash, bayar      | Transaksi tersimpan, struk ditampilkan      |
| 3   | Stok tidak cukup      | Coba checkout item qty > stok          | Error "Stok tidak mencukupi"                |
| 4   | Diskon oleh kasir     | Login sebagai kasir, coba input diskon | **Field diskon tidak muncul** (hanya admin) |
| 5   | Diskon melebihi total | Login admin, input diskon > total      | Error "Diskon tidak boleh melebihi total"   |
| 6   | Nama kasir (kasir)    | Login kasir, isi nama lain             | Nama di struk tetap nama akun sendiri       |
| 7   | Reset cart            | Klik tombol Reset Cart                 | Keranjang dikosongkan                       |

### C. Modul Booking Online

| #   | Test Case        | Langkah                                 | Hasil yang Diharapkan                       |
| --- | ---------------- | --------------------------------------- | ------------------------------------------- |
| 1   | Pesan item       | Login pelanggan → pilih menu → checkout | Booking dibuat, stok **langsung berkurang** |
| 2   | Ubah qty > stok  | Di keranjang, ubah qty melebihi stok    | Qty otomatis disesuaikan ke stok tersedia   |
| 3   | Toko tutup       | Coba pesan saat jam tutup               | Error "Toko sedang tutup"                   |
| 4   | Kasir accept     | Kasir klik Accept di pesanan pending    | Status berubah ke `confirmed`               |
| 5   | Kasir reject     | Kasir klik Reject + isi alasan          | Status `cancelled`, **stok dikembalikan**   |
| 6   | Booking selesai  | Kasir klik Complete                     | Transaksi otomatis dibuat                   |
| 7   | Concurrent order | 2 pelanggan pesan item stok 1           | Hanya 1 yang berhasil, yang lain error stok |

### D. Modul Gudang & Stok

| #   | Test Case                           | Langkah                                      | Hasil yang Diharapkan                                |
| --- | ----------------------------------- | -------------------------------------------- | ---------------------------------------------------- |
| 1   | Transfer ke kasir                   | Pilih item gudang → transfer qty             | Stok gudang berkurang, stok kasir bertambah          |
| 2   | Edit stok kasir naik                | Edit stok kasir, naikkan qty                 | Stok gudang berkurang sesuai selisih                 |
| 3   | Edit stok kasir turun               | Edit stok kasir, turunkan qty                | Stok gudang bertambah sesuai selisih                 |
| 4   | Hapus item gudang berisi stok kasir | Delete item gudang yang punya stok kasir > 0 | **Ditolak** dengan pesan "kosongkan stok kasir dulu" |

### E. Modul Laporan & History

| #   | Test Case              | Langkah                                  | Hasil yang Diharapkan                    |
| --- | ---------------------- | ---------------------------------------- | ---------------------------------------- |
| 1   | Filter history         | Pilih filter tanggal/payment method      | Hanya tampil data sesuai filter          |
| 2   | History kasir terbatas | Login kasir, lihat history               | Hanya melihat transaksi milik sendiri    |
| 3   | Net profit akurat      | Buat transaksi dengan diskon, cek report | Net profit = revenue - cost - diskon     |
| 4   | Export PDF             | Klik export PDF di halaman report        | File PDF terdownload dengan data lengkap |
| 5   | Export Excel           | Klik export Excel di halaman report      | File Excel terdownload                   |

### F. Modul User & Member

| #   | Test Case          | Langkah                             | Hasil yang Diharapkan                      |
| --- | ------------------ | ----------------------------------- | ------------------------------------------ |
| 1   | Soft delete user   | Hapus user                          | User nonaktif, baris masih ada di database |
| 2   | Restore user       | Klik restore pada user yang dihapus | User aktif kembali dan bisa login          |
| 3   | Hapus diri sendiri | Coba hapus akun sendiri             | **Ditolak**                                |
| 4   | Search supplier    | Cari supplier dengan keyword        | Hasil search respek soft-delete filter     |

---
