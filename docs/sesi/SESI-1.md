# Papan Sesi 1 — Fondasi Tim dan Repositori

**Integrator:** Bambang  
**Tag penutup:** `sesi-1`  
**Kemampuan yang ditambahkan:** kontrak lima domain, registrasi, katalog publik,
simulasi belanja, buku besar, gerbang CI.

## Peta unggahan

Kolom *Status* diisi integrator: `draf` → `siap ditinjau` → `disetujui` → `merged`.

| PR | Unggahan | Cabang | Penulis | Isi | Peninjau | Menunggu | Status |
|---|---|---|---|---|---|---|---|
| 1 | Pembuka | `sesi-1/fondasi` | Kelimanya | Kerangka Laravel, konfigurasi, pemuat rute, dokumen tim | Dua anggota | - | |
| 2 | A1 | `sesi-1/kontrak-respons-sekar` | Sekar | Amplop respons dan induk galat domain | Bambang | 1 | |
| 3 | A2 | `sesi-1/kontrak-uang-rani` | Rani | Value object Uang dan ujinya | Dimas | 1 | |
| 4 | A3 | `sesi-1/kontrak-identitas-bambang` | Bambang | Tabel, model, factory pengguna, akun contoh | Bagas | 1 | |
| 5 | A4 | `sesi-1/kontrak-katalog-bagas` | Bagas | Skema dan model katalog, scope tampil | Rani | 4 | |
| 6 | A5 | `sesi-1/kontrak-dompet-dimas` | Dimas | Skema dan model dompet | Sekar | 4 | |
| 7 | B1 | `sesi-1/galat-dan-ci-sekar` | Sekar | Penanganan galat, health, uji kontrak, CI | Bambang | 2 | |
| 8 | B2 | `sesi-1/registrasi-bambang` | Bambang | Registrasi dan ujinya | Bagas | 4, 7 | |
| 9 | B3 | `sesi-1/katalog-bagas` | Bagas | Endpoint katalog, seeder, uji | Rani | 3, 5, 7 | |
| 10 | B4 | `sesi-1/belanja-rani` | Rani | Keranjang, penghitung, simulasi, uji | Dimas | 3, 5, 7 | |
| 11 | B5 | `sesi-1/buku-besar-dimas` | Dimas | Buku besar, pemeriksaan, seeder, uji | Sekar | 2, 3, 6, 7 | |
| 12 | Penutup | `sesi-1/penutup-bambang` | Bambang | Papan sesi, tempat penegakan aturan, skrip rekap | Bagas | 11 | |

## Urutan merge

- Jendela A: **A1 → A2 → A3**, lalu **A4 → A5** (menunggu tabel `pengguna` dari A3).
- Jendela B: **B1 → B2 → B3 → B4 → B5**. B1 lebih dulu karena uji anggota lain
  memeriksa bentuk galat yang dibuat di sana.
- Tombol merge hanya ditekan integrator. Setelah B1 masuk, aktifkan `kualitas` pada
  *Require status checks to pass before merging*.

## Catatan integrator

- Uji bentuk galat (`galat.kode`, `galat.rincian`) merah di cabang sendiri sampai B1
  masuk `main` dan cabang di-rebase. Itu ketergantungan yang tercatat, bukan kerusakan.
- Konflik **diharapkan** pada `database/seeders/DatabaseSeeder.php`. Penyelesaian:
  **simpan ketiga seeder**, jangan memilih satu sisi.
- Merge memakai **merge commit** (squash dan rebase merge dimatikan) agar commit tiap
  anggota tetap utuh di `main`.

## Verifikasi `main`

    git switch main
    git pull origin main
    composer install
    php artisan migrate:fresh --seed
    ./vendor/bin/pint --test
    ./vendor/bin/pest
    php artisan lapak:periksa-buku
    php artisan route:list --path=api/v1

- [ ] `migrate:fresh --seed` lolos dari basis data kosong
- [ ] Pint lolos, Pest hijau
- [ ] `routes/api.php` tidak berubah sejak PR pembuka
- [ ] Uji manual lolos

## Catatan penutup

_Diisi integrator pada PR penutup: kendala, satu pelajaran dari konflik merge,
keluaran `scripts/rekap-sesi.sh 1`, dan titipan untuk integrator Sesi 2._

## Serah terima ke integrator Sesi 2

- [ ] `main` hijau dan tag `sesi-1` ada di remote
- [ ] Tidak ada PR terbuka dan tidak ada cabang `sesi-1/*` yang tersisa
- [ ] Papan sesi dan kolom *Ditegakkan di* sudah terisi
- [ ] Bagas sudah menyalin kerangka papan ini menjadi `docs/sesi/SESI-2.md`