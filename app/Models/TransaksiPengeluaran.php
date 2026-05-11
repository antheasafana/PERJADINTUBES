<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pengeluaran';
    protected $primaryKey = 'id_transaksi_pengeluaran';

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

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_pembayaran' => 'datetime',
        'tanggal_tercatat' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(
            Pengajuan::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    public function kategoriBiaya()
    {
        return $this->belongsTo(
            KategoriBiaya::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function akun()
    {
        return $this->belongsTo(
            akun::class,
            'id_akun',
            'id'
        );
    }
}