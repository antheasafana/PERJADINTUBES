<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPengeluaran extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'transaksi_pengeluaran';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id_transaksi_pengeluaran';

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

        'id_kategori',

        'id_akun',

        'jenis_pengeluaran',

        'tanggal_pengeluaran',

        'uraian',

        'nominal',

        'bukti',

        'status',

        'catatan_verifikasi',

        'tanggal_verifikasi',

        'tanggal_pembayaran',

        'tanggal_tercatat',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'tanggal_pengeluaran' =>
            'date',

        'tanggal_verifikasi' =>
            'datetime',

        'tanggal_pembayaran' =>
            'datetime',

        'tanggal_tercatat' =>
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
    | RELASI KATEGORI BIAYA
    |--------------------------------------------------------------------------
    */

    public function kategoriBiaya()
    {
        return $this->belongsTo(
            KategoriBiaya::class,
            'id_kategori',
            'id_kategori'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI AKUN
    |--------------------------------------------------------------------------
    */

    public function akun()
    {
        return $this->belongsTo(
            akun::class,
            'id_akun',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    public function pembayaran()
    {
        return $this->hasMany(
            Pembayaran::class,
            'id_transaksi_pengeluaran',
            'id_transaksi_pengeluaran'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR STATUS LABEL
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {

            'verifikasi_pengeluaran' =>
                'Verifikasi Pengeluaran',

            'pembayaran' =>
                'Pembayaran',

            'transaksi_tercatat' =>
                'Transaksi Tercatat',

            'ditolak' =>
                'Ditolak',

            default =>
                ucfirst($this->status),
        };
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
    | CEK SUDAH DIBAYAR
    |--------------------------------------------------------------------------
    */

    public function getSudahDibayarAttribute()
    {
        return $this->pembayaran()->exists();
    }
}