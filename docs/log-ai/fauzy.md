# Log Penggunaan AI - Sesi 1

**Nama Anggota:** Fauzy Akhbar (Dimas - Domain D4: Pembayaran dan Saldo)

---

## 1. Fondasi Bersama
- Menggunakan Antigravity/Gemini untuk memandu instalasi Git, konfigurasi awal proyek Laravel 11, dan penyusunan berkas fondasi domain D4 (`CONTRIBUTING.md`, `.github/CODEOWNERS`, `pull_request_template.md`).

## 2. Jendela A (Kontrak A5: Dompet & Mutasi Saldo)
- Mengonfirmasi skema migration `buat_tabel_dompet` dan `buat_tabel_mutasi_saldo` serta model Eloquent `Dompet` dan `MutasiSaldo`.
- Menemukan dan menyelesaikan kendala pembuatan tabel: file migrasi sebelumnya masih berupa stub kosong sehingga tabel fisik belum tercipta di MySQL meski status migration `DONE`.
- Menyesuaikan relasi foreign key `pengguna_id` yang memerlukan kontrak tabel `pengguna` dari domain D1.

## 3. Jendela B (Implementasi B5: Buku Besar & Periksa Buku)
- Mengimplementasikan service `App\Services\BukuBesar` dengan mekanisme transaksi atomik, penguncian baris (`lockForUpdate`), dan pencatatan sepasang mutasi saldo (LP-13).
- Membuat command `php artisan lapak:periksa-buku` untuk memvalidasi 4 invarian buku besar.
- Menulis rangkaian pengujian otomatis `BukuBesarTest` dengan Pest (alur escrow lengkap, saldo tidak cukup, mutasi append-only, deteksi kecurangan).

## 4. Latihan Mandiri (6, 7, 8)
- **Latihan Mandiri 6**: Mengimplementasikan command `php artisan lapak:mutasi {kode}` untuk menelusuri riwayat mutasi dompet serta menghitung saldo berjalan, dilengkapi pengujian `RiwayatMutasiTest`.
- **Latihan Mandiri 7**: Menulis uji `PenjagaBasisDataTest` untuk membuktikan bahwa kueri massal query builder dapat meloloskan update dan delete (karena membypass lifecycle event Eloquent), serta menyusun dokumen usulan mitigasi tingkat basis data (triggers & privilege restrictions) di `docs/domain/D4-dompet.md`.
- **Latihan Mandiri 8**: Melengkapi piagam domain D4 pada `docs/domain/D4-dompet.md` (kebutuhan dan layanan antardomain).
- Memastikan seluruh kode lulus styling `./vendor/bin/pint --test` dan pengujian `./vendor/bin/pest`.