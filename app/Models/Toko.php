<?php

namespace App\Models;

use App\Models\User;
use App\Models\FotoToko;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Toko extends Model
{
    use HasFactory;

    protected $table = 'toko';

    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'provinsi',
        'id_pemilik'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'id_pemilik', 'id');
    }

    public function fotoToko(){
        return $this->hasMany(FotoToko::class, 'id_toko', 'id');
    }

    public function produk(){
        return $this->hasMany(Produk::class, 'id_toko', 'id');
    }
}
