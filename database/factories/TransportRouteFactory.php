<?php // database/factories/TransportRouteFactory.php
namespace Database\Factories;

use App\Models\TransportRoute;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransportRouteFactory extends Factory
{
    protected $model = TransportRoute::class;
    public function definition(): array
    {
        $school = School::inRandomOrder()->first() ?? School::factory()->create();
        $driver = User::where('role', 'driver')->inRandomOrder()->first(); // Assuming a 'driver' role exists
        return [
            'school_id' => $school->id,
            'name' => 'Route ' . $this->faker->randomLetter() . $this->faker->randomDigitNotNull(),
            'description' => $this->faker->optional()->sentence,
            'driver_id' => $this->faker->optional(0.8)->randomElement([$driver?->id]),
            'fee_amount' => $this->faker->optional(0.7)->randomFloat(2, 50, 200),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
