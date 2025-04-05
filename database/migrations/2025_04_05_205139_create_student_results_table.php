<?php

use App\Models\AcademicYearSubject;
use App\Models\Exam;
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
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StudentAcademicYear::class)->constrained();
            $table->foreignIdFor(AcademicYearSubject::class)->constrained(); // المادة في السنة الدراسية
            $table->foreignIdFor(Exam::class)->constrained(); // الامتحان (فصلية، نهائية، إلخ)
            $table->decimal('marks', 5, 2);
            $table->string('grade')->nullable(); // تقدير (A, B, C)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};
