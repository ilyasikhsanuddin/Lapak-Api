<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;
use JsonSerializable;

final readonly class Uang implements JsonSerializable
{
    private function __construct(public int $rupiah)
    {
    }

    public static function dari(int $rupiah): self
    {
        if ($rupiah < 0) {
            throw new InvalidArgumentException(
                'Nilai uang tidak boleh negatif.'
            );
        }

        return new self($rupiah);
    }

    public static function nol(): self
    {
        return new self(0);
    }

    public function tambah(self $lain): self
    {
        return new self($this->rupiah + $lain->rupiah);
    }

    public function kurang(self $lain): self
    {
        if ($lain->rupiah > $this->rupiah) {
            throw new InvalidArgumentException(
                'Pengurangan menghasilkan nilai negatif.'
            );
        }

        return new self($this->rupiah - $lain->rupiah);
    }

    public function kali(int $pengali): self
    {
        if ($pengali < 0) {
            throw new InvalidArgumentException(
                'Pengali tidak boleh negatif.'
            );
        }

        return new self($this->rupiah * $pengali);
    }

    public function persen(int $persen): self
    {
        if ($persen < 0 || $persen > 100) {
            throw new InvalidArgumentException(
                'Persentase harus di antara 0 dan 100.'
            );
        }

        return new self(
            intdiv($this->rupiah * $persen + 50, 100)
        );
    }

    public function sama(self $lain): bool
    {
        return $this->rupiah === $lain->rupiah;
    }

    public function lebihDari(self $lain): bool
    {
        return $this->rupiah > $lain->rupiah;
    }

    public function format(): string
    {
        return 'Rp '.number_format($this->rupiah, 0, ',', '.');
    }

    public function jsonSerialize(): int
    {
        return $this->rupiah;
    }
}