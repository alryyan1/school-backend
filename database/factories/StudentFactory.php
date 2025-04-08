<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User; // Import User model if needed for approved_by_user
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr; // Import Arr helper

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['ذكر', 'انثي']); // Match your enum
        $approved = $this->faker->boolean(85); // 85% chance of being approved

        return [
            // Core Student Info
            'student_name' => $this->faker->name($gender === 'ذكر' ? 'male' : 'female') . ' ' . $this->faker->lastName() . ' ' . $this->faker->lastName(), // Attempt a multi-part name
            'email' => $this->faker->optional(0.8)?->unique()?->safeEmail(), // 80% chance of having email
            'date_of_birth' => $this->faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d'), // Ages 5-18
            'gender' => $gender,
            'goverment_id' => $this->faker->optional(0.7)->numerify('###########'), // 11 digits, 70% chance
            'wished_level' => $this->faker->randomElement(['روضه', 'ابتدائي', 'متوسط', 'ثانوي']), // Match your enum
            'medical_condition' => $this->faker->optional(0.1)->sentence(4), // 10% chance
            'referred_school' => $this->faker->optional(0.3)->company(), // 30% chance
            'success_percentage' => $this->faker->optional(0.6)->numberBetween(50, 100), // 60% chance, range 50-100

            // Image Info
            'image' => null, // Start with null, easier than generating placeholder files

            // Father Info
            'father_name' => $this->faker->name('male') . ' ' . $this->faker->lastName(),
            'father_job' => $this->faker->jobTitle(),
            'father_address' => $this->faker->address(),
            'father_phone' => $this->faker->numerify('09########'), // Syrian-like format
            'father_whatsapp' => $this->faker->optional(0.5)->numerify('09########'), // 50% chance

            // Mother Info
            'mother_name' => $this->faker->name('female') . ' ' . $this->faker->lastName(),
            'mother_job' => $this->faker->jobTitle(),
            'mother_address' => $this->faker->address(),
            'mother_phone' => $this->faker->numerify('09########'),
            'mother_whatsapp' => $this->faker->optional(0.5)->numerify('09########'),

            // Other Parent Info (less frequent)
            'other_parent' => $this->faker->optional(0.15)->name(), // 15% chance
            'relation_of_other_parent' => $this->faker->optional(0.15)->randomElement(['عم', 'عمة', 'خال', 'خالة', 'جد', 'جدة', 'أخ', 'أخت']),
            'relation_job' => $this->faker->optional(0.15)->jobTitle(),
            'relation_phone' => $this->faker->optional(0.15)->numerify('09########'),
            'relation_whatsapp' => $this->faker->optional(0.1)->numerify('09########'),

            // Closest Person Info
            // 'closest_name' => $this->faker->optional(0.4)->name(), // 40% chance
            // 'closest_phone' => $this->faker->optional(0.4)->numerify('09########'),

            // Approval Info
            'approved' => $approved,
            // Use correct spelling from migration 'aproove_date'
            'aproove_date' => $approved ? $this->faker->dateTimeThisYear('-1 month') : null, // If approved, set a date within the last year
             // Fetch an admin user ID if approved, otherwise null. Ensure you have admin users seeded first!
            // 'approved_by_user' => $approved ? User::where('role', 'admin')->inRandomOrder()->first()?->id : null, // More robust seeding needed for this
             'approved_by_user' => null, // Set to null for simplicity initially
            'message_sent' => $approved ? $this->faker->boolean(70) : false, // 70% chance of message sent IF approved

             // Timestamps are handled automatically by Eloquent
             // 'created_at' => now(),
             // 'updated_at' => now(),
        ];
    }
}