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
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        
        // Drop tables in dependency order (child tables first, then parent tables)
        
        // Step 1: Drop tables that reference student_academic_years
        Schema::dropIfExists('student_absences');
        Schema::dropIfExists('student_warnings');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('student_transport_assignments');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('student_results');
        Schema::dropIfExists('student_fee_payments');
        Schema::dropIfExists('academic_year_fees');
        
        // Step 2: Drop tables that directly reference academic_years
        Schema::dropIfExists('academic_year_subjects');
        Schema::dropIfExists('student_academic_years');
        
        // Step 3: Finally drop the academic_years table
        Schema::dropIfExists('academic_years');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This down method would need to recreate all the dropped tables
        // This is a complex operation and would require recreating all the original migrations
        // For now, we'll leave this empty as recreating all these tables would be extensive
        
        // If you need to reverse this migration, you would need to run the original migrations
        // that created these tables in the correct order
    }
};
