<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBiaya extends Model
{
    use HasFactory;

    protected $table = 'detail_biaya';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pengajuan',
        'nama_biaya',
        'nominal'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(
            Pengajuan::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }
}