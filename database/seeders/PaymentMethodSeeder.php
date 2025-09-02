<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'cash',
                'display_name' => 'نقداً',
                'description' => 'دفع نقدي مباشر',
                'is_active' => true,
                'settings' => null,
            ],
            [
                'name' => 'bank_transfer',
                'display_name' => 'تحويل بنكي',
                'description' => 'تحويل من حساب بنكي إلى حساب المدرسة',
                'is_active' => true,
                'settings' => [
                    'bank_name' => 'البنك الأهلي المصري',
                    'account_number' => '1234567890',
                    'swift_code' => 'NBEGEGCX',
                ],
            ],
            [
                'name' => 'check',
                'display_name' => 'شيك',
                'description' => 'دفع بشيك مصرفي',
                'is_active' => true,
                'settings' => [
                    'check_validity_days' => 90,
                ],
            ],
            [
                'name' => 'credit_card',
                'display_name' => 'بطاقة ائتمان',
                'description' => 'دفع ببطاقة ائتمان أو مدى',
                'is_active' => true,
                'settings' => [
                    'supported_cards' => ['visa', 'mastercard', 'mada'],
                ],
            ],
            [
                'name' => 'online',
                'display_name' => 'دفع إلكتروني',
                'description' => 'دفع عبر الإنترنت أو التطبيق',
                'is_active' => true,
                'settings' => [
                    'gateway' => 'stripe',
                    'supported_currencies' => ['EGP', 'USD'],
                ],
            ],
            [
                'name' => 'installment',
                'display_name' => 'أقساط',
                'description' => 'دفع على أقساط شهرية',
                'is_active' => true,
                'settings' => [
                    'max_installments' => 12,
                    'interest_rate' => 0,
                ],
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method
            );
        }
    }
}
