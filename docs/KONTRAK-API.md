# Kontrak API Lapak

Dokumen ini mengunci bentuk respons seluruh endpoint. Mengubah bentuk di sini berarti
mengubah kontrak; ubah lewat PR milik D5 dan beri tahu seluruh tim.

## 1. Amplop sukses

```json
{
  "sukses": true,
  "data": { },
  "meta": null
}
```

`meta` bernilai `null` bila tidak ada keterangan tambahan.

## 2. Amplop sukses berhalaman

```json
{
  "sukses": true,
  "data": [ ],
  "meta": {
    "halaman": 1,
    "per_halaman": 15,
    "total": 42,
    "halaman_terakhir": 3
  }
}
```

## 3. Amplop galat

```json
{
  "sukses": false,
  "galat": {
    "kode": "VALIDASI",
    "pesan": "Data yang dikirim tidak memenuhi syarat.",
    "rincian": { }
  }
}
```

`rincian` bernilai `null` bila kosong. Untuk galat aturan bisnis, `kode` memuat nomor aturan
(misalnya `LP-13`).

## 4. Tabel kode galat

| Status | Kode | Arti |
|---|---|---|
| 401 | `BELUM_MASUK` | Belum masuk atau sesi tidak berlaku |
| 403 | `DITOLAK` | Tidak berwenang melakukan tindakan |
| 404 | `TIDAK_DITEMUKAN` | Sumber daya tidak ditemukan |
| 405 | `METODE_TIDAK_DIIZINKAN` | Metode HTTP tidak diizinkan |
| 422 | `VALIDASI` | Data tidak memenuhi syarat; `rincian` berisi pesan per kolom |
| 429 | `TERLALU_SERING` | Terlalu banyak permintaan |
| 500 | `GALAT_SERVER` | Galat tak terduga; rincian asli hanya di log |
| 503 | `TIDAK_SEHAT` | Basis data tidak terjangkau (health) |
| sesuai `GalatDomain` | `LP-n` | Pelanggaran aturan bisnis nomor n, misalnya `LP-13` (saldo tidak cukup, 409) |
| lain | `GALAT_HTTP` | Galat HTTP lain yang tidak dipetakan |

## 5. Aturan uang

- Seluruh nilai uang adalah **bilangan bulat rupiah** (LP-1). Tipe pecahan dilarang di kolom,
  kode, dan JSON.
- Nama kolom dan bidang uang berakhiran `_rupiah`.
- Perhitungan dilakukan di server; angka kiriman klien diabaikan.

## 6. Aturan waktu

- Waktu pada respons memakai format ISO 8601 (`toIso8601String()`).

## 7. Header

- `X-Request-Id`: dibuat atau diteruskan oleh middleware `IdPermintaan` pada setiap respons API,
  dipakai untuk menelusuri log (B1).

## 8. Endpoint sistem

| Metode | Alamat | Keterangan |
|---|---|---|
| GET | `/api/v1/health` | Mengembalikan `status`, `versi`, dan `waktu`; 503 `TIDAK_SEHAT` bila basis data tidak terjangkau |
