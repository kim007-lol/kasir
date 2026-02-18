# Panduan Fitur & UX: UI Pemesanan Pelanggan 📱

Dokumen ini berisi daftar **Fitur Wajib** yang harus ada di tampilan pelanggan agar sistem berjalan lancar. Gunakan ini sebagai checklist saat membuat UI.

---

## 1. Halaman Menu (`/booking/menu`)

Halaman utama di mana pelanggan memilih makanan.

### ✅ Fitur Wajib:

- **Kategori Filter:** Tombol/Tabs untuk memfilter makanan (misal: "Makanan", "Minuman", "Snack").
- **Search Bar:** Input pencarian nama makanan.
- **Card Menu:**
    - Gambar makanan.
    - Nama & Harga.
    - **Indikator Stok:**
        - Jika stok > 0: Tampilkan tombol "Tambah".
        - Jika stokHabis: Tampilkan label "Habis" (tombol disable).
    - **Input Qty:** Kolom angka (default 1) sebelum tombol tambah.
- **Floating Cart Button:** Tombol melayang (atau di navbar) yang menampilkan jumlah item di keranjang.
- **Cek Toko Tutup:** Tampilkan banner/alert "Toko Tutup" jika jam di luar operasional (07:00 - 15:00) dan disable semua tombol pesan.

---

## 2. Keranjang Belanja (`/booking/cart`)

Tempat review pesanan sebelum checkout.

### ✅ Fitur Wajib:

- **List Item:** Tabel/List item yang dipilih.
    - Nama, Harga Satuan, Qty, Subtotal.
- **Edit Qty/Notes:**
    - Tombol +/- untuk ubah jumlah.
    - Input text untuk "Catatan" (misal: "Jangan pedas", "Es sedikit").
    - Tombol **Update** (penting untuk menyimpan perubahan ke session).
- **Hapus Item:** Tombol tong sampah/hapus per item.
- **Ringkasan Bayar:** Total harga keseluruhan.
- **Tombol Checkout:** Lanjut ke halaman konfirmasi.

---

## 3. Checkout (`/booking/checkout`)

Langkah terakhir. Simple dan cepat.

### ✅ Fitur Wajib:

- **Konfirmasi User:** Tampilkan "Memesan sebagai: Nama User".
- **Pilih Jam Ambil:** Input `time` yang dibatasi (min: jam sekarang + 15 menit, max: 15:00).
- **Metode Pembayaran:**
    - Tampilkan info "Bayar di Kasir (Tunai/QRIS)".
    - (Opsional: Upload bukti transfer jika fitur ada, tapi saat ini default bayar di kasir).
- **Summary Akhir:** Total item & Total harga.
- **Tombol "Buat Pesanan":** Submit form.

---

## 4. Status Pesanan (`/booking/status/{id}`)

Halaman "Live Tracking" setelah pesanan dibuat.

### ✅ Fitur Wajib:

- **Status Badge:** Tampilan visual status sekarang.
    - 🕒 **Menunggu Konfirmasi:** Saat baru order.
    - 👨‍🍳 **Sedang Disiapkan:** Saat kasir memproses.
    - ✅ **Siap Diambil:** Saat makanan sudah jadi.
    - 🎉 **Selesai:** Saat sudah diambil & dibayar.
- **Auto-Refresh:** Tambahkan script _polling_ (refresh otomatis setiap 10-30 detik) atau tombol "Refresh Status" manual agar pelanggan tau kalau pesanan sudah siap.
- **Detail Pesanan:** List item yang dipesan tadi (read-only).
- **Kode Booking/QR:** Tampilkan Kode Booking besar-besar untuk ditunjukkan ke kasir.

---

## 5. Riwayat Pesanan (`/booking/history`)

Daftar pesanan lampau.

### ✅ Fitur Wajib:

- **List Card:** Tampilkan pesanan-pesanan sebelumnya.
- **Info Singkat:** Tanggal, Total Harga, Status Terakhir.
- **Tombol Detail:** Link ke halaman Status masing-masing pesanan.
- **Status Color:** Bedakan warna (Merah = Batal, Hijau = Selesai, Kuning = Proses).

---

## 💡 Tips UX (User Experience)

1. **Mobile First:** Sebagian besar pelanggan akan memesan lewat HP. Pastikan tombol cukup besar untuk jari jempol.
2. **Feedback:** Beri notifikasi "Berhasil masuk keranjang" (Toast/Alert) setiap klik tombol pesan.
3. **Navigasi:** Pastikan tombol "Kembali ke Menu" selalu ada di halaman Cart/Checkout.

Selamat berkreasi! ✨
