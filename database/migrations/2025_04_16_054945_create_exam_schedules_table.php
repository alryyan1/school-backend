<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_exam_schedules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Exam;       // Import related models
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\Classroom;
use App\Models\Teacher;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Exam::class)->constrained()->cascadeOnDelete(); // Link to the overall exam period
            $table->foreignIdFor(Subject::class)->constrained()->cascadeOnDelete(); // Which subject
            $table->foreignIdFor(GradeLevel::class)->constrained(); // Which grade level is taking it
            $table->foreignIdFor(Classroom::class)->nullable()->constrained()->nullOnDelete(); // Optional: Specific room
            $table->foreignIdFor(Teacher::class)->nullable()->constrained()->nullOnDelete(); // Optional: Invigilator
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('max_marks', 5, 2)->default(100.00); // Example: 100.00
            $table->decimal('pass_marks', 5, 2)->nullable();   // Example: 50.00
            $table->timestamps();

            // Optional: Prevent scheduling the same subject for the same grade/classroom multiple times within the same exam period
            // $table->unique(['exam_id', 'subject_id', 'grade_level_id', 'classroom_id'], 'exam_schedule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};