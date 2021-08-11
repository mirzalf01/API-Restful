<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FotoProduk extends Model
{
    use HasFactory;

    protected $table = 'foto_produk';

    protected $fillable = [
        'id_produk',
        'url'
    ];

    public function produk(){
        return $this->belongsTo(Produk::class, 'id_produk', 'id');
    }
}
