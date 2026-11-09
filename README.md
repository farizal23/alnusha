# Al Nusha

Website katalog dan pemesanan online Al Nusha, dibuat dengan Laravel, SQLite, dan Vite.

## Menjalankan aplikasi

Persyaratan: PHP 8.3+, Composer, Node.js, dan npm.

```powershell
composer install
npm install
php artisan migrate --seed
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Untuk pengembangan dengan hot reload, jalankan `composer run dev`.

Database lokal menggunakan SQLite di `database/database.sqlite`. Seeder memasukkan 11 snack dan 14 produk frozen food dari daftar Al Nusha, beserta stok awal contoh. Perbarui stok sebenarnya dari dashboard sebelum menerima pesanan pelanggan.

## Halaman toko

- `/` — beranda dan pilihan produk unggulan.
- `/produk` — satu halaman seluruh produk, pencarian, serta filter Snack/Frozen food.
- `/snack` dan `/frozen-food` — alamat lama yang otomatis dialihkan ke filter kategori di `/produk`.
- `/tentang` — profil Al Nusha dan informasi pesanan partai besar.
- Detail produk, keranjang, checkout, dan konfirmasi pesanan memiliki halaman terpisah.

## Dashboard admin

Buka `http://127.0.0.1:8000/admin/masuk`. Atur `SHOP_ADMIN_EMAIL` dan `SHOP_ADMIN_PASSWORD` pada `.env` sebelum masuk; kredensial admin tidak ditanamkan di source code. Gunakan kata sandi kuat yang berbeda di setiap instalasi.

Dashboard ringkasan, halaman kelola produk (`/admin/produk`), dan halaman kelola pesanan (`/admin/pesanan`) terpisah. Pembatalan pesanan mengembalikan stok secara otomatis.

## Pemesanan dan pembayaran

Checkout menyimpan pesanan dan mengurangi stok dalam satu transaksi database. Transfer bank dan pembayaran saat diterima tersedia sebagai pilihan, lalu tim Al Nusha mengonfirmasi detail pembayaran dan pengiriman kepada pelanggan. Biaya pengiriman awal dapat diatur lewat `SHOP_SHIPPING_FEE`; tambahkan nomor WhatsApp internasional ke `SHOP_WHATSAPP_NUMBER` untuk menampilkan tautan kontak.

Gateway pembayaran dan API kurir belum diaktifkan karena kredensial penyedia dan area layanan belum diberikan. Sesuaikan biaya pengiriman, rekening, kontak, dan metode pembayaran sebelum toko dipublikasikan.
