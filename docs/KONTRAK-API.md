\# Kontrak API Lapak

> \*\*STATUS: DRAF.\*\* Disusun dari kode `ResponsApi`, `GalatDomain`, `PenanganGalatApi`, dan
> `KesehatanController` pada modul Hands-On Lapak Sesi 1. Cocokkan dengan versi resmi dari dosen
> atau integrator sebelum di-merge. Bagian bertanda \*\*TODO\*\* belum ditetapkan oleh sumbernya.

Berkas ini mengunci bentuk respons seluruh endpoint. Mengubah bentuk di sini berarti mengubah kontrak,
dan perubahannya harus lewat Pull Request yang ditinjau pemilik D5 (lihat `.github/CODEOWNERS`).

\## 1. Prinsip umum

\- API hanya berbicara JSON. Middleware `PaksaJson` memaksa header `Accept: application/json`.
\- Semua endpoint berada di bawah awalan `/api/v1`.
\- Setiap respons membawa header `X-Request-Id` (UUID buatan server). Nilai kiriman klien tidak dipakai.
&#x20; Pengenal yang sama muncul di setiap baris log permintaan itu.
\- Controller tidak merakit respons sendiri. Sukses lewat trait `ResponsApi`, galat lewat
&#x20; `PenanganGalatApi`.
\- Permintaan yang mengubah uang atau stok wajib membawa header `Idempotency-Key` (LP-11).
&#x20; Kunci yang sama menghasilkan respons yang sama dan efek satu kali. Penegakannya dibangun pada Sesi 4.

\## 2. Amplop sukses

```json
{
&#x20; "sukses": true,
&#x20; "data": { },
&#x20; "meta": null
}
```

| Kolom   | Tipe               | Keterangan                                              |
|---------|--------------------|---------------------------------------------------------|
| sukses  | boolean            | Selalu `true` pada respons sukses.                      |
| data    | objek, larik, null | Isi respons.                                            |
| meta    | objek atau null    | `null` bila tidak ada meta.                             |

Status HTTP sukses: `200` untuk pembacaan dan perubahan, `201` untuk pembuatan sumber daya baru.

\### Respons berhalaman

Daftar yang berhalaman memakai amplop yang sama. `data` berisi larik, `meta` berisi:

```json
{
&#x20; "sukses": true,
&#x20; "data": \[ ],
&#x20; "meta": {
&#x20;   "halaman": 1,
&#x20;   "per\_halaman": 12,
&#x20;   "total": 48,
&#x20;   "halaman\_terakhir": 4
&#x20; }
}
```

Batas halaman katalog: bawaan 12, maksimum 60 (`config/lapak.php`).

\## 3. Amplop galat

```json
{
&#x20; "sukses": false,
&#x20; "galat": {
&#x20;   "kode": "TIDAK\_DITEMUKAN",
&#x20;   "pesan": "Sumber daya tidak ditemukan.",
&#x20;   "rincian": null
&#x20; }
}
```

| Kolom   | Tipe            | Keterangan                                                        |
|---------|-----------------|-------------------------------------------------------------------|
| sukses  | boolean         | Selalu `false` pada respons galat.                                |
| kode    | string          | Kode mesin-baca. Klien dan uji merujuk kode, bukan teks pesan.    |
| pesan   | string          | Kalimat untuk manusia, berbahasa Indonesia.                       |
| rincian | objek atau null | `null` bila tidak ada rincian.                                    |

\## 4. Tabel kode galat

\### 4.1 Galat sistem

| Status | Kode                     | Pesan                                          | Rincian                         |
|--------|--------------------------|------------------------------------------------|---------------------------------|
| 401    | `BELUM\_MASUK`            | Anda belum masuk atau sesi tidak berlaku.      | `null`                          |
| 403    | `DITOLAK`                | Anda tidak berwenang melakukan tindakan ini.   | `null`                          |
| 404    | `TIDAK\_DITEMUKAN`        | Sumber daya tidak ditemukan.                   | `null`                          |
| 405    | `METODE\_TIDAK\_DIIZINKAN` | Metode HTTP tidak diizinkan pada alamat ini.   | `null`                          |
| 422    | `VALIDASI`               | Data yang dikirim tidak memenuhi syarat.       | Galat per kolom (lihat 4.3)     |
| 429    | `TERLALU\_SERING`         | Terlalu banyak permintaan. Coba lagi sebentar. | `null`                          |
| lain   | `GALAT\_HTTP`             | Permintaan tidak dapat diproses.               | `null`                          |
| 500    | `GALAT\_SERVER`           | Terjadi kesalahan pada server.                 | Lihat 4.4                       |
| 503    | `TIDAK\_SEHAT`            | Basis data tidak terjangkau.                   | `null` (hanya dari `/health`)   |

\### 4.2 Galat aturan bisnis

Pelanggaran aturan bisnis dilempar sebagai turunan `GalatDomain`. `kode` memuat kode aturan
(`LP-1` sampai `LP-17`), `statusHttp` ditentukan pelempar (bawaan `422`), dan `rincian` bebas diisi
pelempar. Contoh:

```json
{
&#x20; "sukses": false,
&#x20; "galat": {
&#x20;   "kode": "LP-13",
&#x20;   "pesan": "Saldo tidak cukup.",
&#x20;   "rincian": { "kurang": 5000 }
&#x20; }
}
```

Pola yang terlihat pada contoh modul: `409` untuk permintaan yang bentrok dengan keadaan saat ini
(stok atau saldo kurang), `422` untuk isi permintaan yang tidak dapat diproses. Ini kebiasaan dari
contoh, bukan aturan baku. Pemilik domain menetapkan status tiap aturan pada piagam domainnya.

\### 4.3 Galat validasi

`rincian` berisi nama kolom sebagai kunci dan larik pesan sebagai nilai:

```json
{
&#x20; "sukses": false,
&#x20; "galat": {
&#x20;   "kode": "VALIDASI",
&#x20;   "pesan": "Data yang dikirim tidak memenuhi syarat.",
&#x20;   "rincian": { "surel": \["Surel sudah terdaftar."] }
&#x20; }
}
```

\### 4.4 Galat tak terduga

Rincian galat tak terduga hanya masuk log. Klien tidak pernah menerimanya, karena pesan aslinya dapat
memuat potongan kueri SQL, jalur berkas, atau nilai konfigurasi.

\- `app.debug` mati (produksi): `rincian` bernilai `null`.
\- `app.debug` menyala (pengembangan): `rincian` berisi `{ "pesan\_asli": "..." }`.

\## 5. Aturan uang

\- \*\*Bilangan bulat rupiah\*\* (LP-1). Tipe pecahan (`float`, `double`, `decimal`) dilarang di kolom,
&#x20; kode, dan JSON. CI menolak migration yang memakainya.
\- Nilai uang pada respons berbentuk objek dengan dua kolom:

```json
{ "rupiah": 382000, "tampilan": "Rp 382.000" }
```

\- `rupiah` adalah angka untuk dihitung. `tampilan` adalah teks untuk ditampilkan, berpemisah ribuan titik.
\- Harga, ongkir, dan total dihitung server. Angka yang dikirim klien diabaikan (LP-6).
\- Komisi marketplace 5% dari subtotal produk, dibulatkan setengah ke atas (LP-8). Nilainya dibaca dari
&#x20; `config('lapak.komisi\_persen')`, tidak ditulis di kode.

\## 6. Aturan waktu

\- Stempel waktu pada respons memakai format \*\*ISO 8601\*\*, contoh `2026-11-01T20:59:00+07:00`.
\- \*\*TODO:\*\* zona waktu penyimpanan basis data dan zona waktu tampilan belum ditetapkan oleh sumber
&#x20; draf ini. Tim perlu memutuskan satu zona (usulan: simpan UTC, tampilkan dengan offset) dan
&#x20; menuliskannya di sini.

Batas waktu yang berlaku, dengan sumber nilainya:

| Aturan | Batas                                                              | Sumber                                   |
|--------|--------------------------------------------------------------------|------------------------------------------|
| LP-10  | Pesanan belum dibayar batal otomatis setelah 24 jam                | `config('lapak.batas\_bayar\_jam')`        |
| LP-12  | Dana dilepas otomatis 3 hari setelah pesanan dikirim               | `config('lapak.tahan\_dana\_hari')`        |
| LP-14  | Sesi berlaku paling lama 7 hari, gugur setelah 2 jam tidak dipakai | Dibangun pada Sesi 2                     |
| LP-15  | Sesi segar: kata sandi dikonfirmasi ulang dalam 10 menit terakhir  | Dibangun pada Sesi 2                     |

\## 7. Endpoint health

`GET /api/v1/health` tanpa autentikasi.

Sehat, status `200`:

```json
{
&#x20; "sukses": true,
&#x20; "data": {
&#x20;   "status": "sehat",
&#x20;   "versi": "0.1.0",
&#x20;   "waktu": "2026-11-01T20:59:00+07:00"
&#x20; },
&#x20; "meta": null
}
```

Basis data tidak terjangkau: status `503`, kode `TIDAK\_SEHAT` (lihat 4.1).

\## 8. Mengubah kontrak

1\. Ubah berkas ini dan kode terkait (`ResponsApi`, `GalatDomain`, `PenanganGalatApi`) dalam satu PR.
2\. Perbarui uji kontrak di `tests/Feature/Sistem/KontrakGalatTest.php`.
3\. Sebut perubahan pada bagian "Berkas bersama yang disentuh" di deskripsi PR.
4\. Pemilik D5 meninjau. CODEOWNERS memintanya otomatis.
