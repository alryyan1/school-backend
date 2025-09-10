<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'رواتب الموظفين',
                'name_en' => 'Employee Salaries',
                'description' => 'رواتب وأجور الموظفين والعاملين',
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'صيانة وتشغيل',
                'name_en' => 'Maintenance & Operations',
                'description' => 'صيانة المباني والمعدات والتشغيل',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'أدوات ومستلزمات',
                'name_en' => 'Supplies & Materials',
                'description' => 'أدوات الكتابة والمستلزمات التعليمية',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'كهرباء ومياه',
                'name_en' => 'Utilities',
                'description' => 'فواتير الكهرباء والمياه والغاز',
                'color' => '#EF4444',
                'is_active' => true,
            ],
            [
                'name' => 'نقل ومواصلات',
                'name_en' => 'Transportation',
                'description' => 'تكاليف النقل والمواصلات',
                'color' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'تكنولوجيا',
                'name_en' => 'Technology',
                'description' => 'أجهزة الحاسوب والبرمجيات والإنترنت',
                'color' => '#06B6D4',
                'is_active' => true,
            ],
            [
                'name' => 'تأمين',
                'name_en' => 'Insurance',
                'description' => 'أقساط التأمين على المباني والمعدات',
                'color' => '#84CC16',
                'is_active' => true,
            ],
            [
                'name' => 'تدريب وتطوير',
                'name_en' => 'Training & Development',
                'description' => 'برامج التدريب والتطوير المهني',
                'color' => '#F97316',
                'is_active' => true,
            ],
            [
                'name' => 'أخرى',
                'name_en' => 'Others',
                'description' => 'مصروفات أخرى متنوعة',
                'color' => '#6B7280',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}
