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

## 7. ⚙️ Pengaturan & Profile
- [ ] **Edit Profile Toko**: Ganti nama toko/alamat (jika ada fitur ini).
- [ ] **Ganti Password**: Ubah password sendiri -> Logout -> Login password baru -> Berhasil.
