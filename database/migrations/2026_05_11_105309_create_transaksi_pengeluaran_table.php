<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_pengeluaran', function (Blueprint $table) {
            $table->id('id_transaksi_pengeluaran');

            $table->unsignedBigInteger('id_pengajuan')->nullable();
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->unsignedBigInteger('id_akun')->nullable();

            $table->string('jenis_pengeluaran');
            $table->date('tanggal_pengeluaran');
            $table->string('uraian');
            $table->decimal('nominal', 15, 2);
            $table->string('bukti')->nullable();

            $table->enum('status', [
                'verifikasi_pengeluaran',
                'pembayaran',
                'transaksi_tercatat',
                'ditolak'
            ])->default('verifikasi_pengeluaran');

            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_pembayaran')->nullable();
            $table->timestamp('tanggal_tercatat')->nullable();

            $table->timestamps();

            $table->foreign('id_pengajuan')
                ->references('id_pengajuan')
                ->on('pengajuan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pengeluaran');
    }
};