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
        Schema::create('jurnal', function (Blueprint $table) {
    $table->id('id_jurnal');

    $table->foreignId('id_pembayaran')
          ->nullable()
          ->constrained('pembayaran', 'id_pembayaran')
          ->cascadeOnDelete();

    $table->date('tanggal_jurnal');

    $table->string('keterangan');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};
