<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $table = 'verifikasi';

    protected $fillable = [
        'id_pengajuan',
        'admin_id',
        'level',
        'status',
        'catatan',
        'alasan_reject',
        'tanggal_verifikasi'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION PENGAJUAN
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
    | RELATION ADMIN
    |--------------------------------------------------------------------------
    */

    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'admin_id'
        );
    }
}