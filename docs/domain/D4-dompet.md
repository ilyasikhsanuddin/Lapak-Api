# Domain D4: Pembayaran dan Saldo

**Pemilik:** Dimas (Fauzy Akhbar)

**Tabel yang dikelola:** `dompet`, `mutasi_saldo`, `pembayaran`, `pencairan`

**Tugas Utama:**
- Mencatat perpindahan uang sebagai pasangan mutasi berjumlah nol (LP-13).
- Memastikan uang tidak pernah ditambahkan, melainkan selalu dipindahkan dari satu dompet ke dompet lain.
- Hanya dompet eksternal yang boleh memiliki saldo negatif.