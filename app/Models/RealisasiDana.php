<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiDana extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'realisasi_dana';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id_realisasi';

    /*
    |--------------------------------------------------------------------------
    | TIMESTAMP
    |--------------------------------------------------------------------------
    */

    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'id_pengajuan',

        'id_jenis_transaksi',

        'tgl_realisasi',

        'total_realisasi',

        'status',

        'catatan'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function pengajuan()
    {
        return $this->belongsTo(
            Pengajuan::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI TRANSAKSI PENGELUARAN
    |--------------------------------------------------------------------------
    */

    public function transaksiPengeluaran()
    {
        return $this->hasManyThrough(

            TransaksiPengeluaran::class,

            Pengajuan::class,

            'id_pengajuan',

            'id_pengajuan',

            'id_pengajuan',

            'id_pengajuan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL PENGELUARAN
    |--------------------------------------------------------------------------
    */

    public function getTotalPengeluaranAttribute()
    {
        return $this->transaksiPengeluaran
            ->sum('nominal');
    }

    /*
    |--------------------------------------------------------------------------
    | SISA REALISASI
    |--------------------------------------------------------------------------
    */

    public function getSisaRealisasiAttribute()
    {
        return
            $this->total_realisasi -
            $this->total_pengeluaran;
    }
}