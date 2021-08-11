<?php

namespace App\Models;

use App\Models\Produk;
use App\Models\FotoProduk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'id_toko',
        'nama',
        'harga',
        'stok',
        'nomor_rak'
    ];

    public function fotoProduk(){
        return $this->hasMany(FotoProduk::class, 'id_produk', 'id');
    }

    public function toko(){
        return $this->belongsTo(Toko::class, 'id_toko', 'id');
    }
}
