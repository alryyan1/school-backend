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
        // The create migration already defines enrollment_id on fresh installs
        if (Schema::hasColumn('fee_installments', 'enrollment_id')) {
            return;
        }

        Schema::table('fee_installments', function (Blueprint $table) {
            // Drop the existing student_id column and its foreign key
            
            // Add the correct enrollment_id column
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            
            // Update the index
            $table->index('enrollment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_installments', function (Blueprint $table) {
            // Drop the enrollment_id column and its foreign key
            $table->dropColumn('enrollment_id');
            


        });
    }
};
