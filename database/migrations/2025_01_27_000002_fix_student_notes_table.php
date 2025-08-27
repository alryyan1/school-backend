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
        // Check if student_notes table exists and has the problematic column
        if (Schema::hasTable('student_notes')) {
            $columns = DB::select("SHOW COLUMNS FROM student_notes LIKE 'student_academic_years_id'");
            if (!empty($columns)) {
                // Remove the student_academic_years_id column from student_notes table
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->dropForeign(['student_academic_years_id']);
                    $table->dropColumn('student_academic_years_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the student_academic_years_id column if needed
        if (Schema::hasTable('student_notes')) {
            $columns = DB::select("SHOW COLUMNS FROM student_notes LIKE 'student_academic_years_id'");
            if (empty($columns)) {
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->unsignedBigInteger('student_academic_years_id')->nullable()->after('id');
                });
            }
        }
    }
};
