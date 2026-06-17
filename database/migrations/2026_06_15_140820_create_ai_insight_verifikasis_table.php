<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_insight_verifikasi', function (Blueprint $table) {
            $table->id();

            $table->longText('insight');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_insight_verifikasi');
    }
};