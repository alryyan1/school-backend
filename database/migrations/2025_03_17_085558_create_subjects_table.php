<?php
// database/migrations/xxxx_xx_xx_create_subjects_table.php

use App\Models\GradeLevel;
use App\Models\School;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the subject (e.g., "Mathematics", "Science")
            $table->string('code')->unique(); // Short code (e.g., "MATH101")
            $table->text('description')->nullable(); // Optional description
            // $table->foreignIdFor(School::class)->nullable()->constrained(); // If multiple schools are supported
            // $table->foreignIdFor(GradeLevel::class)->nullable()->constrained(); // Optional: if subjects vary by grade
            // $table->foreignIdFor('teacher_id')->nullable()->constrained(); // Optional: assigned teacher
            // $table->boolean('is_active')->default(true); // To disable a subject if needed
            // $table->integer('credit_hours')->nullable(); // For higher education (e.g., 3 credits)
            // $table->string('type')->nullable(); // e.g., "Core", "Elective", "Optional"
            $table->timestamps();
            // $table->softDeletes(); // For archiving subjects
        
            // Foreign keys
            // $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            // $table->foreign('grade_level_id')->references('id')->on('grade_levels')->onDelete('set null');
            // $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}