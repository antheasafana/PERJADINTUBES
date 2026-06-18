<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengiriman_emails')) {

            Schema::create('pengiriman_emails', function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger('id_realisasi');

                $table->foreign('id_realisasi')
                      ->references('id_realisasi')
                      ->on('realisasi_dana')
                      ->onDelete('cascade');

                $table->string('email');

                $table->boolean('status_kirim')
                      ->default(false);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_emails');
    }
};