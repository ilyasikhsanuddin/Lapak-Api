# Domain D4: Pembayaran dan Saldo

**Pemilik:** Dimas (Fauzy Akhbar)

**Tabel yang dikelola:** `dompet`, `mutasi_saldo`, `pembayaran` (Sesi 4), `pencairan` (Sesi 5)

---

## 1. Tugas Utama dan Aturan Bisnis (LP-13)

1. **Prinsip Double-Entry Bookkeeping (Buku Besar Berpasangan)**:
   - Uang tidak pernah "diciptakan" atau "ditambahkan" begitu saja ke dalam sebuah dompet.
   - Uang selalu **dipindahkan** dari satu dompet ke dompet lain via `BukuBesar::pindahkan()`.
   - Setiap pemindahan mencatat **tepat dua baris mutasi** dengan UUID `kelompok` yang sama:
     - Satu baris bernilai negatif (sumber/keluar).
     - Satu baris bernilai positif (tujuan/masuk).
     - Jumlah aljabar kedua baris mutasi selalu tepat sama dengan **nol**.
2. **Invarian Saldo**:
   - Saldo tersimpan pada tabel `dompet` harus selalu sama dengan jumlah seluruh mutasi pada tabel `mutasi_saldo`.
   - Hanya dompet `eksternal` yang boleh memiliki saldo negatif (mencerminkan dana yang masuk/ditampung dari luar sistem). Dompet `escrow`, `pendapatan`, dan `penjual` tidak boleh negatif.
3. **Kekekalan Catatan (Append-Only)**:
   - Baris pada tabel `mutasi_saldo` tidak boleh pernah diubah (`UPDATE`) dan tidak boleh dihapus (`DELETE`). Koreksi pencatatan hanya sah dilakukan melalui mutasi pembalik.

---

## 2. Piagam Domain D4 (Latihan Mandiri 8)

### Yang Dibutuhkan dari Domain Lain:
- **D1 (Identitas dan Sesi)**:
  - Model `App\Models\Pengguna` untuk mengikat dompet penjual (`dompetPenjual(Pengguna $penjual)`).
  - Verifikasi pengguna dan otorisasi saat pencairan dana (Sesi 5).
- **D2 (Toko dan Katalog)**:
  - Data toko dan ongkir valid untuk memastikan nilai pemindahan dana akurat.
- **D3 (Keranjang dan Pesanan)**:
  - Nomor pesanan terstandarisasi (`LP-9`, format `LPK-YYYYMMDD-NNNNNN`) sebagai parameter `rujukan` pada setiap pemindahan saldo.
  - Komposisi nilai belanja (subtotal, komisi LP-8, ongkir) dari `PenghitungBelanja` dan `RincianToko`.
- **D5 (Kualitas, Otorisasi, Audit)**:
  - `App\Exceptions\GalatDomain` sebagai induk pengecualian domain bisnis.
  - Integrasi gerbang CI pada `.github/workflows/ci.yml` untuk memanggil `php artisan lapak:periksa-buku`.

### Yang Disediakan untuk Domain Lain:
- **Service `App\Services\BukuBesar`**:
  - `dompetSistem(JenisDompet $jenis)`: Mengambil/membuat dompet sistem (`eksternal`, `escrow`, `pendapatan`).
  - `dompetPenjual(Pengguna $penjual)`: Mengambil/membuat dompet penjual dengan kode unik `penjual:<id>`.
  - `pindahkan(...)`: Melakukan transfer saldo berpasangan yang aman dengan penguncian pesimistik (`lockForUpdate`), pengecekan saldo, dan rollback otomatis bila terjadi kegagalan.
- **Exceptions**:
  - `App\Exceptions\SaldoTidakCukup`: Dilempar saat pemindahan dana melebihi saldo dompet yang tidak boleh negatif (HTTP 409).
- **Artisan Console Commands**:
  - `php artisan lapak:periksa-buku`: Memeriksa 4 invarian buku besar untuk verifikasi integritas data dan gerbang CI.
  - `php artisan lapak:mutasi {kode}`: Menampilkan riwayat mutasi dompet dan membuktikan kesesuaian saldo berjalan dengan saldo tersimpan.

---

## 3. Analisis dan Usulan Mitigasi Penjaga Basis Data (Latihan Mandiri 7)

### Batasan Penjaga Model Eloquent
Pada model `App\Models\MutasiSaldo`, penjaga append-only diimplementasikan menggunakan event lifecycle Eloquent:
```php
protected static function booted(): void
{
    self::updating(fn () => throw new LogicException('LP-13: mutasi saldo tidak boleh diubah.'));
    self::deleting(fn () => throw new LogicException('LP-13: mutasi saldo tidak boleh dihapus.'));
}
```
**Kelemahan**: Penjaga ini hanya aktif ketika mutasi diubah melalui instance model Eloquent (misalnya `$mutasi->save()` atau `$mutasi->delete()`).
Berdasarkan pembuktian pada pengujian `tests/Feature/Dompet/PenjagaBasisDataTest.php`:
1. Kueri massal Eloquent (`MutasiSaldo::query()->where(...)->update(...)` atau `delete(...)`) langsung dieksekusi menjadi SQL mentah ke basis data tanpa memicu model event `updating` atau `deleting`.
2. Perintah query mentah melalui `DB::table('mutasi_saldo')->update(...)` atau akses client database langsung (seperti phpMyAdmin/DBeaver) dapat melewati penjaga kode aplikasi sepenuhnya.

### Usulan Mitigasi di Tingkat Basis Data

Untuk mencapai strategi *defense-in-depth* (pertahanan berlapis), diusulkan mitigasi berikut:

#### 1. Penerapan Database Triggers (MySQL / PostgreSQL)
Membuat trigger tingkat basis data yang menolak setiap operasi `UPDATE` dan `DELETE` pada tabel `mutasi_saldo`:
```sql
-- Mencegah UPDATE pada mutasi_saldo
DELIMITER $$
CREATE TRIGGER cegah_update_mutasi_saldo
BEFORE UPDATE ON mutasi_saldo
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'LP-13: Tabel mutasi_saldo bersifat append-only, dilarang melakukan UPDATE.';
END$$
DELIMITER ;

-- Mencegah DELETE pada mutasi_saldo
DELIMITER $$
CREATE TRIGGER cegah_delete_mutasi_saldo
BEFORE DELETE ON mutasi_saldo
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'LP-13: Tabel mutasi_saldo bersifat append-only, dilarang melakukan DELETE.';
END$$
DELIMITER ;
```
*Kelebihan*: Berlaku universal untuk seluruh koneksi basis data, baik dari aplikasi Laravel, script konsol, maupun DBA/akses manual.

#### 2. Pembatasan Hak Akses Pengguna Basis Data (*Least Privilege*)
Pada lingkungan produksi (*staging* & *production*), akun basis data yang digunakan oleh aplikasi Laravel dikonfigurasi dengan izin terbatas:
- Tabel umum: `SELECT`, `INSERT`, `UPDATE`, `DELETE`.
- Khusus tabel `mutasi_saldo`: **Hanya diberi hak `SELECT` dan `INSERT`**.
```sql
REVOKE UPDATE, DELETE ON lapak.mutasi_saldo FROM 'lapak_app'@'%';
GRANT SELECT, INSERT ON lapak.mutasi_saldo TO 'lapak_app'@'%';
FLUSH PRIVILEGES;
```
*Kelebihan*: Menjamin secara fisik di level perizinan mesin basis data bahwa aplikasi tidak memiliki wewenang untuk mengeksekusi perintah manipulasi baris riwayat.

#### 3. Rekonsiliasi Terjadwal Otomatis
Perintah `php artisan lapak:periksa-buku` didaftarkan pada scheduled task (Cron) untuk dijalankan secara berkala (misal setiap jam) guna mendeteksi anomali integritas data sedini mungkin.