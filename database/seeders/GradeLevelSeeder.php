<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GradeLevel; // Import the GradeLevel model
use Illuminate\Support\Facades\DB;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear the table first
        // DB::table('grade_levels')->delete();

        $gradeLevels = [
            // رياض الأطفال
            ['name' => 'روضة أولى', 'code' => 'KG1', 'description' => 'المستوى الأول في رياض الأطفال'],
            ['name' => 'روضة ثانية (تمهيدي)', 'code' => 'KG2', 'description' => 'المستوى الثاني في رياض الأطفال (التمهيدي)'],
            // المرحلة الابتدائية
            ['name' => 'الصف الأول الابتدائي', 'code' => 'G1', 'description' => null],
            ['name' => 'الصف الثاني الابتدائي', 'code' => 'G2', 'description' => null],
            ['name' => 'الصف الثالث الابتدائي', 'code' => 'G3', 'description' => null],
            ['name' => 'الصف الرابع الابتدائي', 'code' => 'G4', 'description' => null],
            ['name' => 'الصف الخامس الابتدائي', 'code' => 'G5', 'description' => null],
            ['name' => 'الصف السادس الابتدائي', 'code' => 'G6', 'description' => null],
            // المرحلة المتوسطة
            ['name' => 'الصف الأول المتوسط', 'code' => 'G7', 'description' => null],
            ['name' => 'الصف الثاني المتوسط', 'code' => 'G8', 'description' => null],
            ['name' => 'الصف الثالث المتوسط', 'code' => 'G9', 'description' => null],
            // المرحلة الثانوية
            ['name' => 'الصف الأول الثانوي', 'code' => 'G10', 'description' => null],
            ['name' => 'الصف الثاني الثانوي', 'code' => 'G11', 'description' => null],
            ['name' => 'الصف الثالث الثانوي', 'code' => 'G12', 'description' => null],
        ];

        foreach ($gradeLevels as $gradeData) {
            GradeLevel::firstOrCreate(
                ['code' => $gradeData['code']], // Check based on unique code
                $gradeData
            );
        }
    }
}