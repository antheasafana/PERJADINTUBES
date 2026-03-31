<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama
            $table->string('NIP'); // NIP Pegawai
            $table->string('posisi'); // Posisi Pegawai
            $table->date('tanggal_lahir'); // Tanggal lahir
            $table->string('upload_KTP'); // Path dokumen
            $table->boolean('is_admin')->default(false); // Admin status (0 = bukan admin, 1 = admin)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
