<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'NIP',
        'posisi',
        'tanggal_lahir',
        'upload_KTP',
        'is_admin',
        'email',
    ];
}