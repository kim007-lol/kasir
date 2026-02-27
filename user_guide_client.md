# **USER GUIDE APLIKASI KASIRKU (SMEGABIZ)**

Panduan ini menjelaskan cara penggunaan aplikasi **Kasirku (SMEGABIZ)** untuk tiga jenis pengguna:

* **Admin**
* **Kasir**
* **Pelanggan (Booking Online / SmeGo)**

---

# 1. HALAMAN UTAMA (Landing Page) — `/`

Halaman ini ditampilkan kepada pengunjung yang belum masuk (login) ke dalam sistem.

### Fungsi Utama

| Menu/Tombol                 | Keterangan                                                                         |
| --------------------------- | ---------------------------------------------------------------------------------- |
| Login Staff                 | Digunakan oleh Admin dan Kasir untuk masuk ke sistem                               |
| Daftar / Login Pelanggan    | Digunakan pelanggan untuk membuat akun atau masuk untuk melakukan pemesanan online |
| Pesan Sekarang / Lihat Menu | Mengarahkan langsung ke halaman menu pemesanan online                              |
| Ikon Keranjang              | Membuka halaman keranjang belanja pelanggan                                        |

### Catatan

Jika pengguna sudah login, sistem akan otomatis mengarahkan ke halaman sesuai peran:

* Admin → Dashboard Admin
* Kasir → Dashboard Kasir
* Pelanggan → Halaman Menu Booking

---

# 2. HALAMAN LOGIN

## 2.1 Login Staff (Admin dan Kasir)

Field yang harus diisi:

* Email
* Password (kata sandi)

Setelah berhasil login, pengguna akan masuk ke dashboard sesuai perannya.

---

## 2.2 Login dan Registrasi Pelanggan

Pelanggan dapat:

* Mendaftar akun baru
* Login ke akun yang sudah terdaftar
* Melakukan reset password (jika fitur tersedia)

Akun pelanggan diperlukan untuk melakukan pemesanan online.

---

# 3. PANEL ADMIN

Panel Admin menggunakan tampilan dengan menu di sisi kiri (Sidebar).

## Menu Utama Admin

| Menu              | Fungsi                                        |
| ----------------- | --------------------------------------------- |
| Dashboard         | Menampilkan ringkasan penjualan dan statistik |
| Gudang            | Mengelola stok barang utama                   |
| Supplier          | Mengelola data pemasok barang                 |
| Kategori          | Mengelompokkan produk berdasarkan jenis       |
| Stok Item Kasir   | Mengatur barang yang tersedia untuk dijual    |
| Stock Opname      | Menyesuaikan stok fisik dengan stok di sistem |
| Histori Transaksi | Melihat riwayat transaksi yang sudah selesai  |
| Member            | Mengelola pelanggan tetap                     |
| Laporan           | Melihat dan mengunduh laporan keuangan        |
| Kelola User       | Mengatur akun Admin dan Kasir                 |
| Logout            | Keluar dari sistem                            |

---

## 3.1 Dashboard Admin

Menampilkan informasi penting seperti:

* Total pendapatan hari ini
* Jumlah transaksi
* Peringatan stok hampir habis
* Grafik penjualan

Dashboard membantu Admin memantau kondisi bisnis secara cepat.

---

## 3.2 Gudang

Digunakan untuk mengelola stok barang utama sebelum dijual di kasir.

Fitur:

* Tambah produk baru
* Edit data produk
* Hapus produk (dengan konfirmasi)
* Pencarian produk
* Indikator warna stok:

  * Hijau: Aman
  * Kuning: Mulai menipis
  * Merah: Stok hampir habis

---

## 3.3 Supplier

Digunakan untuk mengelola data pemasok barang.

Fitur:

* Tambah supplier
* Edit supplier
* Nonaktifkan supplier (data tidak terhapus permanen)
* Aktifkan kembali supplier

---

## 3.4 Kategori

Mengatur pengelompokan produk seperti:

* Makanan
* Minuman
* Snack

Kategori membantu sistem dalam pengorganisasian menu dan laporan.

---

## 3.5 Stok Item Kasir

Menampilkan produk yang siap dijual di meja kasir.

Fitur:

* Tambah item ke kasir
* Edit harga jual dan stok
* Hapus item
* Indikator warna stok:

  * Merah: Kurang dari 10
  * Kuning: 10–20
  * Hijau: Aman

---

## 3.6 Stock Opname

Stock opname adalah proses mencocokkan stok fisik (barang nyata) dengan stok yang tercatat di sistem.

Fitur:

* Membuat penyesuaian stok
* Melihat riwayat penyesuaian
* Menyimpan hasil perhitungan

---

## 3.7 Histori Transaksi

Menampilkan seluruh transaksi yang telah selesai.

Fitur:

* Filter berdasarkan tanggal
* Filter metode pembayaran (Tunai / QRIS)
* Lihat detail transaksi
* Unduh struk dalam format PDF
* Cetak struk printer kasir

---

## 3.8 Member

Mengelola pelanggan tetap.

Fitur:

* Tambah member
* Edit data member
* Nonaktifkan member
* Aktifkan kembali member

---

## 3.9 Laporan

Digunakan untuk melihat laporan keuangan dan pergerakan stok.

Fitur:

* Filter berdasarkan periode tanggal
* Unduh laporan dalam format PDF
* Unduh laporan dalam format Excel
* Melihat riwayat stok masuk dan transfer barang

---

## 3.10 Kelola User

Mengatur akun Admin dan Kasir.

Fitur:

* Tambah user baru
* Edit user
* Nonaktifkan akun
* Aktifkan kembali akun

---

# 4. PANEL KASIR

Panel Kasir menggunakan menu di bagian atas (Navbar).

## Menu Utama Kasir

| Menu            | Fungsi                                |
| --------------- | ------------------------------------- |
| Dashboard       | Ringkasan dan shortcut menu           |
| Transaksi       | Melayani pembelian langsung           |
| Stok            | Mengelola stok kasir                  |
| Histori         | Melihat riwayat transaksi dan booking |
| Pesanan         | Melihat pesanan online masuk          |
| Layar Pelanggan | Tampilan untuk monitor pelanggan      |
| Logout          | Keluar dari sistem                    |

---

## 4.1 Dashboard Kasir

Menampilkan:

* Notifikasi pesanan baru
* Peringatan stok hampir habis
* Shortcut ke halaman transaksi
* Informasi pengaturan toko

Notifikasi pesanan online muncul otomatis tanpa perlu memuat ulang halaman.

---

## 4.2 Transaksi (Point of Sale)

Halaman utama untuk melayani pembeli secara langsung.

### Bagian Kiri – Daftar Produk

* Pencarian produk (bisa menggunakan barcode scanner)
* Pilih jumlah (qty)
* Tambahkan ke keranjang
* Indikator stok dengan warna

Jika barcode dipindai dan hanya ada satu produk yang sesuai, sistem otomatis menambahkan produk ke keranjang.

---

### Bagian Kanan – Keranjang dan Pembayaran

Fitur:

* Daftar item yang dibeli
* Total belanja
* Diskon (khusus Admin)
* Input uang pelanggan
* Perhitungan kembalian otomatis
* Metode pembayaran: Tunai atau QRIS
* Tombol Bayar aktif jika jumlah uang mencukupi

---

## 4.3 Stok Item Kasir

Digunakan untuk:

* Menambah barang dari gudang
* Menambah barang titipan (konsinyasi)
* Melihat detail harga dan stok

---

## 4.4 Barang Titipan (Konsinyasi)

Barang dari pihak ketiga yang dijual di kasir.

Fitur:

* Tambah barang
* Edit barang
* Hapus barang

---

## 4.5 Pesanan Online

Digunakan untuk mengelola pesanan dari pelanggan online.

Status pesanan:

* Pending (Menunggu)
* Diterima
* Diproses
* Siap
* Selesai
* Ditolak

Saat ada pesanan baru, sistem memberikan notifikasi suara dan pemberitahuan di layar.

---

## 4.6 Layar Pelanggan

Halaman khusus untuk ditampilkan pada monitor kedua yang menghadap pelanggan.

Menampilkan:

* Daftar belanja secara langsung
* Total harga
* Status pembayaran

---

## 4.7 Pengaturan Toko

Mengatur jam operasional toko untuk sistem booking online.

Fitur:

* Atur jam buka
* Atur jam tutup
* Simpan pengaturan
* Pengaturan darurat (membuka/menutup toko di luar jam normal)

---

# 5. SISTEM BOOKING ONLINE (SmeGo) – PANEL PELANGGAN

## 5.1 Menu

Pelanggan dapat:

* Melihat daftar makanan dan minuman
* Memfilter berdasarkan kategori
* Melihat stok tersedia
* Menambahkan ke keranjang

Jika toko sedang tutup, pelanggan hanya dapat melihat menu tanpa melakukan pemesanan.

---

## 5.2 Keranjang

Pelanggan dapat:

* Mengubah jumlah pesanan
* Menambahkan catatan khusus
* Melihat total pembayaran secara otomatis
* Menghapus item
* Melanjutkan ke checkout

---

## 5.3 Checkout

Halaman konfirmasi sebelum pesanan dikirim.

Menampilkan:

* Ringkasan pesanan
* Total pembayaran
* Nama pelanggan

Setelah menekan tombol "Pesan Sekarang", pesanan masuk ke kasir dengan status **Pending**.

---

## 5.4 Status Pesanan

Pelanggan dapat memantau perkembangan pesanan secara otomatis.

Urutan status:
Pending → Diterima → Diproses → Siap → Selesai

---

## 5.5 Riwayat Pesanan

Menampilkan semua pesanan yang pernah dibuat beserta statusnya.

---

# KODE WARNA SISTEM

| Warna  | Arti                     |
| ------ | ------------------------ |
| Hijau  | Sukses / Aman            |
| Kuning | Peringatan / Menunggu    |
| Merah  | Kritis / Ditolak / Habis |
| Biru   | Sedang Diproses          |

---

# CATATAN PENTING

* Barcode scanner dapat langsung digunakan di halaman Transaksi.
* Layar Pelanggan sebaiknya dibuka di monitor terpisah.
* Notifikasi pesanan online muncul otomatis tanpa perlu memuat ulang halaman.
