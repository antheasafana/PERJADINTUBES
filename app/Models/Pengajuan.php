<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'id_pegawai',
        'jenis_pengajuan',
        'id_pengajuan_parent',
        'tujuan',
        'tgl_berangkat',
        'tgl_kembali',
        'estimasi_biaya',
        'dokumen',
        'status'
    ];
}