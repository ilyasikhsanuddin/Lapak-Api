<?php

use App\Domain\Uang;
use App\Enums\JenisDompet;
use App\Enums\JenisMutasi;
use App\Models\MutasiSaldo;
use App\Services\BukuBesar;

it('menolak pengubahan dan penghapusan mutasi saldo melalui model Eloquent (LP-13)', function () {
    $buku = app(BukuBesar::class);
    $eksternal = $buku->dompetSistem(JenisDompet::Eksternal);
    $escrow = $buku->dompetSistem(JenisDompet::Escrow);

    $buku->pindahkan(
        $eksternal,
        $escrow,
        Uang::dari(25_000),
        JenisMutasi::DanaMasuk,
        'LPK-UJI-PENJAGA-01'
    );

    $mutasi = MutasiSaldo::where('rujukan', 'LPK-UJI-PENJAGA-01')->first();
    expect($mutasi)->not->toBeNull();

    // Model Eloquent menolak update lewat event booted()
    expect(fn () => $mutasi->forceFill(['jumlah_rupiah' => 99_999])->save())
        ->toThrow(LogicException::class, 'LP-13: mutasi saldo tidak boleh diubah.');

    // Model Eloquent menolak delete lewat event booted()
    expect(fn () => $mutasi->delete())
        ->toThrow(LogicException::class, 'LP-13: mutasi saldo tidak boleh dihapus.');
});

it('membuktikan bahwa kueri massal query builder dapat meloloskan update dan delete (batas penjaga model)', function () {
    $buku = app(BukuBesar::class);
    $eksternal = $buku->dompetSistem(JenisDompet::Eksternal);
    $escrow = $buku->dompetSistem(JenisDompet::Escrow);

    $buku->pindahkan(
        $eksternal,
        $escrow,
        Uang::dari(50_000),
        JenisMutasi::DanaMasuk,
        'LPK-UJI-PENJAGA-02'
    );

    $mutasi = MutasiSaldo::where('rujukan', 'LPK-UJI-PENJAGA-02')->first();
    expect($mutasi)->not->toBeNull();

    // PENTING: Kueri massal (Query Builder) langsung berinteraksi dengan database tanpa
    // memicu model lifecycle events (updating/deleting) pada booted(), sehingga lolos!
    $jumlahBarisDiubah = MutasiSaldo::query()
        ->where('id', $mutasi->id)
        ->update(['jumlah_rupiah' => 123_456]);

    expect($jumlahBarisDiubah)->toBe(1)
        ->and($mutasi->refresh()->jumlah_rupiah)->toBe(123_456);

    // Begitu pula dengan kueri delete langsung
    $jumlahBarisDihapus = MutasiSaldo::query()
        ->where('id', $mutasi->id)
        ->delete();

    expect($jumlahBarisDihapus)->toBe(1)
        ->and(MutasiSaldo::find($mutasi->id))->toBeNull();
});
