<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE verifikasi MODIFY COLUMN status ENUM('pending','approve','reject') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE verifikasi MODIFY COLUMN status ENUM('approve','reject')");
    }
};