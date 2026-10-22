<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ButirKeranjang extends Model
{
    protected $table = 'butir_keranjang';

    protected $fillable = [
        'keranjang_id',
        'produk_id',
        'kuantitas',
    ];

    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class, 'keranjang_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
