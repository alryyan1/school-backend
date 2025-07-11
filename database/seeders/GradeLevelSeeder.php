<?php
// database/seeders/GradeLevelSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GradeLevel; // Import the GradeLevel model

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gradeLevels = [
            // المتوسط
            ['name' => 'اول متوسط', 'code' => 'G7-MID'],
            ['name' => 'ثاني متوسط', 'code' => 'G8-MID'],
            ['name' => 'ثالث متوسط', 'code' => 'G9-MID'],
            // الإبتدائية
            ['name' => 'الصف الاول ابتدائي', 'code' => 'G1-ELEM'],
            ['name' => 'الصف الثاني ابتدائي', 'code' => 'G2-ELEM'],
            ['name' => 'الصف الثالث ابتدائي', 'code' => 'G3-ELEM'],
            ['name' => 'الصف الرابع ابتدائي', 'code' => 'G4-ELEM'],
            ['name' => 'الصف الخامس ابتدائي', 'code' => 'G5-ELEM'],
            ['name' => 'الصف السادس ابتدائي', 'code' => 'G6-ELEM'],
            // الثانوية
            ['name' => 'الصف الاول ثانوي', 'code' => 'G10-SEC'],
            ['name' => 'الصف الثاني ثانوي', 'code' => 'G11-SEC'],
            ['name' => 'الصف الثالث ادبي', 'code' => 'G12-LIT'], // Literary track
            ['name' => 'الصف الثالث علمي', 'code' => 'G12-SCI'], // Scientific track
        ];

        foreach ($gradeLevels as $gradeData) {
            GradeLevel::firstOrCreate(
                ['code' => $gradeData['code']],
                ['name' => $gradeData['name']]
            );
        }
    }
}