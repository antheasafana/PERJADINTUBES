<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->foreignId('id_pegawai')->constrained('pegawai');
            // ✅ TAMBAH 'PENGEMBALIAN'
            $table->enum('jenis_pengajuan', ['UANG_MUKA', 'REIMBURSEMENT', 'PENGEMBALIAN']);
            $table->foreignId('id_pengajuan_parent')->nullable();
            $table->string('tujuan');
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->decimal('estimasi_biaya', 15, 2)->nullable();
            // ✅ UBAH json → string karena controller simpan nama file
            $table->string('dokumen')->nullable();
            $table->string('status')->default('Diajukan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};