<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old student_id FK/index safely using information_schema, then drop column
        if (Schema::hasColumn('student_warnings', 'student_id')) {
            $fk = DB::selectOne("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'student_warnings' AND COLUMN_NAME = 'student_id' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1");
            if ($fk && isset($fk->CONSTRAINT_NAME)) {
                DB::statement("ALTER TABLE `student_warnings` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }
            $indexes = DB::select("SHOW INDEX FROM `student_warnings` WHERE Key_name IN ('student_id', 'student_warnings_student_id_index')");
            foreach ($indexes as $idx) {
                if (!empty($idx->Key_name)) {
                    DB::statement("ALTER TABLE `student_warnings` DROP INDEX `{$idx->Key_name}`");
                }
            }
            Schema::table('student_warnings', function (Blueprint $table) {
                $table->dropColumn('student_id');
            });
        }

        // Add enrollment_id with FK if missing
        if (!Schema::hasColumn('student_warnings', 'enrollment_id')) {
            Schema::table('student_warnings', function (Blueprint $table) {
                $table->foreignId('enrollment_id')->after('id')->constrained('enrollments')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('student_warnings', 'enrollment_id')) {
            // Drop FK by resolving name dynamically
            $fk = DB::selectOne("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'student_warnings' AND COLUMN_NAME = 'enrollment_id' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1");
            if ($fk && isset($fk->CONSTRAINT_NAME)) {
                DB::statement("ALTER TABLE `student_warnings` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }
            $indexes = DB::select("SHOW INDEX FROM `student_warnings` WHERE Key_name IN ('enrollment_id', 'student_warnings_enrollment_id_index')");
            foreach ($indexes as $idx) {
                if (!empty($idx->Key_name)) {
                    DB::statement("ALTER TABLE `student_warnings` DROP INDEX `{$idx->Key_name}`");
                }
            }
            Schema::table('student_warnings', function (Blueprint $table) {
                $table->dropColumn('enrollment_id');
            });
        }

        if (!Schema::hasColumn('student_warnings', 'student_id')) {
            Schema::table('student_warnings', function (Blueprint $table) {
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->index('student_id');
            });
        }
    }
};


