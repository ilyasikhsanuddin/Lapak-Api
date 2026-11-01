<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;
use JsonSerializable;

<<<<<<< HEAD
=======
/**
 * LP-1: uang adalah bilangan bulat rupiah. Tidak ada float di lapisan mana pun.
 */
>>>>>>> origin/main
final readonly class Uang implements JsonSerializable
{
    private function __construct(public int $rupiah)
    {
<<<<<<< HEAD
=======
        if ($rupiah < 0) {
            throw new InvalidArgumentException('Uang tidak boleh negatif.');
        }
>>>>>>> origin/main
    }

    public static function dari(int $rupiah): self
    {
<<<<<<< HEAD
        if ($rupiah < 0) {
            throw new InvalidArgumentException(
                'Nilai uang tidak boleh negatif.'
            );
        }

=======
>>>>>>> origin/main
        return new self($rupiah);
    }

    public static function nol(): self
    {
        return new self(0);
    }

<<<<<<< HEAD
    public function tambah(self $lain): self
=======
    public function tambah(Uang $lain): self
>>>>>>> origin/main
    {
        return new self($this->rupiah + $lain->rupiah);
    }

<<<<<<< HEAD
    public function kurang(self $lain): self
    {
        if ($lain->rupiah > $this->rupiah) {
            throw new InvalidArgumentException(
                'Pengurangan menghasilkan nilai negatif.'
            );
=======
    public function kurang(Uang $lain): self
    {
        if ($this->rupiah < $lain->rupiah) {
            throw new InvalidArgumentException('Hasil pengurangan tidak boleh negatif.');
>>>>>>> origin/main
        }

        return new self($this->rupiah - $lain->rupiah);
    }

    public function kali(int $pengali): self
    {
<<<<<<< HEAD
        if ($pengali < 0) {
            throw new InvalidArgumentException(
                'Pengali tidak boleh negatif.'
            );
        }

=======
>>>>>>> origin/main
        return new self($this->rupiah * $pengali);
    }

    public function persen(int $persen): self
    {
<<<<<<< HEAD
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
=======
        $hasil = intdiv($this->rupiah * $persen + 50, 100);

        return new self($hasil);
>>>>>>> origin/main
    }

    public function format(): string
    {
        return 'Rp '.number_format($this->rupiah, 0, ',', '.');
    }

    public function jsonSerialize(): int
    {
        return $this->rupiah;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
