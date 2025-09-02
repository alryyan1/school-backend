<?php // database/factories/FeeInstallmentFactory.php
namespace Database\Factories;

use App\Models\FeeInstallment;
use App\Models\EnrollMent;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeInstallmentFactory extends Factory
{
    protected $model = FeeInstallment::class;
    public function definition(): array
    {
        $enrollment = EnrollMent::inRandomOrder()->first() ?? EnrollMent::factory()->create();
        $amountDue = $this->faker->randomFloat(2, 100, 2000);
        $paid = $this->faker->optional(0.6)->randomFloat(2, 10, $amountDue); // 60% chance of some payment
        $status = ($paid === null) ? 'pending' : (($paid >= $amountDue) ? 'paid' : 'partially_paid');
        // Add overdue logic based on due_date if needed during actual use
        return [
            'enrollment_id' => $enrollment->id,
            'title' => 'Installment ' . $this->faker->monthName() . ' ' . $this->faker->year(),
            'amount_due' => $amountDue,
            'amount_paid' => $paid ?? 0.00,
            'due_date' => $this->faker->dateTimeBetween('-6 months', '+6 months')->format('Y-m-d'),
            'status' => $status,
            'notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}
