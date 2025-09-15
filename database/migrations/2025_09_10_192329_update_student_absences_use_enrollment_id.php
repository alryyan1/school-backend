<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_absences', function (Blueprint $table) {
            // Check if student_id column exists and drop it
            if (Schema::hasColumn('student_absences', 'student_id')) {
                // Try to drop foreign key constraint if it exists
                try {
                    $table->dropForeign(['student_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropIndex(['student_id']);
                $table->dropColumn('student_id');
            }
            
            // Check if student_academic_year_id column exists and rename it to enrollment_id
            if (Schema::hasColumn('student_absences', 'student_academic_year_id')) {
                $table->renameColumn('student_academic_year_id', 'enrollment_id');
            } else {
                // Add the new enrollment_id column if neither exists
                $table->foreignId('enrollment_id')->after('id')->constrained('enrollments')->onDelete('cascade');
                $table->index('enrollment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_absences', function (Blueprint $table) {
            // Rename enrollment_id back to student_academic_year_id
            if (Schema::hasColumn('student_absences', 'enrollment_id')) {
                $table->renameColumn('enrollment_id', 'student_academic_year_id');
            }
        });
    }
};
