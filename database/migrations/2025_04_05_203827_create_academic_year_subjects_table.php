<?php

use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\StudentAcademicYear;
use App\Models\Subject;
use App\Models\Teacher;
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
        Schema::create('academic_year_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AcademicYear::class)->constrained();
            $table->foreignIdFor(GradeLevel::class)->constrained(); // المواد تختلف حسب الصف
            $table->foreignIdFor(Subject::class)->constrained(); // المادة الدراسية
            $table->foreignIdFor(Teacher::class)->nullable()->constrained(); // المعلم المسؤول
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_subjects');
    }
};
