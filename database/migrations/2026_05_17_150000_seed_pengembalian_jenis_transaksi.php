<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('jenis_transaksi')
            ->where('jenis_transaksi', 'Pengembalian')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('jenis_transaksi')->insert([
            'jenis_transaksi' => 'Pengembalian',
            'keterangan' => 'Pengembalian sisa dana perjalanan dinas ke admin/kas negara',
            'bukti_transaksi' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('jenis_transaksi')
            ->where('jenis_transaksi', 'Pengembalian')
            ->delete();
    }
};
