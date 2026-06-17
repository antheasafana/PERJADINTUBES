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
        Schema::create('verifikasi', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELASI KE PENGAJUAN
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('id_pengajuan');

            $table->foreign('id_pengajuan')
                ->references('id_pengajuan')
                ->on('pengajuan')
                ->onDelete('cascade');

            /*
            |--------------------------------------------------------------------------
            | ADMIN YANG MEMVERIFIKASI
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('admin_id');

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            /*
            |--------------------------------------------------------------------------
            | DATA VERIFIKASI
            |--------------------------------------------------------------------------
            */

            // level approval
            $table->integer('level');

            // approve / reject
            $table->enum('status', [
                'approve',
                'reject'
            ]);

            // catatan admin
            $table->text('catatan')
                ->nullable();

            // alasan reject
            $table->text('alasan_reject')
                ->nullable();

            // tanggal verifikasi
            $table->timestamp('tanggal_verifikasi')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};