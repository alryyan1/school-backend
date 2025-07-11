<?php
// database/seeders/SubjectSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject; // Import the Subject model

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
            ['name' => 'لغة عربية', 'code' => 'ARAB'],
            ['name' => 'English', 'code' => 'ENGL'],
            ['name' => 'رياضيات', 'code' => 'MATH'],
            ['name' => 'علوم', 'code' => 'SCI'],
            ['name' => 'جغرافيا', 'code' => 'GEO'],
            ['name' => 'تاريخ', 'code' => 'HIST'],
            ['name' => 'تكنولوجيا', 'code' => 'TECH'],
            ['name' => 'تربية تقنية', 'code' => 'TECH-ED'],
            ['name' => 'تربية فنية', 'code' => 'ART-ED'],
            ['name' => 'فنية', 'code' => 'ART'], // From elementary
            ['name' => 'دراسات اسلامية', 'code' => 'ISLM-ST'], // Secondary school version
            ['name' => 'هندسية', 'code' => 'ENGIN'],
            ['name' => 'فيزياء', 'code' => 'PHYS'],
            ['name' => 'كيمياء', 'code' => 'CHEM'],
            ['name' => 'احياء', 'code' => 'BIO'],
            ['name' => 'أدب انجليزي', 'code' => 'ENGL-LIT'],
            ['name' => 'حاسوب', 'code' => 'COMP-SCI'],
            ['name' => 'فنون', 'code' => 'FINE-ART'],
            ['name' => 'رياضيات اساسية', 'code' => 'MATH-B'],
            ['name' => 'رياضيات متخصصة', 'code' => 'MATH-A'],
        ];

        foreach ($subjects as $subjectData) {
            // Use firstOrCreate to avoid creating duplicates if seeder is run again
            Subject::firstOrCreate(
                ['code' => $subjectData['code']], // Check based on unique code
                ['name' => $subjectData['name']]  // Data to insert if it doesn't exist
            );
        }
    }
}