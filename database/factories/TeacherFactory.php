<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr; // Import Arr helper if needed elsewhere, not strictly here
use Illuminate\Support\Str; // Import Str if needed for complex strings

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['ذكر', 'انثي']); // Match enum values

        return [
            // Required Fields
            'national_id' => $this->faker->unique()->numerify('###########'), // Unique 11-digit string
            'name' => $this->faker->name($gender === 'ذكر' ? 'male' : 'female') . ' ' . $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $gender,
            'qualification' => $this->faker->randomElement([
                'بكالوريوس تربية',
                'ليسانس آداب',
                'بكالوريوس علوم',
                'ماجستير لغة عربية',
                'دبلوم معلمين',
                'هندسة معلوماتية'
            ]),
            'hire_date' => $this->faker->dateTimeBetween('-10 years', '-1 month')->format('Y-m-d'), // Hired between 1 month and 10 years ago

            // Nullable Fields
            'phone' => $this->faker->optional(0.9)->numerify('09########'), // 90% chance, Syrian-like format
            'birth_date' => $this->faker->optional(0.95)?->dateTimeBetween('-60 years', '-23 years')?->format('Y-m-d'), // 95% chance, Age 23-60
            'address' => $this->faker->optional(0.85)->address(), // 85% chance
            'photo' => null, // Default to null, generating/managing files is complex for basic seeding

            // Fields with Defaults
            'is_active' => $this->faker->boolean(95), // 95% chance of being active

            // Timestamps are handled automatically
        ];
    }
}