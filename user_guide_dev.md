# Panduan Pengujian Sistem Kasirku (User Acceptance Testing)

Panduan ini dibuat untuk membantu Anda melakukan pengujian menyeluruh (End-to-End Testing) pada aplikasi Kasirku. Ikuti langkah-langkah di bawah ini secara bertahap untuk memastikan semua fitur berjalan lancar dan menemukan bug.

## 📋 Persiapan

Pastikan aplikasi sudah berjalan:

1. Jalankan `php artisan serve`
2. Buka browser (Chrome/Edge disarankan)
3. Siapkan 2 browser atau 2 tab berbeda (Incognito) untuk mensimulasikan peran berbeda (misal: Admin di Chrome, Pelanggan di Edge).

---

## 1. Pengujian Autentikasi & Akun

### 1.1 Login Staff (Admin/Kasir)

- [ ] Buka `/login`
- [ ] Login sabagai **Admin** (email/pass admin).
    - _Ekspektasi_: Diarahkan ke `/dashboard` (Tampilan Admin).
- [ ] Logout.
- [ ] Login sebagai **Kasir**.
    - _Ekspektasi_: Diarahkan ke `/cashier/dashboard` (Tampilan Kasir).
- [ ] Coba login dengan password salah.
    - _Ekspektasi_: Muncul pesan error credential tidak cocok.

### 1.2 Registrasi & Login Pelanggan

- [ ] Buka halaman utama `/` -> Klik "Daftar" atau akses `/pelanggan/register`.
- [ ] Daftar akun baru.
    - _Ekspektasi_: Berhasil daftar dan otomatis login atau diarahkan ke login.
- [ ] Login sebagai Pelanggan.
    - _Ekspektasi_: Diarahkan ke halaman menu booking (`/booking/menu`).

---

## 2. Pengujian Fitur Admin (Back Office)

_Login sebagai Admin_

### 2.1 Manajemen Master Data

- **Kategori**:
    - [ ] Tambah kategori baru.
    - [ ] Edit nama kategori.
    - [ ] Hapus kategori.
    - [ ] Restore kategori (jika fitur soft delete aktif).
- **Supplier**:
    - [ ] Tambah supplier baru.
    - [ ] Edit & Hapus.
- **Member**:
    - [ ] Tambah member baru.
    - [ ] Cek validasi (misal kode member unik).

### 2.2 Manajemen Gudang (Warehouse)

- [ ] Buka menu **Gudang**.
- [ ] Tambah barang baru (Set stok awal, harga beli, harga jual).
- [ ] Edit barang (ubah stok/harga).
- [ ] Hapus barang.

### 2.3 Manajemen User (Staff)

- [ ] Tambah user baru (role: Kasir).
- [ ] Edit user (ganti password).
- [ ] Nonaktifkan/Hapus user.

### 2.4 Laporan

- [ ] Buka menu **Laporan**.
- [ ] Filter tanggal laporan.
- [ ] Coba **Export PDF** dan **Export Excel**.
    - _Ekspektasi_: File terunduh dan datanya sesuai dengan tampilan web.

### 2.5 Pengaturan Toko

- [ ] Buka **Pengaturan**.
- [ ] Ubah jam operasional.
- [ ] Toggle "Buka/Tutup Toko" secara manual.
    - _Ekspektasi_: Perubahan berpengaruh pada kemampuan pelanggan melakukan booking.

---

## 3. Pengujian Fitur Kasir (Point of Sales)

_Login sebagai Kasir_

### 3.1 Dashboard Kasir

- [ ] Cek stok menipis (Low Stock Alert).

### 3.2 Persiapan Stok Kasir (Request ke Gudang)

- [ ] Buka menu **Stok Item**.
- [ ] Lakukan "Ambil dari Gudang".
- [ ] Pilih item dan jumlah.
    - _Ekspektasi_: Stok di Gudang berkurang, Stok di Kasir bertambah.

### 3.3 Barang Titipan (Consignment)

- [ ] Buka menu **Barang Titipan**.
- [ ] Tambah barang titipan baru.
- [ ] Edit & Hapus.

### 3.4 **Customer-Facing Display (Layar Pelanggan)** 🆕

- [ ] Klik link **"LAYAR PELANGGAN"** di navbar.
- [ ] Drag tab baru ke layar kedua (simulasi).
- [ ] Pastikan tampilan awal kosong ("Belum ada barang").

### 3.5 Transaksi Penjualan (POS)

- [ ] Buka menu **Transaksi**.
- [ ] **Scan Barcode / Cari Barang**:
    - [ ] Input kode barang atau scan.
    - _Ekspektasi_: Barang masuk ke tabel keranjang.
    - _Layar Pelanggan_: Barang harus muncul real-time.
- [ ] **Update Keranjang**:
    - [ ] Ubah Qty.
    - [ ] Hapus item.
    - [ ] Reset Keranjang.
    - _Layar Pelanggan_: Harus sinkron update.
- [ ] **Checkout**:
    - [ ] Pilih Member (opsional).
    - [ ] Pilih Diskon (jika ada).
    - [ ] Masukkan Nominal Bayar (Tunai/QRIS).
    - [ ] Klik **Bayar**.
    - _Layar Pelanggan_: Muncul overlay "Memproses Pembayaran".
- [ ] **Struk & Selesai**:
    - _Ekspektasi_: Transaksi berhasil, muncul halaman Struk.
    - _Layar Pelanggan_: Muncul overlay "Terima Kasih!".
    - [ ] Coba Print atau Download PDF.
    - [ ] Klik **"Kembali / Transaksi Baru"**.
    - _Layar Pelanggan_: Kembali bersih/kosong.

### 3.6 Riwayat Transaksi

- [ ] Buka **Riwayat**.
- [ ] Filter laporan hari ini.
- [ ] Klik detail transaksi untuk lihat struk ulang.

---

## 4. Pengujian Booking Online (Sisi Pelanggan)

_Login sebagai Pelanggan di browser/tab lain_

### 4.1 Membuat Pesanan

- [ ] Buka Menu Booking.
- [ ] Tambah beberapa item ke keranjang.
- [ ] Buka Keranjang -> Checkout.
- [ ] Isi detail (Waktu pengambilan, catatan).
- [ ] Kirim Pesanan.
    - _Ekspektasi_: Muncul status "Menunggu Konfirmasi".

### 4.2 Cek Status & History

- [ ] Pantau halaman status pesanan.

---

## 5. Pengujian Manajemen Booking (Sisi Kasir)

_Kembali ke tab Kasir_

### 5.1 Memproses Pesanan Masuk

- [ ] Buka menu **Pesanan** (Badge notifikasi harus muncul).
- [ ] Lihat pesanan baru dari pelanggan tadi.
- [ ] **Terima Pesanan**:
    - _Ekspektasi_: Status berubah jadi "Diproses".
    - _Sisi Pelanggan_: Status harus update jadi "Diproses".
- [ ] **Pesanan Siap**:
    - Klik "Siap diambil/disajikan".
    - _Sisi Pelanggan_: Status "Siap".
- [ ] **Selesaikan Pesanan**:
    - Proses pembayaran (jika belum lunas) -> Selesai.
    - _Ekspektasi_: Masuk ke riwayat penjualan.

---

## 6. Skenario Error (Edge Cases)

Coba lakukan hal-hal "nakal" untuk memastikan sistem aman:

1.  **Stok Habis**: Coba transaksi barang yang stoknya 0.
    - _Ekspektasi_: Muncul error/peringatan stok tidak cukup.
2.  **Input Minus**: Masukkan qty negatif atau harga negatif.
    - _Ekspektasi_: Sistem menolak.
3.  **Bayar Kurang**: Masukkan nominal bayar < total belanja.
    - _Ekspektasi_: Transaksi gagal/muncul peringatan kurang bayar.
4.  **Refresh Halaman**:
    - Sedang isi keranjang -> Refresh.
    - _Ekspektasi_: Keranjang tetap ada (session) atau reset (tergantung kebijakan, saat ini reset untuk transaksi kasir).
5.  **Akses Ilegal**:
    - Login sebagai Kasir -> Coba akses URL `/warehouse` (milik admin).
    - _Ekspektasi_: Error 403 Forbidden atau Redirect.

---

## Laporkan Bug

Jika menemukan ketidaksesuaian antara Ekspektasi dan hasil lapagan, catat:

1.  Fitur apa?
2.  Langkah yang dilakukan?
3.  Apa yang terjadi?
4.  Screenshot error (jika ada).
