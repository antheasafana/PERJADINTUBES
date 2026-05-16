<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanEmail extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_emails';

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE REALISASI DANA
    |--------------------------------------------------------------------------
    */

    public function realisasiDana()
    {
        return $this->belongsTo(
            RealisasiDana::class,
            'id_realisasi'
        );
    }
}