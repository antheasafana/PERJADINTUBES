<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnals';

    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'tanggal',
        'deskripsi',
    ];

    public function details()
    {
        return $this->hasMany(
            JurnalDetail::class,
            'id_jurnal',
            'id_jurnal'
        );
    }
}