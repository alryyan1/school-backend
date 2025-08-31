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
        if (Schema::hasTable('student_notes')) {
            // Check if student_academic_years_id column exists
            $columns = DB::select("SHOW COLUMNS FROM student_notes LIKE 'student_academic_years_id'");
            if (!empty($columns)) {
                // Drop the foreign key constraint first
                try {
                    Schema::table('student_notes', function (Blueprint $table) {
                        $table->dropForeign(['student_academic_years_id']);
                    });
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                
                // Drop the old column
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->dropColumn('student_academic_years_id');
                });
            }
            
            // Check if student_id column doesn't exist
            $studentIdColumns = DB::select("SHOW COLUMNS FROM student_notes LIKE 'student_id'");
            if (empty($studentIdColumns)) {
                // Add the new student_id column
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_notes')) {
            // Drop the new student_id column
            try {
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->dropForeign(['student_id']);
                    $table->dropColumn('student_id');
                });
            } catch (\Exception $e) {
                // Column doesn't exist, continue
            }
            
            // Add back the old student_academic_years_id column
            $studentAcademicYearColumns = DB::select("SHOW COLUMNS FROM student_notes LIKE 'student_academic_years_id'");
            if (empty($studentAcademicYearColumns)) {
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->unsignedBigInteger('student_academic_years_id')->nullable()->after('id');
                });
            }
        }
    }
};
