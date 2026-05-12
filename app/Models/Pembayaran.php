<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'pembayaran';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id_pembayaran';

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

        'id_transaksi_pengeluaran',

        'jenis_pembayaran',

        'arah_transaksi',

        'nominal',

        'status',

        'tanggal_pembayaran',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'tanggal_pembayaran' =>
            'datetime',
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
        return $this->belongsTo(
            TransaksiPengeluaran::class,
            'id_transaksi_pengeluaran',
            'id_transaksi_pengeluaran'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR FORMAT NOMINAL
    |--------------------------------------------------------------------------
    */

    public function getFormatNominalAttribute()
    {
        return 'Rp '
            . number_format(
                $this->nominal,
                0,
                ',',
                '.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR STATUS LABEL
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute()
    {
        return $this->status == 'dibayar'
            ? 'Dibayar'
            : 'Belum Dibayar';
    }
}