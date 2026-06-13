<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('rekomendasi_perjalanan', function (Blueprint $table) {
            $table->text('saran_efisiensi')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('rekomendasi_perjalanan', function (Blueprint $table) {
            $table->string('saran_efisiensi')->nullable()->change();
        });
    }
};