<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'pembayaran',
            function (Blueprint $table) {

                $table->id(
                    'id_pembayaran'
                );

                /*
                |--------------------------------------------------------------------------
                | RELASI
                |--------------------------------------------------------------------------
                */

                $table->integer(
                    'id_pengajuan'
                );

                $table->integer(
                    'id_transaksi_pengeluaran'
                );

                /*
                |--------------------------------------------------------------------------
                | JENIS PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'jenis_pembayaran',
                    [

                        'uang_muka',

                        'reimbursement',

                        'pengembalian_dana',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | ARAH TRANSAKSI
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'arah_transaksi',
                    [

                        'admin_ke_pegawai',

                        'pegawai_ke_admin',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | NOMINAL
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'nominal',
                    15,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                $table->enum(
                    'status',
                    [

                        'pending',

                        'dibayar',

                        'selesai',
                    ]
                )->default(
                    'pending'
                );

                /*
                |--------------------------------------------------------------------------
                | TANGGAL
                |--------------------------------------------------------------------------
                */

                $table->timestamp(
                    'tanggal_pembayaran'
                )->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'pembayaran'
        );
    }
};