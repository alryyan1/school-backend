<?php // database/factories/StudentTransportAssignmentFactory.php
namespace Database\Factories;

use App\Models\StudentTransportAssignment;
use App\Models\StudentAcademicYear;
use App\Models\TransportRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentTransportAssignmentFactory extends Factory
{
    protected $model = StudentTransportAssignment::class;
    public function definition(): array
    {
        // Get an enrollment and a route potentially from the same school/year
        $enrollment = StudentAcademicYear::with('academicYear')->inRandomOrder()->first() ?? StudentAcademicYear::factory()->create();
        $route = TransportRoute::where('school_id', $enrollment->school_id)->inRandomOrder()->first(); // Ensure route matches school
        // Basic factory, doesn't handle unique constraint perfectly if run standalone
        if (!$route) return []; // Cannot create assignment without a route in the same school
        return [
            'student_academic_year_id' => $enrollment->id,
            'transport_route_id' => $route->id,
            'pickup_point' => $this->faker->optional()->streetName(),
            'dropoff_point' => $this->faker->optional()->streetName(),
        ];
    }
}
