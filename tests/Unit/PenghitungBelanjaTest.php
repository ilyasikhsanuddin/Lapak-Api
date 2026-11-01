<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Belanja\BarisBelanja;
use App\Domain\Belanja\PenghitungBelanja;
use App\Domain\Uang;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PenghitungBelanjaTest extends TestCase
{
    private function tokoContoh(): array
    {
        return [
            1 => [
                'nama' => 'Batik Laweyan Bu Sri',
                'ongkir' => Uang::dari(12_000),
            ],
            2 => [
                'nama' => 'Oleh-Oleh Mbak Wulan',
                'ongkir' => Uang::dari(10_000),
            ],
        ];
    }

    public function test_belanja_dipisahkan_per_toko(): void
    {
        $rincian = (new PenghitungBelanja(5))->hitung([
            new BarisBelanja(
                11, 'Intip Goreng', 2, Uang::dari(32_500), 3
            ),
            new BarisBelanja(
                1, 'Kain Batik Cap', 1, Uang::dari(185_000), 2
            ),
            new BarisBelanja(
                12, 'Abon Sapi', 2, Uang::dari(78_000), 1
            ),
        ], $this->tokoContoh());

        $this->assertCount(2, $rincian);
        $this->assertSame('Batik Laweyan Bu Sri', $rincian[0]->namaToko);
        $this->assertSame(370_000, $rincian[0]->subtotal->rupiah);
        $this->assertSame(382_000, $rincian[0]->total()->rupiah);
        $this->assertSame(175_500, $rincian[1]->subtotal->rupiah);
        $this->assertSame(185_500, $rincian[1]->total()->rupiah);
        $this->assertSame(
            567_500,
            PenghitungBelanja::totalBayar($rincian)->rupiah
        );
    }

    public function test_komisi_dihitung_dari_subtotal_saja(): void
    {
        [$batik, $kuliner] = (new PenghitungBelanja(5))->hitung([
            new BarisBelanja(
                1, 'Kain Batik Cap', 1, Uang::dari(185_000), 2
            ),
            new BarisBelanja(
                11, 'Intip Goreng', 2, Uang::dari(32_500), 3
            ),
            new BarisBelanja(
                12, 'Abon Sapi', 2, Uang::dari(78_000), 1
            ),
        ], $this->tokoContoh());

        $this->assertSame(18_500, $batik->komisi->rupiah);
        $this->assertSame(363_500, $batik->bagianPenjual()->rupiah);
        $this->assertSame(8_775, $kuliner->komisi->rupiah);
        $this->assertSame(176_725, $kuliner->bagianPenjual()->rupiah);
    }

    public function test_belanja_kosong_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PenghitungBelanja(5))->hitung([], $this->tokoContoh());
    }

    public function test_kuantitas_nol_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BarisBelanja(
            1, 'Kain Batik Cap', 1, Uang::dari(185_000), 0
        );
    }
}