<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('realisasi_dana', 'status')) {
            return;
        }

        Schema::table('realisasi_dana', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'TEREALISASI'])
                ->default('PENDING')
                ->after('total_realisasi');
            $table->text('catatan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('realisasi_dana', function (Blueprint $table) {
            $table->dropColumn(['catatan', 'status']);
        });
    }
};