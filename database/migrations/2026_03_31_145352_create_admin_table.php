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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_admin'); // Nama
            $table->integer('nip_admin'); // nip
            $table->string('posisi_admin'); // Kategori
            $table->date('tanggal_lahir_admin'); // Tanggal lahir
            $table->string('dokumen_admin'); // Path dokumen
            $table->boolean('is_admin')->default(false); // Admin status (0 = bukan admin, 1 = admin) 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
