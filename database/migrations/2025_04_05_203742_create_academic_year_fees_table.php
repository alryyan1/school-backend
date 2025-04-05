<?php

use App\Models\GradeLevel;
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
        Schema::create('academic_year_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StudentAcademicYear::class)->constrained();
            $table->foreignIdFor(GradeLevel::class)->constrained(); // رسوم تختلف حسب الصف
            $table->string('name'); // مثال: "رسوم التسجيل"
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['one_time', 'monthly', 'termly', 'yearly']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_fees');
    }
};
