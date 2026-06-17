<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiInsightVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'ai_insight_verifikasi';

    protected $fillable = [
        'insight',
    ];
}