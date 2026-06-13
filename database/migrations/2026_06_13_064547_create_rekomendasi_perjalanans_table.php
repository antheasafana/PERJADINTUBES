<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migrasi untuk membuat tabel rekomendasi perjalanan.
     */
    public function up(): void {
        Schema::create('rekomendasi_perjalanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID Pegawai
            $table->text('analisis_rekomendasi');  // Hasil teks analisis dari Gemini AI
            $table->string('tujuan_terpopuler')->nullable();
            $table->string('saran_efisiensi')->nullable();
            $table->timestamps();

            // Opsional: Menambahkan foreign key constraint jika tabel users ada
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void {
        Schema::dropIfExists('rekomendasi_perjalanan');
    }
};