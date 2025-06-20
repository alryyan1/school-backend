<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Carbon\Carbon; // For date manipulation

/**
 * @extends \\Illuminate\\Database\\Eloquent\\Factories\\Factory<\\App\\Models\\Student>
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

        // Common Arabic first names (adjust or expand)
        $maleFirstNames = ['أحمد', 'محمد', 'علي', 'خالد', 'عمر', 'يوسف', 'عبدالله', 'سعيد', 'حسن', 'إبراهيم'];
        $femaleFirstNames = ['فاطمة', 'مريم', 'زينب', 'عائشة', 'نور', 'سارة', 'هند', 'ليلى', 'خديجة', 'آمنة'];
        // Common Arabic last names (can be used as part of full name)
        $commonLastNames = ['الأحمد', 'المحمود', 'الهاشمي', 'القرشي', 'التميمي', 'العمري', 'الخالدي', 'السعيد', 'الحسن', 'الغامدي', 'العتيبي', 'المالكي'];

        $firstName = $this->faker->randomElement($gender === 'ذكر' ? $maleFirstNames : $femaleFirstNames);
        $middleName1 = $this->faker->randomElement($commonLastNames); // Use as a middle name segment
        $middleName2 = $this->faker->randomElement($commonLastNames);
        $lastName = $this->faker->randomElement($commonLastNames);

        $studentFullName = $firstName . ' ' . $middleName1 . ' ' . $middleName2 . ' ' . $lastName;
        // Ensure uniqueness if names are short or list is small, by appending a random number
        if (strlen($studentFullName) < 15) { // Arbitrary length check
            $studentFullName .= ' ' . $this->faker->randomNumber(2);
        }


        $approved = $this->faker->boolean(85);
        // Error  Call to a member function numerify() on null.
        //call saveemail on null
        return [
            'student_name' => $studentFullName,
            'email' => $this->faker?->optional(0.7)->unique()?->safeEmail(), // 70% chance
            'date_of_birth' => $this->faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d'),
            'gender' => $gender,
            'goverment_id' => $this->faker?->optional(0.8)->unique()?->numerify('###########'), // 80% chance
            'wished_level' => $this->faker?->randomElement(['روضه', 'ابتدائي', 'متوسط', 'ثانوي']),
            'medical_condition' => $this->faker?->optional(0.05)->randomElement(['ربو خفيف', 'حساسية قمح', 'لا يوجد']),
            'referred_school' => $this->faker?->optional(0.2)->randomElement(['مدرسة التفوق', 'مدرسة الإبداع', 'مدرسة الأوائل']),
            'success_percentage' => $this->faker?->optional(0.5)->numberBetween(60, 99),

            'image' => null, // Default to null

            'father_name' => $this->faker?->randomElement($maleFirstNames) . ' ' . $this->faker?->randomElement($commonLastNames) . ' ' . $lastName,
            'father_job' => $this->faker?->randomElement(['مهندس', 'طبيب', 'معلم', 'موظف حكومي', 'رجل أعمال', 'محاسب']),
            'father_address' => 'حي ' . $this->faker?->randomElement(['النزهة', 'السلام', 'الروضة', 'الملك فهد']) . '، شارع ' . $this->faker?->streetName(),
            'father_phone' => $this->faker?->numerify('05########'), // Saudi-like mobile format
            'father_whatsapp' => $this->faker?->optional(0.6)->numerify('05########'),

            'mother_name' => $this->faker->randomElement($femaleFirstNames) . ' ' . $this->faker->randomElement($commonLastNames) . ' ' . $lastName,
            'mother_job' => $this->faker->randomElement(['معلمة', 'ربة منزل', 'طبيبة', 'مهندسة', 'موظفة']),
            'mother_address' => $this->faker->address(),
            'mother_phone' => $this->faker?->numerify('05########'),
            'mother_whatsapp' => $this->faker?->optional(0.6)->numerify('05########'),

            'other_parent' => $this->faker->optional(0.1)->name(),
            'relation_of_other_parent' => $this->faker->optional(0.1)->randomElement(['عم', 'خال', 'جد']),
            'relation_job' => $this->faker->optional(0.1)->jobTitle(),
            'relation_phone' => $this->faker?->optional(0.1)->numerify('05########'),
            'relation_whatsapp' => $this->faker?->optional(0.05)->numerify('05########'),

            // 'closest_name' => $this->faker->optional(0.3)->name(),
            // 'closest_phone' => $this->faker?->optional(0.3)->numerify('05########'),

            'approved' => $approved,
            'aproove_date' => $approved ? Carbon::instance($this->faker->dateTimeThisYear('-2 months'))->format('Y-m-d H:i:s') : null,
            // For approved_by_user, ensure you have admin users seeded first or set to null.
            'approved_by_user' => $approved ? (User::where('role', 'admin')->inRandomOrder()->first()?->id ?? null) : null,
            'message_sent' => $approved ? $this->faker->boolean(60) : false,

            // Timestamps are handled automatically
        ];
    }
}