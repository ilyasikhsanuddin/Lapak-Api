<?php

namespace Tests\Feature;

use App\Enums\StatusProduk;
use App\Enums\StatusToko;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulasiBelanjaTest extends TestCase
{
    use RefreshDatabase;

    public function test_simulasi_belanja_dengan_produk_valid(): void
    {
        $produk = Produk::factory()->create([
            'status' => StatusProduk::Aktif,
            'stok' => 10,
        ]);

        $response = $this->postJson('/api/v1/simulasi-belanja', [
            'butir' => [
                [
                    'produk_id' => $produk->id,
                    'kuantitas' => 2,
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'toko',
                        'baris',
                        'subtotal',
                        'ongkir',
                        'total',
                    ],
                ],
            ]);
    }

    public function test_produk_yang_tidak_tersedia_menghasilkan_422(): void
    {
        $response = $this->postJson('/api/v1/simulasi-belanja', [
            'butir' => [
                [
                    'produk_id' => 999999,
                    'kuantitas' => 1,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('error.kode', 'LP-4');
    }

    public function test_stok_tidak_mencukupi_menghasilkan_409(): void
    {
        $produk = Produk::factory()->create([
            'status' => StatusProduk::Aktif,
            'stok' => 1,
        ]);

        $response = $this->postJson('/api/v1/simulasi-belanja', [
            'butir' => [
                [
                    'produk_id' => $produk->id,
                    'kuantitas' => 2,
                ],
            ],
        ]);

        $response
            ->assertStatus(409)
            ->assertJsonPath('error.kode', 'LP-4')
            ->assertJsonPath('error.rincian.diminta', 2)
            ->assertJsonPath('error.rincian.tersedia', 1);
    }

    public function test_daftar_belanja_kosong_menghasilkan_422(): void
    {
        $response = $this->postJson('/api/v1/simulasi-belanja', [
            'butir' => [],
        ]);

        $response->assertStatus(422);
    }
}
