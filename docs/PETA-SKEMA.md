# Peta Skema Basis Data Lapak

## 1. Kepemilikan Tabel

| Domain | Nama Domain | Pemilik | Tabel |
|---|---|---|---|
| D1 | Identitas dan Sesi | Bambang | pengguna, sesi |
| D2 | Toko dan Katalog | Bagas | toko, kategori, produk |
| D3 | Keranjang dan Pesanan | Rani | keranjang, butir_keranjang, pesanan, butir_pesanan |
| D4 | Pembayaran dan Saldo | Dimas | dompet, mutasi_saldo, pembayaran, pencairan |
| D5 | Otorisasi, Audit, dan Kualitas | Sekar | peran, izin, kunci_idempotensi, log_audit |

## 2. Aturan Penomoran Migration

Nama file migration menggunakan pola:

`2026_10_SS_DNNNNN_nama.php`

Keterangan:
- SS: nomor sesi.
- D: nomor domain.
- NNNNN: nomor urut migration.

Contoh:

`2026_10_01_200003_buat_tabel_produk.php`

File tersebut merupakan migration ketiga milik Domain D2 pada Sesi 1.

## 3. Arah Foreign Key

Foreign key hanya boleh menunjuk ke domain dengan nomor yang sama atau lebih kecil.

Arah ketergantungan antardomain:

```text
pengguna (D1) <-- toko (D2) <-- produk (D2) <-- butir_keranjang (D3)
pengguna (D1) <-- keranjang (D3)
pengguna (D1) <-- dompet (D4) <-- mutasi_saldo (D4)