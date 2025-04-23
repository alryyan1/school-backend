<?php // database/factories/StudentFeePaymentFactory.php
namespace Database\Factories;

use App\Models\StudentFeePayment;
use App\Models\FeeInstallment; // Use FeeInstallment
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFeePaymentFactory extends Factory
{
    protected $model = StudentFeePayment::class;
    public function definition(): array
    {
        // Get an installment that is not fully paid
        $installment = FeeInstallment::where('status', '!=', 'paid')->inRandomOrder()->first() ?? FeeInstallment::factory()->create();
        // Pay a portion or the remaining amount
        $remaining = $installment->amount_due - $installment->amount_paid;
        $paymentAmount = $this->faker->randomFloat(2, 10, $remaining);
        return [
            'fee_installment_id' => $installment->id, // <-- Link to installment
            'amount' => $paymentAmount,
            'payment_date' => $this->faker->dateTimeBetween($installment->due_date, '+1 month')->format('Y-m-d'),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
