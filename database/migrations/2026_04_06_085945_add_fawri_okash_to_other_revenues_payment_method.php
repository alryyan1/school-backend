<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE other_revenues MODIFY COLUMN payment_method ENUM('cash', 'bank', 'fawri', 'okash') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE other_revenues MODIFY COLUMN payment_method ENUM('cash', 'bank') NOT NULL");
    }
};
