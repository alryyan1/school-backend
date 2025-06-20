<?php // database/factories/ExamResultFactory.php
namespace Database\Factories;
use App\Models\ExamResult; use App\Models\StudentAcademicYear; use App\Models\ExamSchedule; use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamResult>
 */
class ExamResultFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExamResult::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $examSchedule = ExamSchedule::inRandomOrder()->first() ?? ExamSchedule::factory()->create();
        // Find an enrollment matching the schedule's grade and year (simplified)
        $enrollment = StudentAcademicYear::where('academic_year_id', $examSchedule->exam->academic_year_id) // Assuming Exam has direct academic_year_id
                                      ->where('grade_level_id', $examSchedule->grade_level_id)
                                      ->inRandomOrder()->first() ?? StudentAcademicYear::factory()->create([
                                          'academic_year_id' => $examSchedule->exam->academic_year_id,
                                          'grade_level_id' => $examSchedule->grade_level_id,
                                      ]);
        $isAbsent = fake()->boolean(10); // 10% chance of being absent
        $marksObtained = $isAbsent ? null : fake()->randomFloat(2, 0, (float)$examSchedule->max_marks);
        return [
            'student_academic_year_id' => $enrollment->id,
            'exam_schedule_id' => $examSchedule->id,
            'marks_obtained' => $marksObtained,
            'grade_letter' => $isAbsent ? 'AB' : fake()->optional(0.7)->randomElement(['A', 'B+', 'C', 'Pass']),
            'is_absent' => $isAbsent,
            'remarks' => fake()->optional(0.2)->sentence,
            'entered_by_user_id' => User::where('role', 'teacher')->orWhere('role','admin')->inRandomOrder()->first()?->id,
        ];
    }
}