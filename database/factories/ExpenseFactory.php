<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expenseTitles = [
            'شراء أثاث للمكتب',
            'صيانة أجهزة الحاسوب',
            'شراء مواد تعليمية',
            'صيانة المبنى',
            'شراء مستلزمات النظافة',
            'صيانة نظام التكييف',
            'شراء أدوات مكتبية',
            'صيانة شبكة الإنترنت',
            'شراء مواد التنظيف',
            'صيانة الأبواب والنوافذ',
            'شراء أجهزة إلكترونية',
            'صيانة نظام الإنذار',
            'شراء مواد الطباعة',
            'صيانة المصاعد',
            'شراء مستلزمات المطبخ',
            'صيانة نظام الإضاءة',
            'شراء مواد الأمان',
            'صيانة نظام الصرف',
            'شراء أدوات الصيانة',
            'صيانة نظام التدفئة',
            'شراء مواد التجميل',
            'صيانة نظام المراقبة',
            'شراء مستلزمات الحديقة',
            'صيانة نظام المياه',
            'شراء مواد التخزين',
        ];


        return [
            'title' => $this->faker->randomElement($expenseTitles),
            'description' => $this->faker->optional(0.7)->sentence(10),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'expense_category_id' => ExpenseCategory::factory(),
            'created_by' => User::factory(),
            'expense_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }


    /**
     * Indicate that the expense is for a specific category.
     */
    public function forCategory(ExpenseCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'expense_category_id' => $category->id,
        ]);
    }


    /**
     * Indicate that the expense is created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
