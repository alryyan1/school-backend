<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpenseCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'صيانة',
            'أثاث',
            'مواد تعليمية',
            'مستلزمات مكتبية',
            'نظافة',
            'أجهزة إلكترونية',
            'أمان',
            'طباعة',
            'إنترنت',
            'تكييف',
            'إضاءة',
            'مياه',
            'صرف صحي',
            'تدفئة',
            'مطبخ',
            'حديقة',
            'نقل',
            'اتصالات',
            'برمجيات',
            'تدريب',
        ];

        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
            '#F8C471', '#82E0AA', '#F1948A', '#85C1E9', '#D7BDE2',
            '#A9DFBF', '#F9E79F', '#D5DBDB', '#AED6F1', '#A3E4D7'
        ];

        return [
            'name' => $this->faker->randomElement($categories),
            'name_en' => $this->faker->optional(0.3)->words(2, true),
            'description' => $this->faker->optional(0.6)->sentence(8),
            'color' => $this->faker->randomElement($colors),
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
