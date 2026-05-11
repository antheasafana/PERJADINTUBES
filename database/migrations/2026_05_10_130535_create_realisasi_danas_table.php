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
        Schema::create('realisasi_dana', function (Blueprint $table) { // tanpa 's'
            $table->id('id_realisasi');
            $table->foreignId('id_pengajuan')
                ->unique()
                ->constrained('pengajuan', 'id_pengajuan')
                ->onDelete('cascade');
            $table->date('tgl_realisasi');
            $table->decimal('total_realisasi', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_dana');
    }
};
