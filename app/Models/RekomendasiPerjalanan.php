<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomendasiPerjalanan extends Model {
    use HasFactory;

    // Menentukan nama tabel secara eksplisit dalam Bahasa Indonesia
    protected $table = 'rekomendasi_perjalanan';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'analisis_rekomendasi',
        'tujuan_terpopuler',
        'saran_efisiensi'
    ];

    /**
     * Relasi ke model User (Pegawai).
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}