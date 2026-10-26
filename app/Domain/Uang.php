<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;
use JsonSerializable;

/**
 * LP-1: uang adalah bilangan bulat rupiah. Tidak ada float di lapisan mana pun.
 */
final readonly class Uang implements JsonSerializable
{
    private function __construct(public int $rupiah)
    {
        if ($rupiah < 0) {
            throw new InvalidArgumentException('Uang tidak boleh negatif.');
        }
    }

    public static function dari(int $rupiah): self
    {
        return new self($rupiah);
    }

    public static function nol(): self
    {
        return new self(0);
    }

    public function tambah(Uang $lain): self
    {
        return new self($this->rupiah + $lain->rupiah);
    }

    public function kurang(Uang $lain): self
    {
        if ($this->rupiah < $lain->rupiah) {
            throw new InvalidArgumentException('Hasil pengurangan tidak boleh negatif.');
        }

        return new self($this->rupiah - $lain->rupiah);
    }

    public function kali(int $pengali): self
    {
        return new self($this->rupiah * $pengali);
    }

    public function persen(int $persen): self
    {
        $hasil = intdiv($this->rupiah * $persen + 50, 100);

        return new self($hasil);
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
