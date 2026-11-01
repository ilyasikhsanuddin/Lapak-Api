# Domain D3: Keranjang dan Pesanan

**Pemilik Domain:** Rani

## 1. Tabel yang Dikelola

Domain D3 mengelola tabel berikut:
- `keranjang`
- `butir_keranjang`
- `pesanan`
- `butir_pesanan`

## 2. Tanggung Jawab Domain

Domain D3 bertanggung jawab terhadap proses keranjang belanja dan pesanan pengguna, meliputi:
- Mengelola keranjang belanja pengguna.
- Menyimpan produk dan jumlah produk dalam keranjang.
- Menghitung subtotal belanja berdasarkan data produk.
- Memisahkan belanja berdasarkan toko.
- Mengelola proses checkout dan pencatatan pesanan.

## 3. Ketergantungan Antardomain

- D1 Identitas: menggunakan data pengguna sebagai pemilik keranjang.
- D2 Katalog: menggunakan data produk dan toko untuk proses belanja.

Foreign key hanya boleh mengarah ke domain yang sama atau nomor domain yang lebih rendah.

## 4. Aturan Bisnis Terkait

- LP-1: Seluruh nilai uang menggunakan bilangan bulat rupiah.
- LP-4: Produk hanya dapat dibeli jika produk dan toko aktif serta stok mencukupi.
- LP-5: Penjual tidak boleh membeli produk dari tokonya sendiri.
- LP-6: Harga, ongkir, dan total dihitung oleh server. Harga produk disimpan pada detail pesanan saat checkout.
- LP-7: Satu checkout menghasilkan satu pesanan untuk setiap toko.
- LP-19: Uang menolak nilai negatif dan menggunakan pembulatan half-up tanpa float.
- LP-20: Simulasi mengabaikan harga dan total yang dikirim oleh klien.
- LP-21: Keranjang dari beberapa toko dipisahkan menjadi satu detail pesanan per toko.

## 5. Catatan Pengembangan

Pada Sesi 1, fokus pengembangan Domain D3 adalah fondasi skema keranjang dan pesanan sesuai pembagian tugas domain.

Tabel `pesanan` dan `butir_pesanan` termasuk kepemilikan Domain D3, dengan pengembangan kontraknya pada sesi berikutnya sesuai modul.

## 6. Batas Tanggung Jawab

Domain D3 menangani keranjang dan pesanan. Data toko dan produk menjadi tanggung jawab Domain D2, sedangkan pengelolaan dompet dan saldo menjadi tanggung jawab Domain D4.