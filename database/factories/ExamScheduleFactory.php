<?php

namespace Database\Factories;

use App\Models\ExamSchedule;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ExamScheduleFactory extends Factory
{
    protected $model = ExamSchedule::class;

    public function definition(): array
    {
        $exam = Exam::inRandomOrder()->first() ?? Exam::factory()->create();
        $gradeLevel = GradeLevel::inRandomOrder()->first() ?? GradeLevel::factory()->create();
        // Find subject and classroom potentially linked to grade (more complex logic needed for realistic data)
        $subject = Subject::inRandomOrder()->first() ?? Subject::factory()->create();
        $classroom = Classroom::where('grade_level_id', $gradeLevel->id)->where('school_id', $exam->school_id)->inRandomOrder()->first();
        $teacher = Teacher::inRandomOrder()->first(); // Any teacher as invigilator example

        $examDate = $this->faker->dateTimeBetween($exam->start_date, $exam->end_date)->format('Y-m-d');
        $startTime = Carbon::createFromTime($this->faker->numberBetween(8, 14), 0, 0); // Start between 8 AM and 2 PM

        return [
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'grade_level_id' => $gradeLevel->id,
            'classroom_id' => $this->faker->optional(0.7)->randomElement([$classroom?->id]), // 70% chance if classroom found
            'teacher_id' => $this->faker->optional(0.9)->randomElement([$teacher?->id]), // 90% chance if teacher found
            'exam_date' => $examDate,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $startTime->addHours($this->faker->numberBetween(1, 3))->format('H:i:s'), // 1-3 hour duration
            'max_marks' => $this->faker->randomElement([50.00, 100.00]),
            'pass_marks' => $this->faker->optional(0.9)->randomElement([25.00, 50.00]), // 90% chance
        ];
    }
}