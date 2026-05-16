<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pengajuan
            MODIFY COLUMN jenis_pengajuan
            ENUM('UANG_MUKA', 'REIMBURSEMENT', 'PENGEMBALIAN') NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE pengajuan
            MODIFY COLUMN jenis_pengajuan
            ENUM('UANG_MUKA', 'REIMBURSEMENT') NOT NULL
        ");
    }
};
