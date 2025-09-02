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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('academic_year')->nullable();
            $table->foreignId('grade_level_id')->constrained('grade_levels')->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->enum('status', ['active', 'transferred', 'graduated', 'withdrawn'])->default('active');
            $table->enum('enrollment_type', ['regular', 'scholarship'])->default('regular');
            $table->integer('fees');
            $table->integer('discount');
            $table->timestamps();
            
            $table->index(['student_id', 'academic_year']);
            $table->index('status');
            $table->index('enrollment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
