<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject; // Import the Subject model
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear the table first
        // DB::table('subjects')->delete();

        $subjects = [
            // Core Subjects
            ['name' => 'لغة عربية', 'code' => 'ARAB', 'description' => 'قواعد اللغة العربية وآدابها'],
            ['name' => 'لغة إنجليزية', 'code' => 'ENGL', 'description' => 'English Language Skills'],
            ['name' => 'رياضيات', 'code' => 'MATH', 'description' => 'الجبر والهندسة والحساب'],
            ['name' => 'علوم', 'code' => 'SCI', 'description' => 'مبادئ الفيزياء والكيمياء والأحياء'],
            ['name' => 'دراسات إسلامية', 'code' => 'ISLM', 'description' => 'قرآن، حديث، فقه، توحيد'],
            ['name' => 'دراسات اجتماعية', 'code' => 'SOCL', 'description' => 'تاريخ وجغرافيا وتربية وطنية'],
            // Specific Science Branches (for later grades)
            ['name' => 'فيزياء', 'code' => 'PHYS', 'description' => 'Physics'],
            ['name' => 'كيمياء', 'code' => 'CHEM', 'description' => 'Chemistry'],
            ['name' => 'أحياء', 'code' => 'BIOL', 'description' => 'Biology'],
            // Other Subjects
            ['name' => 'حاسب آلي', 'code' => 'COMP', 'description' => 'مهارات الحاسب والبرمجة'],
            ['name' => 'تربية فنية', 'code' => 'ART', 'description' => 'الرسم والأشغال اليدوية'],
            ['name' => 'تربية بدنية', 'code' => 'PE', 'description' => 'الأنشطة الرياضية واللياقة البدنية'],
        ];

        foreach ($subjects as $subjectData) {
            Subject::firstOrCreate(
                ['code' => $subjectData['code']], // Check based on unique code
                $subjectData
            );
        }
    }
}