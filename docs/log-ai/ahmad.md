\# Log Penggunaan AI: Sekar (D5)

> Hapus catatan ini setelah selesai. Bagian bertanda \*\*TODO\*\* harus diisi sendiri setelah Anda memeriksanya.

\## Sesi 1

| Bagian                         | Alat   | Peran AI                                                                 |
|--------------------------------|--------|--------------------------------------------------------------------------|
| `docs/KONTRAK-API.md`          | Claude | Menyusun draf dari kode `ResponsApi`, `GalatDomain`, `PenanganGalatApi`, `KesehatanController` pada modul Hands-On Lapak Sesi 1 |
| `docs/domain/D5-kualitas.md`   | Claude | Menyusun draf dari tabel domain, CODEOWNERS, dan lampiran rencana sesi   |
| `docs/log-ai/ahmad.md`         | Claude | Menyusun kerangka log ini                                                |

Berkas pendamping `dokumen-repositori/` belum tersedia saat dokumen ini ditulis, sehingga ketiganya
disusun dari isi modul PDF, bukan disalin dari berkas resmi.

\## Cara memverifikasi

\*\*TODO, isi setelah memeriksa.\*\* Centang hanya yang benar-benar dilakukan:

\- \[ ] Membandingkan `KONTRAK-API.md` dengan versi resmi dari dosen atau integrator
\- \[ ] Mencocokkan tabel kode galat dengan `app/Exceptions/PenanganGalatApi.php` di cabang `sesi-1/galat-dan-ci-sekar`
\- \[ ] Mencocokkan contoh JSON dengan hasil `./vendor/bin/pest` pada `tests/Feature/Sistem/KontrakGalatTest.php`
\- \[ ] Menetapkan zona waktu bersama tim dan mengisi bagian TODO pada `KONTRAK-API.md`
\- \[ ] Mengisi bagian "Yang dibutuhkan dari domain lain" pada `D5-kualitas.md` bersama tim

\## Hal yang tidak dipakai AI

\*\*TODO:\*\* tulis bagian pekerjaan Sesi 1 yang Anda kerjakan tanpa AI, atau hapus bagian ini bila tidak ada.
