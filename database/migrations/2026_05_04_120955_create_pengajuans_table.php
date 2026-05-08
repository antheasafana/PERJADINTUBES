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

    Schema::create('pengajuan', function (Blueprint $table) {
    $table->id('id_pengajuan');
    $table->foreignId('id_pegawai')->constrained('pegawai');
    $table->enum('jenis_pengajuan',['UANG_MUKA','REIMBURSEMENT']);
    $table->foreignId('id_pengajuan_parent')->nullable();
    $table->string('tujuan');
    $table->date('tgl_berangkat');
    $table->date('tgl_kembali');
    $table->decimal('estimasi_biaya',15,2)->nullable();
    $table->json('dokumen')->nullable();
    $table->string('status')->default('Diajukan');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
