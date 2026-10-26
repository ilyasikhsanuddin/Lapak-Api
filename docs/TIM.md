# Tim Lapak

Dokumen ini mengikat cara kerja tim selama lima sesi. Ganti nama dan akun GitHub
dengan anggota tim Anda **sebelum** PR pembuka di-merge; setelah itu tidak berubah.

## Domain tetap, integrator bergilir

Domain tidak berpindah: setiap anggota bertanggung jawab atas satu bagian sistem
dari skema sampai pengujian. Yang berpindah adalah peran integrator.

| Domain | Pemilik | Tabel |
|---|---|---|
| D1 Identitas dan Sesi | Bambang | `pengguna`, `sesi` |
| D2 Toko dan Katalog | Bagas | `toko`, `kategori`, `produk` |
| D3 Keranjang dan Pesanan | Rani | `keranjang`, `butir_keranjang`, `pesanan`, `butir_pesanan` |
| D4 Pembayaran dan Saldo | Dimas | `dompet`, `mutasi_saldo`, `pembayaran`, `pencairan` |
| D5 Otorisasi, Audit, dan Kualitas | Sekar | `peran`, `izin`, `kunci_idempotensi`, `log_audit` |

Foreign key hanya boleh menunjuk ke domain bernomor **sama atau lebih kecil**.
Urutan itu sekaligus urutan merge yang aman.

| Sesi | Fokus | Integrator | Tag penutup |
|---|---|---|---|
| 1 | Fondasi tim dan repositori | Bambang | `sesi-1` |
| 2 | Identitas dan session dengan kunci | Bagas | `sesi-2` |
| 3 | RBAC | Rani | `sesi-3` |
| 4 | Keamanan transaksi | Dimas | `sesi-4` |
| 5 | Escrow, saldo, audit, dan rilis | Sekar | `v1.0.0` |

### Tugas integrator pada sesinya

1. Membuka sesi: menulis papan sesi di `docs/sesi/` dan memperbarui `.github/CODEOWNERS`.
2. Menjaga urutan merge dan menjadi **satu-satunya** orang yang menekan tombol merge.
3. Menjalankan `main` dari basis data kosong setelah setiap jendela unggah.
4. Menutup sesi: mengisi papan sesi, memasang tag, menjalankan rekap kontribusi,
   dan menyerahkan peran.

Integrator tetap mengerjakan domainnya sendiri. Ia tidak mengerjakan domain orang
lain dan tidak menyetujui PR miliknya sendiri.

## Lingkar tinjauan

Setiap PR ditinjau satu rekan. Siklus bergeser tiap sesi.

| Penulis | Sesi 1 | Sesi 2 | Sesi 3 | Sesi 4 | Sesi 5 |
|---|---|---|---|---|---|
| Bambang | Bagas | Rani | Dimas | Sekar | dua reviewer |
| Bagas | Rani | Dimas | Sekar | Bambang | dua reviewer |
| Rani | Dimas | Sekar | Bambang | Bagas | dua reviewer |
| Dimas | Sekar | Bambang | Bagas | Rani | dua reviewer |
| Sekar | Bambang | Bagas | Rani | Dimas | dua reviewer |

Reviewer menarik cabang, menjalankan migrasi dan pengujian, lalu mencoba cara
menguji pada deskripsi PR. **Persetujuan tanpa menjalankan bukan review.**

## Identitas Git

Kontribusi dibaca dari riwayat Git, jadi setiap anggota meng-commit atas namanya
sendiri:

    git config --global user.name "Nama Lengkap Anda"
    git config --global user.email "email-github@student.uns.ac.id"
    git config --global init.defaultBranch main
    git config --global pull.rebase true