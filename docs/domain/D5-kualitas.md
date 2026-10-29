\# Piagam Domain D5 — Otorisasi, Audit, dan Kualitas



\*\*Pemilik:\*\* Sekar (Ahmad Shobah Rahmadani)  

\*\*Pertanyaan yang dijawab:\*\* Apakah sistem aman, konsisten bentuknya, dan dapat dibuktikan benar setiap kali kode berubah?



\## Yang dimiliki



| Jenis | Berkas / nama |

|---|---|

| Tabel | `peran`, `izin` (Sesi 3), `kunci\_idempotensi` (Sesi 4), `log\_audit` (Sesi 5) |

| Trait | `App\\Http\\Concerns\\ResponsApi` (A1, sudah diunggah di PR #6) |

| Kelas galat | `App\\Exceptions\\GalatDomain` (induk), `App\\Exceptions\\PenanganGalatApi` (B1) |

| Middleware | `PaksaJson`, `IdPermintaan` (B1) |

| Berkas bersama | `bootstrap/app.php`, `app/Providers/`, `routes/api.php`, `routes/api/sistem.php`, `config/lapak.php`, `composer.json`, `.github/` |

| Dokumen | `docs/KONTRAK-API.md` |

| Uji | `tests/Feature/Sistem/` (kontrak galat, uji arsitektur) |

| Migration | `\*\_5000\*.php` (nomor domain 5) |



\## Endpoint Sesi 1



| Metode | Alamat | Keterangan |

|---|---|---|

| GET | `/api/v1/health` | Memeriksa aplikasi dan koneksi basis data (B1). |



\## Keputusan kualitas



\- \*\*Satu amplop respons.\*\* Semua controller mewarisi `ResponsApi`, sehingga bentuk sukses dan halaman tidak dirakit sendiri per domain.

\- \*\*Satu pintu galat.\*\* `PenanganGalatApi` mengubah setiap pengecualian menjadi amplop galat. Controller tidak perlu `try-catch`.

\- \*\*Galat tak terduga tidak bocor.\*\* Rincian aslinya hanya masuk log. Klien hanya menerima `GALAT\_SERVER`, kecuali saat debug menyala.

\- \*\*Model ketat di luar produksi.\*\* `Model::shouldBeStrict()` aktif selama bukan production, agar atribut salah ketik cepat ketahuan.

\- \*\*Gerbang CI.\*\* Alur CI menjalankan `pint --test`, `migrate:fresh --seed`, dan `pest --ci` pada setiap PR.

\- \*\*Uji arsitektur.\*\* Melarang `dd`/`dump` dan kolom float, double, decimal di migration (LP-1: uang adalah bilangan bulat rupiah).



\## Yang dibutuhkan dari domain lain



\- \*\*D1 (Identitas):\*\* model `Pengguna` untuk relasi peran (Sesi 3) dan pelaku pada `log\_audit`.

\- \*\*D4 (Dompet):\*\* hasil `php artisan lapak:periksa-buku` untuk dipanggil dari alur CI.



\## Yang disediakan untuk domain lain



\- `ResponsApi::sukses()` dan `berhalaman()`.

\- `GalatDomain` sebagai induk galat aturan bisnis (kode aturan, pesan, status HTTP, rincian).

\- Bentuk galat yang tetap, sehingga uji domain lain dapat memeriksa `galat.kode` dan `galat.rincian`.

\- Gerbang CI yang menolak PR bila gaya kode, migrasi, atau uji gagal.



\## Status Sesi 1



| Bagian | Status |

|---|---|

| A1: `ResponsApi` dan `Controller` | PR #6, menunggu tinjauan |

| B1: penanganan galat, health, CI | Menunggu jendela A selesai di-merge |

