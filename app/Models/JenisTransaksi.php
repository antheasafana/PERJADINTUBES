<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTransaksi extends Model
{
    use HasFactory;

    public const UANG_MUKA = 'Uang muka';

    public const REIMBURSEMENT = 'Reimbursement';

    public const PENGEMBALIAN = 'Pengembalian';

    protected $table = 'jenis_transaksi';

    protected $guarded = [];
    
    public function pengajuan()
    {
    return $this->hasMany(
        Pengajuan::class,
        'id_jenis_transaksi'
    );
    }
}