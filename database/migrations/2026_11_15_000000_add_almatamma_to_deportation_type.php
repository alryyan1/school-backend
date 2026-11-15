<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * إضافة نوع ترحيل ثالث: المتمة
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE enrollments MODIFY deportation_type ENUM('داخلي', 'خارجي', 'المتمة') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE enrollments SET deportation_type = NULL WHERE deportation_type = 'المتمة'");
        DB::statement("ALTER TABLE enrollments MODIFY deportation_type ENUM('داخلي', 'خارجي') NULL");
    }
};
