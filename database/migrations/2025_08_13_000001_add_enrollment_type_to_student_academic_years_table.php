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
        Schema::table('student_academic_years', function (Blueprint $table) {
            // regular = عادي, scholarship = منحة
            $table->enum('enrollment_type', ['regular', 'scholarship'])->default('regular')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic_years', function (Blueprint $table) {
            $table->dropColumn('enrollment_type');
        });
    }
};


