<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('verifikasis', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------
            | TAMBAH KOLOM JIKA BELUM ADA
            |--------------------------------------------------------------
            */

            if (!Schema::hasColumn('verifikasis', 'id_transaksi_pengeluaran')) {

                $table->unsignedBigInteger('id_transaksi_pengeluaran')
                    ->nullable()
                    ->after('id_pengajuan');

                $table->foreign('id_transaksi_pengeluaran')
                    ->references('id_transaksi_pengeluaran')
                    ->on('transaksi_pengeluaran')
                    ->onDelete('cascade');
            }
        });

        /*
        |--------------------------------------------------------------
        | UPDATE ENUM STATUS
        |--------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE verifikasis
            MODIFY COLUMN status
            ENUM('pending','approve','reject')
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasis', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------
            | HAPUS FOREIGN KEY JIKA ADA
            |--------------------------------------------------------------
            */

            if (Schema::hasColumn('verifikasis', 'id_transaksi_pengeluaran')) {

                $table->dropForeign([
                    'id_transaksi_pengeluaran'
                ]);

                $table->dropColumn(
                    'id_transaksi_pengeluaran'
                );
            }
        });

        /*
        |--------------------------------------------------------------
        | KEMBALIKAN ENUM
        |--------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE verifikasis
            MODIFY COLUMN status
            ENUM('approve','reject')
            NOT NULL
        ");
    }
};