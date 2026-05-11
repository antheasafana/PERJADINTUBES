<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'pengajuan';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id_pengajuan';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | RELATION PEGAWAI
    |--------------------------------------------------------------------------
    */

    public function pegawai()
    {
        return $this->belongsTo(
            Pegawai::class,
            'id_pegawai',
            'id_pegawai'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION JENIS TRANSAKSI
    |--------------------------------------------------------------------------
    */

    public function jenisTransaksi()
    {
        return $this->belongsTo(
            JenisTransaksi::class,
            'jenis_pengajuan',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION REALISASI DANA
    |--------------------------------------------------------------------------
    */

    public function realisasiDana()
    {
        return $this->hasOne(
            RealisasiDana::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION TRANSAKSI PENGELUARAN
    |--------------------------------------------------------------------------
    */

    public function transaksiPengeluaran()
    {
        return $this->hasMany(
            TransaksiPengeluaran::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION VERIFIKASI
    |--------------------------------------------------------------------------
    */

    public function verifikasi()
    {
        return $this->hasMany(
            Verifikasi::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION DETAIL BIAYA
    |--------------------------------------------------------------------------
    */

    public function detailBiaya()
    {
        return $this->hasMany(
            DetailBiaya::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PARENT PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo(
            Pengajuan::class,
            'id_pengajuan_parent',
            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CHILD PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function children()
    {
        return $this->hasMany(
            Pengajuan::class,
            'id_pengajuan_parent',
            'id_pengajuan'
        );
    }
}