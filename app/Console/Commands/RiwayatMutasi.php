<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Dompet;
use Illuminate\Console\Command;

final class RiwayatMutasi extends Command
{
    protected $signature = 'lapak:mutasi {kode : Kode dompet yang ingin diperiksa}';

    protected $description = 'Menampilkan riwayat mutasi sebuah dompet beserta saldo berjalan (LP-13)';

    public function handle(): int
    {
        $kode = (string) $this->argument('kode');
        $dompet = Dompet::query()->where('kode', $kode)->first();

        if ($dompet === null) {
            $this->error("Dompet dengan kode '{$kode}' tidak ditemukan.");

            return self::FAILURE;
        }

        $daftarMutasi = $dompet->mutasi()->orderBy('id')->get();

        if ($daftarMutasi->isEmpty()) {
            $this->warn("Belum ada mutasi untuk dompet '{$kode}'.");
            $this->info('Saldo tercatat: Rp '.number_format($dompet->saldo_rupiah, 0, ',', '.'));

            return self::SUCCESS;
        }

        $baris = [];
        $saldoBerjalan = 0;

        foreach ($daftarMutasi as $mutasi) {
            $saldoBerjalan += $mutasi->jumlah_rupiah;
            $baris[] = [
                $mutasi->id,
                $mutasi->created_at?->format('Y-m-d H:i:s') ?? '-',
                $mutasi->jenis->value,
                number_format($mutasi->jumlah_rupiah, 0, ',', '.'),
                number_format($saldoBerjalan, 0, ',', '.'),
                $mutasi->rujukan,
                $mutasi->keterangan ?? '-',
            ];
        }

        $this->table(
            ['ID', 'Waktu', 'Jenis', 'Jumlah (Rp)', 'Saldo Berjalan (Rp)', 'Rujukan', 'Keterangan'],
            $baris
        );

        if ($saldoBerjalan !== $dompet->saldo_rupiah) {
            $this->error(
                "Invarian terlanggar! Saldo berjalan terakhir ({$saldoBerjalan}) tidak sama dengan saldo tersimpan ({$dompet->saldo_rupiah})."
            );

            return self::FAILURE;
        }

        $this->info(
            'Saldo berjalan baris terakhir (Rp '.number_format($saldoBerjalan, 0, ',', '.').') terbukti sama dengan saldo tersimpan (Rp '.number_format($dompet->saldo_rupiah, 0, ',', '.').').'
        );

        return self::SUCCESS;
    }
}
