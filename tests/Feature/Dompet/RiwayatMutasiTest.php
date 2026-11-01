<?php

use App\Domain\Uang;
use App\Enums\JenisDompet;
use App\Enums\JenisMutasi;
use App\Models\Pengguna;
use App\Services\BukuBesar;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->buku = app(BukuBesar::class);
    $this->eksternal = $this->buku->dompetSistem(JenisDompet::Eksternal);
    $this->escrow = $this->buku->dompetSistem(JenisDompet::Escrow);
    $this->pendapatan = $this->buku->dompetSistem(JenisDompet::Pendapatan);
});

it('menampilkan peringatan jika dompet belum memiliki mutasi', function () {
    $this->artisan('lapak:mutasi', ['kode' => 'escrow'])
        ->expectsOutputToContain("Belum ada mutasi untuk dompet 'escrow'.")
        ->assertSuccessful();
});

it('menolak kode dompet yang tidak ada', function () {
    $this->artisan('lapak:mutasi', ['kode' => 'dompet_palsu'])
        ->expectsOutputToContain("Dompet dengan kode 'dompet_palsu' tidak ditemukan.")
        ->assertFailed();
});

it('menampilkan riwayat mutasi dan membuktikan saldo berjalan sama dengan saldo tersimpan', function () {
    $penjual = $this->buku->dompetPenjual(Pengguna::factory()->create());

    // 1. Pembeli membayar 200.000 ke escrow
    $this->buku->pindahkan(
        $this->eksternal,
        $this->escrow,
        Uang::dari(200_000),
        JenisMutasi::DanaMasuk,
        'LPK-UJI-MUT-01'
    );

    // 2. Pembeli membayar 150.000 ke escrow
    $this->buku->pindahkan(
        $this->eksternal,
        $this->escrow,
        Uang::dari(150_000),
        JenisMutasi::DanaMasuk,
        'LPK-UJI-MUT-02'
    );

    // 3. Komisi 10.000 dipotong dari escrow
    $this->buku->pindahkan(
        $this->escrow,
        $this->pendapatan,
        Uang::dari(10_000),
        JenisMutasi::Komisi,
        'LPK-UJI-MUT-01'
    );

    // 4. Dana 190.000 dilepas ke penjual
    $this->buku->pindahkan(
        $this->escrow,
        $penjual,
        Uang::dari(190_000),
        JenisMutasi::LepasDana,
        'LPK-UJI-MUT-01'
    );

    // Saldo akhir escrow seharusnya: 200.000 + 150.000 - 10.000 - 190.000 = 150.000
    expect($this->escrow->refresh()->saldo_rupiah)->toBe(150_000);

    $this->artisan('lapak:mutasi', ['kode' => 'escrow'])
        ->expectsOutputToContain('Saldo berjalan baris terakhir (Rp 150.000) terbukti sama dengan saldo tersimpan (Rp 150.000).')
        ->assertSuccessful();
});

it('mendeteksi jika saldo tersimpan di dompet tidak cocok dengan saldo berjalan', function () {
    // Tambah mutasi
    $this->buku->pindahkan(
        $this->eksternal,
        $this->escrow,
        Uang::dari(50_000),
        JenisMutasi::DanaMasuk,
        'LPK-UJI-MUT-FAIL'
    );

    // Manipulasi langsung di database
    DB::table('dompet')->where('kode', 'escrow')->update(['saldo_rupiah' => 999_999]);

    $this->artisan('lapak:mutasi', ['kode' => 'escrow'])
        ->expectsOutputToContain('Invarian terlanggar! Saldo berjalan terakhir (50000) tidak sama dengan saldo tersimpan (999999).')
        ->assertFailed();
});
