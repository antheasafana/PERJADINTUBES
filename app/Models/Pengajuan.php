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
     public function pegawai()
    {
    return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function jenisTransaksi()
    {
    return $this->belongsTo(JenisTransaksi::class, 'id_jenis_transaksi');
    }

    public function realisasiDana()
    {
    return $this->hasOne(RealisasiDana::class, 'id_pengajuan');
    }
}