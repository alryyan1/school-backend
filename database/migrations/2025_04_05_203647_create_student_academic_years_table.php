<?php

use App\Models\academicYear;
use App\Models\AcademicYear as ModelsAcademicYear;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\StudentAcademicYear;
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
        Schema::create('student_academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained();
            $table->foreignIdFor(AcademicYear::class)->constrained();
            $table->foreignIdFor(GradeLevel::class)->constrained(); // الصف الدراسي (مثال: الصف الأول الثانوي)
            $table->foreignIdFor(Classroom::class)->nullable()->constrained(); // الفصل (مثال: "أولى ثانوي أ")
            $table->enum('status', ['active', 'transferred', 'graduated', 'withdrawn'])->default('active');
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id']); // منع تكرار تسجيل الطالب في نفس السنة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_years');
    }
};
