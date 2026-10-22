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
    public function test_kuantitas_harus_minimal_satu(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Kuantitas minimal 1.');

        new BarisBelanja(
            produkId: 1,
            namaProduk: 'Kain Batik Cap',
            tokoId: 1,
            hargaSatuan: Uang::dari(185_000),
            kuantitas: 0,
        );
    }

    public function test_belanja_kosong_tidak_dapat_dihitung(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Belanja kosong tidak dapat dihitung.'
        );

        (new PenghitungBelanja(5))->hitung([], []);
    }
}