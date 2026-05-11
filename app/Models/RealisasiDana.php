<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiDana extends Model
{
    use HasFactory;

    protected $table = 'realisasi_dana';
    protected $primaryKey = 'id_realisasi';

    protected $fillable = [
        'id_pengajuan',
        'id_jenis_transaksi',
        'tgl_realisasi',
        'total_realisasi',
    ];

    public function pengajuan()
    {
    return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }
}