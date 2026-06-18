<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransaksiPengeluaran;

class Verifikasi extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'verifikasis';

    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

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

        'admin_id',

        'level',

        'status',

        'catatan',

        'alasan_reject',

        'tanggal_verifikasi',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'tanggal_verifikasi' =>
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
    | RELASI ADMIN
    |--------------------------------------------------------------------------
    */

    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'admin_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR STATUS LABEL
    |--------------------------------------------------------------------------
    */

    public function getVerificationTypeAttribute()
    {
        return $this->id_transaksi_pengeluaran
            ? 'Verifikasi Pengeluaran'
            : 'Verifikasi Pengajuan';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {

            'approve' =>
                'Approved',

            'reject' =>
                'Rejected',

            'pending' =>
                'Pending',

            default =>
                ucfirst($this->status),
        };
    }
}