<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\School; // Import School
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon; // Use Carbon for easier date manipulation

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        // Assumes School records exist
        $school = School::inRandomOrder()->first() ?? School::factory()->create();
        $startDate = Carbon::instance($this->faker->dateTimeBetween('-1 month', '+3 months'));
        $endDate = $startDate->copy()->addDays($this->faker->numberBetween(3, 10)); // Exam period 3-10 days

        return [
            'name' => 'امتحانات ' . $this->faker->randomElement(['منتصف الفصل', 'نهاية الفصل', 'الفترة الأولى', 'الفترة الثانية']) . ' ' . $startDate->format('Y'),
            'school_id' => $school->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'description' => $this->faker->optional(0.5)->sentence, // 50% chance of description
        ];
    }
}