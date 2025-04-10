<?php

namespace Database\Factories;

use App\Models\StudentFeePayment;
use App\Models\StudentAcademicYear; // Import
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFeePaymentFactory extends Factory
{
    protected $model = StudentFeePayment::class;

    public function definition(): array
    {
        // Assumes StudentAcademicYear records exist
        $enrollment = StudentAcademicYear::inRandomOrder()->first() ?? StudentAcademicYear::factory()->create();

        return [
            'student_academic_year_id' => $enrollment->id,
            'amount' => $this->faker->randomFloat(2, 50, 500), // Amount between 50.00 and 500.00
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => $this->faker->optional(0.3)->sentence(), // 30% chance of having notes
        ];
    }
}