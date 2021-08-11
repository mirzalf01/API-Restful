<?php

namespace App\Models;

use App\Models\Toko;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FotoToko extends Model
{
    use HasFactory;

    protected $table = 'foto_toko';

    protected $fillable = [
        'id_toko',
        'url'
    ];

    public function toko(){
        return $this->belongsTo(Toko::class, 'id_toko', 'id');
    }
}
