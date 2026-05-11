<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

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

    protected $casts = [
        'dokumen' => 'array',
        'tgl_berangkat' => 'date',
        'tgl_kembali' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai');
    }

    public function parent()
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan_parent');
    }

    public function children()
    {
        return $this->hasMany(Pengajuan::class, 'id_pengajuan_parent');
    }

    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class, 'id_pengajuan');
    }
}