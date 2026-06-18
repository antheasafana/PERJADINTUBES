<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_details';

    protected $primaryKey = 'id_jurnal_detail';

    protected $fillable = [
        'id_jurnal',
        'id_akun',
        'debit',
        'kredit',
    ];

    public function jurnal()
    {
        return $this->belongsTo(
            Jurnal::class,
            'id_jurnal',
            'id_jurnal'
        );
    }

    public function akun()
    {
        return $this->belongsTo(
            Akun::class,
            'id_akun',
            'id'
        );
    }
}