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
            // Check if enrollment_id column doesn't exist
            $enrollmentIdColumns = DB::select("SHOW COLUMNS FROM student_notes LIKE 'enrollment_id'");
            if (empty($enrollmentIdColumns)) {
                // Add the new enrollment_id column
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
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
            // Drop the enrollment_id column
            try {
                Schema::table('student_notes', function (Blueprint $table) {
                    $table->dropForeign(['enrollment_id']);
                    $table->dropColumn('enrollment_id');
                });
            } catch (\Exception $e) {
                // Column doesn't exist, continue
            }
        }
    }
};
