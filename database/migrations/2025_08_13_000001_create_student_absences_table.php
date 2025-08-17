<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_academic_year_id')->constrained('student_academic_years')->cascadeOnDelete();
            $table->date('absent_date');
            $table->string('reason')->nullable();
            $table->boolean('excused')->default(false);
            $table->timestamps();
            $table->unique(['student_academic_year_id', 'absent_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_absences');
    }
};


