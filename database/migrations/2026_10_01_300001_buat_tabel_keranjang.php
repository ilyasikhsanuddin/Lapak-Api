<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')
                ->unique()
                ->constrained('pengguna')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('butir_keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keranjang_id')
                ->constrained('keranjang')
                ->cascadeOnDelete();
            $table->foreignId('produk_id')
                ->constrained('produk')
                ->restrictOnDelete();
            $table->unsignedInteger('kuantitas');
            $table->timestamps();

            $table->unique(['keranjang_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('butir_keranjang');
        Schema::dropIfExists('keranjang');
    }
};
