<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('realisasi_dana', function (Blueprint $table) {
            if (! Schema::hasColumn('realisasi_dana', 'status')) {
                $table->enum('status', ['PENDING', 'TEREALISASI'])
                    ->default('PENDING')
                    ->after('total_realisasi');
            }

            if (! Schema::hasColumn('realisasi_dana', 'catatan')) {
                $table->text('catatan')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('realisasi_dana', function (Blueprint $table) {
            if (Schema::hasColumn('realisasi_dana', 'catatan')) {
                $table->dropColumn('catatan');
            }

            if (Schema::hasColumn('realisasi_dana', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
