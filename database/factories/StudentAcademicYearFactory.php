<?php

namespace Database\Factories;

use App\Models\StudentAcademicYear;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentAcademicYearFactory extends Factory
{
    protected $model = StudentAcademicYear::class;

    public function definition(): array
    {
        // !! Important: Assumes related models (Student, AcademicYear, GradeLevel) have records !!
        // Fetch random existing IDs. In a real seeder, you might control this better.
        $student = Student::inRandomOrder()->first() ?? Student::factory()->create();
        $academicYear = AcademicYear::inRandomOrder()->first() ?? AcademicYear::factory()->create();
        $gradeLevel = GradeLevel::inRandomOrder()->first() ?? GradeLevel::factory()->create();

        // Find a classroom matching the grade level (or make it nullable)
        $classroom = Classroom::where('grade_level_id', $gradeLevel->id)->inRandomOrder()->first();

        return [
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'grade_level_id' => $gradeLevel->id,
            'classroom_id' => $this->faker->optional(0.8)->randomElement([$classroom?->id]), // 80% chance of having classroom if found
            'status' => $this->faker->randomElement(['active', 'transferred', 'graduated', 'withdrawn']),
        ];
    }
}