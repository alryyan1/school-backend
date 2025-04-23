<?php
// database/migrations/xxxx_xx_xx_xxxxxx_modify_student_fee_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FeeInstallment; // Import the new model
use App\Models\StudentAcademicYear; // Keep for dropping old key

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_fee_payments', function (Blueprint $table) {
            if (Schema::hasColumn('student_fee_payments', 'student_academic_year_id')) {
                // Laravel will automatically resolve the foreign key constraint name
                $table->dropForeign(['student_academic_year_id']);
                $table->dropColumn('student_academic_year_id');
            }
        });
    
        Schema::table('student_fee_payments', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\FeeInstallment::class)
                  ->after('id')
                  ->constrained()
                  ->cascadeOnDelete();
    
            $table->index('fee_installment_id');
        });
    }
    
};