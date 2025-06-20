<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_exam_results_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StudentAcademicYear; // Represents the student's enrollment for a specific year
use App\Models\ExamSchedule;       // Represents the specific scheduled exam (subject, date, time)
use App\Models\User;               // Represents the user who entered/updated the marks

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();

            // Foreign key linking to the student's specific enrollment for the academic year
            // This enrollment record (StudentAcademicYear) contains student_id, academic_year_id, grade_level_id, school_id
            $table->foreignIdFor(StudentAcademicYear::class)
                  ->constrained() // Assumes 'student_academic_years' table
                  ->cascadeOnDelete(); // If enrollment is deleted, delete associated results

            // Foreign key linking to the specific scheduled exam
            // ExamSchedule links to Exam (period), Subject, GradeLevel, Classroom (optional), Teacher (invigilator - optional)
            $table->foreignIdFor(ExamSchedule::class)
                  ->constrained() // Assumes 'exam_schedules' table
                  ->cascadeOnDelete(); // If a schedule item is deleted, delete associated results

            $table->decimal('marks_obtained', 5, 2)->nullable(); // Actual marks student received (e.g., 85.50)
            // max_marks and pass_marks are typically on the ExamSchedule model/table,
            // but can be denormalized here if they could vary per student per result (uncommon).

            $table->string('grade_letter', 10)->nullable(); // e.g., A+, B, C, Pass, Fail, Excellent
            $table->boolean('is_absent')->default(false);   // Was the student absent for this exam?
            $table->text('remarks')->nullable();            // Any teacher remarks or comments

            // Track who entered and last updated the marks
            $table->foreignIdFor(User::class, 'entered_by_user_id')
                  ->nullable()
                  ->constrained('users') // References 'id' on 'users' table
                  ->nullOnDelete();     // If user is deleted, set this to null

            $table->foreignIdFor(User::class, 'updated_by_user_id')
                  ->nullable()
                  ->constrained('users') // References 'id' on 'users' table
                  ->nullOnDelete();

            $table->timestamps(); // created_at and updated_at

            // Unique constraint: A student can only have one result entry
            // for a specific scheduled exam (within their enrollment for that year).
            $table->unique(['student_academic_year_id', 'exam_schedule_id'], 'student_exam_schedule_result_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};