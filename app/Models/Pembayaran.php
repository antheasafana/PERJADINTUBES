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

        'no_rekening_pegawai',

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

    public static function createPendingForPengajuan(
        Pengajuan $pengajuan,
        ?TransaksiPengeluaran $transaksi = null
    ): ?self {
        if ($transaksi) {
            if (static::where('id_transaksi_pengeluaran', $transaksi->id_transaksi_pengeluaran)->exists()) {
                return null;
            }

            $jenisPembayaran = match ($pengajuan->jenis_pengajuan) {
                'UANG_MUKA' => 'uang_muka',
                'PENGEMBALIAN' => 'pengembalian_dana',
                default => 'reimbursement',
            };

            $arahTransaksi = $pengajuan->jenis_pengajuan === 'PENGEMBALIAN'
                ? 'pegawai_ke_admin'
                : 'admin_ke_pegawai';

            $nominal = $transaksi->nominal;
            $idTransaksi = $transaksi->id_transaksi_pengeluaran;
        } else {
            if ($pengajuan->jenis_pengajuan !== 'UANG_MUKA') {
                return null;
            }

            if (static::where('id_pengajuan', $pengajuan->id_pengajuan)
                ->where('jenis_pembayaran', 'uang_muka')
                ->exists()) {
                return null;
            }

            $jenisPembayaran = 'uang_muka';
            $arahTransaksi = 'admin_ke_pegawai';
            $nominal = $pengajuan->estimasi_biaya;
            $idTransaksi = null;
        }

        return static::create([
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'id_transaksi_pengeluaran' => $idTransaksi,
            'jenis_pembayaran' => $jenisPembayaran,
            'arah_transaksi' => $arahTransaksi,
            'nominal' => $nominal,
            'no_rekening_pegawai' => null,
            'status' => 'pending',
            'tanggal_pembayaran' => null,
        ]);
    }
}