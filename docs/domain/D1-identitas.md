# Piagam Domain D1 — Identitas dan Sesi

**Pemilik:** Bambang  
**Pertanyaan yang dijawab:** Siapa yang sedang berbicara dengan sistem, dan apa buktinya?

## Yang dimiliki

| Jenis | Berkas / nama |
|---|---|
| Tabel | `pengguna` (Sesi 1), `sesi` (Sesi 2) |
| Model | `App\Models\Pengguna` |
| Enum | `App\Enums\StatusAkun` (`aktif`, `ditangguhkan`) |
| Controller | `RegistrasiController` |
| Request / Resource | `RegistrasiRequest`, `PenggunaResource` |
| Rute | `routes/api/identitas.php` |
| Uji | `tests/Feature/Identitas/` |
| Migration | `*_1000*.php` (nomor domain 1) |

## Endpoint Sesi 1

| Metode | Alamat | Keterangan |
|---|---|---|
| POST | `/api/v1/registrasi` | Mendaftarkan pengguna baru. Login menyusul pada Sesi 2. |

## Keputusan keamanan

- `status` **tidak** ada di `$fillable`: klien tidak dapat menentukan status akunnya
  (dan kelak peran). Controller memakai `safe()->only([...])`.
- `kata_sandi` ada di `$hidden` dan di-cast `hashed`; jangan `Hash::make()` lagi.
- Panjang kata sandi dibatasi `max:72` karena bcrypt hanya memproses 72 byte pertama.
- Surel dinormalkan (huruf kecil, tanpa spasi tepi) **sebelum** diperiksa keunikannya.
- `PenggunaResource` bekerja sebagai daftar putih; kolom baru tidak ikut keluar
  sampai sengaja dituliskan.
- `PenggunaSeeder` menolak berjalan di produksi (kata sandi contoh tertulis di repo).

## Yang disediakan untuk domain lain

- Tabel `pengguna` (PK `id`) sebagai target foreign key `toko.pemilik_id`,
  `keranjang.pengguna_id`, `dompet.pengguna_id`.
- `PenggunaFactory` (dengan state `ditangguhkan()`).
- Sembilan akun contoh pada `PenggunaSeeder`; `KatalogSeeder` mencari pemilik toko
  berdasarkan surel dari daftar ini.

## Yang dibutuhkan dari domain lain

- **D5 (Sekar):** `ResponsApi`, `GalatDomain`, dan penangan galat terpusat, agar
  bentuk respons dan galat registrasi seragam.
- Dari Sesi 3: tabel `peran`/`izin` untuk menetapkan peran pada akun.

## Rencana sesi berikutnya

- **Sesi 2** — login menerbitkan kunci sesi buatan sendiri (disimpan sebagai hash,
  berkedaluwarsa, dapat dicabut per perangkat, dapat dituntut segar untuk tindakan
  sensitif). Keranjang terikat pengguna.