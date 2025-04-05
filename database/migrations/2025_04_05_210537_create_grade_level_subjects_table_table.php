<?php

use App\Models\GradeLevel;
use App\Models\Subject;
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
        Schema::create('grade_level_subjects_table', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(GradeLevel::class)->constrained();
            $table->foreignIdFor(Subject::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_level_subjects_table');
    }
};
