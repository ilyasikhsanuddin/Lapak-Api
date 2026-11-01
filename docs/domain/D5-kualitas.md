\# D5 Otorisasi, Audit, dan Kualitas



> \*\*STATUS: DRAF.\*\* Disusun dari tabel domain, CODEOWNERS, dan rencana unggahan pada modul

> Hands-On Lapak Sesi 1. Bagian "Yang disediakan" dan "Yang dibutuhkan" perlu

> dibahas bersama tim dan dibacakan pada awal Sesi 2.



\*\*Pemilik:\*\* `@ahmadshobahrahmadani-ux` (TODO: tulis nama anggota seperti yang dipakai tim pada dokumen domain lain)



\*\*Pertanyaan yang dijawab domain ini:\*\* Siapa boleh melakukan apa, siapa yang sudah melakukan apa,

dan apakah `main` layak dipercaya?



\*\*Tabel milik domain:\*\* `peran`, `izin`, `kunci\_idempotensi`, `log\_audit`.

Migration domain ini memakai digit domain `5` pada nama berkas, pola `2026\_10\_SS\_5NNNNN\_nama.php`,

dan dimiliki D5 menurut CODEOWNERS.



\## Aturan bisnis yang ditegakkan domain ini



| Kode  | Aturan                                                                     | Sesi |

|-------|----------------------------------------------------------------------------|------|

| LP-1  | Uang bilangan bulat rupiah, ditegakkan oleh uji arsitektur pada CI         | 1    |

| LP-2  | Kewenangan ditentukan izin yang melekat pada peran, bukan nama peran       | 3    |

| LP-11 | Permintaan pengubah uang atau stok wajib membawa `Idempotency-Key`         | 4    |

| LP-17 | Tindakan sensitif tercatat pada log audit yang hanya dapat ditambah, berantai hash | 5 |



\## Berkas milik domain



Sesuai `.github/CODEOWNERS`, D5 memiliki seluruh berkas bersama:



\- `app/Exceptions/`, `app/Http/Concerns/`, `app/Http/Middleware/`, `app/Providers/`

\- `bootstrap/app.php`, `routes/api.php`, `routes/api/sistem.php`

\- `config/lapak.php`, `composer.json`, `.github/`

\- `docs/KONTRAK-API.md`, `tests/Feature/Sistem/`

\- `database/seeders/DatabaseSeeder.php`

\- Migration dengan digit domain `5`



Anggota domain lain boleh menyentuh berkas ini bila perlu, tetapi harus menyebutnya pada bagian

"Berkas bersama yang disentuh" di deskripsi PR. Pemilik D5 diminta meninjau otomatis.



\## Rencana per sesi



| Sesi | Unggahan kontrak (A)                                      | Unggahan implementasi (B)                                           |

|------|-----------------------------------------------------------|---------------------------------------------------------------------|

| 1    | `ResponsApi`, `GalatDomain`                               | Penangan galat, middleware, health, uji kontrak, uji arsitektur, CI |

| 2    | Pendaftaran guard sesi dan alias middleware               | Pembatasan percobaan login, uji 401 seluruh endpoint terlindung, pemangkasan sesi kedaluwarsa |

| 3    | Tabel peran, izin, pivot, enum izin, middleware izin      | Seeder peran, cache izin, uji matriks otorisasi peran kali endpoint |

| 4    | Tabel `kunci\_idempotensi`, middleware idempoten (LP-11)   | Uji balapan dua checkout serentak pada MySQL, tugas CI kedua dengan MySQL |

| 5    | Tabel `log\_audit`, antarmuka pencatat audit               | Rantai hash, perintah verifikasi, endpoint auditor (LP-17), rilis v1.0.0 |



Pada Sesi 5 pemilik D5 menjadi integrator menurut rencana modul. Tabel ini mengikuti lampiran

modul dan dapat berubah menurut papan sesi.





\## Yang disediakan untuk domain lain



| Yang disediakan                | Wujud                                              | Cara memakai                                              |

|--------------------------------|----------------------------------------------------|-----------------------------------------------------------|

| Amplop sukses                  | Trait `ResponsApi` pada `Controller`               | `$this->sukses(...)`, `$this->berhalaman(...)`            |

| Induk galat aturan bisnis      | Kelas `GalatDomain`                                | Turunkan atau lempar dengan kode `LP-n`                   |

| Penangan galat terpusat        | `PenanganGalatApi`                                 | Tidak perlu `try-catch` di controller                     |

| Pengenal permintaan            | Middleware `IdPermintaan`, header `X-Request-Id`   | Otomatis pada grup `api`                                  |

| Pemaksa JSON                   | Middleware `PaksaJson`                             | Otomatis pada grup `api`                                  |

| Endpoint health                | `GET /api/v1/health`                               | Pemeriksaan hidup dan basis data                          |

| Mode ketat Eloquent            | `Model::shouldBeStrict` di luar produksi           | Hindari lazy loading, gunakan `with()`                    |

| Gerbang CI                     | `.github/workflows/ci.yml`, tugas `kualitas`       | Pint, `migrate:fresh --seed`, Pest pada setiap PR ke `main` |

| Kontrak API tertulis           | `docs/KONTRAK-API.md`                              | Rujukan bentuk respons seluruh domain                     |



\## Yang dibutuhkan dari domain lain



\*\*TODO, diisi bersama tim sebelum Sesi 2.\*\* Usulan awal dari ketergantungan yang terlihat di modul:



| Dari domain  | Kebutuhan D5                                                              | Sesi |

|--------------|---------------------------------------------------------------------------|------|

| D1 Identitas | Tabel `pengguna` dan `sesi` sebagai sasaran foreign key peran dan audit   | 2, 3 |

| D1 Identitas | Penerbit dan pemeriksa kunci sesi, untuk didaftarkan sebagai guard        | 2    |

| D3 Belanja   | Titik checkout dan pembayaran yang akan dipasangi middleware idempoten    | 4    |

| D4 Saldo     | Antarmuka pencatat untuk peristiwa uang yang harus masuk log audit        | 5    |



\## Cara bekerja dengan domain ini



\- Menambah kode galat baru: tambahkan ke `docs/KONTRAK-API.md` pada PR yang sama dengan kode yang

&#x20; melemparnya.

\- Menambah rute: sunting `routes/api/<domain>.php` milik domain Anda, jangan `routes/api.php`.

\- Uji Anda tiba-tiba gagal setelah rebase ke `main`: baca pesan galatnya. Mode ketat Eloquent hampir

&#x20; selalu diatasi dengan `with()` pada kueri atau `refresh()` setelah `create()`.

