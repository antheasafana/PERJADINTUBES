<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table =
        'pembayarans';

    protected $primaryKey =
        'id_pembayaran';

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
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function pengajuan()
    {
        return $this->belongsTo(
            Pengajuan::class,
            'id_pengajuan'
        );
    }

    public function transaksiPengeluaran()
    {
        return $this->belongsTo(
            TransaksiPengeluaran::class,
            'id_transaksi_pengeluaran'
        );
    }
}