<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeLevelSubjectSeederFromImage extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, let's add any missing subjects that are in the image but not in the existing seeder
        $this->addMissingSubjects();
        
        // Clear existing grade level subject assignments
        DB::table('grade_level_subjects_table')->delete();
        
        // Define the curriculum structure based on the image
        $curriculum = [
            // Middle School Stage (المرحلة المتوسطة)
            [
                'grade_level_id' => 1, // اول متوسط
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'لغة', 'code' => 'LANG'],
                    ['name' => 'تربيه', 'code' => 'EDU'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                    ['name' => 'تكنولوجيا', 'code' => 'TECH'],
                    ['name' => 'تربية تقنية', 'code' => 'TECH-ED'],
                ]
            ],
            [
                'grade_level_id' => 2, // ثاني متوسط
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'لغة', 'code' => 'LANG'],
                    ['name' => 'تربيه', 'code' => 'EDU'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                    ['name' => 'تكنولوجيا', 'code' => 'TECH'],
                    ['name' => 'تربية تقنية', 'code' => 'TECH-ED'],
                ]
            ],
            [
                'grade_level_id' => 3, // ثالث متوسط
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'لغة', 'code' => 'LANG'],
                    ['name' => 'تربيه', 'code' => 'EDU'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                    ['name' => 'تكنولوجيا', 'code' => 'TECH'],
                    ['name' => 'تربية فنية', 'code' => 'ART-ED'],
                ]
            ],
            
            // Primary School Stage (المرحلة الابتدائية) - Grades 1-3
            [
                'grade_level_id' => 4, // الصف الاول ابتدائي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'فنية', 'code' => 'ART'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                ]
            ],
            [
                'grade_level_id' => 5, // الصف الثاني ابتدائي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'فنية', 'code' => 'ART'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                ]
            ],
            [
                'grade_level_id' => 6, // الصف الثالث ابتدائي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'فنية', 'code' => 'ART'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                ]
            ],
            
            // Primary School Stage (المرحلة الابتدائية) - Grades 4-6
            [
                'grade_level_id' => 7, // الصف الرابع ابتدائي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                    ['name' => 'تكنولوجيا', 'code' => 'TECH'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                ]
            ],
            [
                'grade_level_id' => 8, // الصف الخامس ابتدائي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                    ['name' => 'تكنولوجيا', 'code' => 'TECH'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                ]
            ],
            [
                'grade_level_id' => 9, // الصف السادس ابتدائي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'علوم', 'code' => 'SCI'],
                    ['name' => 'تكنولوجيا', 'code' => 'TECH'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                ]
            ],
            
            // Secondary School Stage (المرحلة الثانوية)
            [
                'grade_level_id' => 10, // الصف الاول ثانوي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'دراسات اسلامية', 'code' => 'ISLM-ST'],
                    ['name' => 'هندسية', 'code' => 'ENGIN'],
                    ['name' => 'فيزياء - كيمياء - احياء', 'code' => 'SCI-COMB'],
                ]
            ],
            [
                'grade_level_id' => 11, // الصف الثاني ثانوي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات', 'code' => 'MATH'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'دراسات اسلامية', 'code' => 'ISLM-ST'],
                    ['name' => 'هندسية', 'code' => 'ENGIN'],
                    ['name' => 'فيزياء - كيمياء - احياء', 'code' => 'SCI-COMB'],
                ]
            ],
            [
                'grade_level_id' => 12, // الصف الثالث ادبي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات اساسية', 'code' => 'MATH-B'],
                    ['name' => 'تاريخ', 'code' => 'HIST'],
                    ['name' => 'جغرافيا', 'code' => 'GEO'],
                    ['name' => 'دراسات اسلامية', 'code' => 'ISLM-ST'],
                    ['name' => 'أدب انجليزي', 'code' => 'ENGL-LIT'],
                    ['name' => 'English', 'code' => 'ENGL-ADV'],
                ]
            ],
            [
                'grade_level_id' => 13, // الصف الثالث علمي
                'subjects' => [
                    ['name' => 'تربية اسلامية', 'code' => 'ISLM'],
                    ['name' => 'لغة عربية', 'code' => 'ARAB'],
                    ['name' => 'English', 'code' => 'ENGL'],
                    ['name' => 'رياضيات متخصصة', 'code' => 'MATH-A'],
                    ['name' => 'فيزياء', 'code' => 'PHYS'],
                    ['name' => 'كيمياء', 'code' => 'CHEM'],
                    ['name' => 'احياء', 'code' => 'BIO'],
                    ['name' => 'حاسوب', 'code' => 'COMP-SCI'],
                    ['name' => 'هندسية', 'code' => 'ENGIN'],
                    ['name' => 'فنون', 'code' => 'FINE-ART'],
                ]
            ],
        ];
        
        // Insert the grade level subject assignments
        foreach ($curriculum as $gradeData) {
            $gradeLevelId = $gradeData['grade_level_id'];
            
            foreach ($gradeData['subjects'] as $subjectData) {
                // Get the subject ID by name or code
                $subject = DB::table('subjects')
                    ->where('name', $subjectData['name'])
                    ->orWhere('code', $subjectData['code'])
                    ->first();
                
                if ($subject) {
                    DB::table('grade_level_subjects_table')->insert([
                        'grade_level_id' => $gradeLevelId,
                        'subject_id' => $subject->id,
                        'teacher_id' => null, // No teacher assigned initially
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Log missing subject
                    $this->command->warn("Subject not found: {$subjectData['name']} ({$subjectData['code']})");
                }
            }
        }
        
        $this->command->info('Grade level subjects seeded successfully!');
    }
    
    /**
     * Add missing subjects that are in the image but not in the existing seeder
     */
    private function addMissingSubjects(): void
    {
        $missingSubjects = [
            [
                'name' => 'لغة',
                'code' => 'LANG',
                'description' => 'لغة إضافية',
            ],
            [
                'name' => 'تربيه',
                'code' => 'EDU',
                'description' => 'تربية وتعليم',
            ],
            [
                'name' => 'فيزياء - كيمياء - احياء',
                'code' => 'SCI-COMB',
                'description' => 'علوم مجمعة (فيزياء - كيمياء - احياء)',
            ],
            [
                'name' => 'English',
                'code' => 'ENGL-ADV',
                'description' => 'لغة انجليزية متقدمة',
            ],
        ];
        
        foreach ($missingSubjects as $subject) {
            // Check if subject already exists
            $exists = DB::table('subjects')
                ->where('name', $subject['name'])
                ->orWhere('code', $subject['code'])
                ->exists();
            
            if (!$exists) {
                DB::table('subjects')->insert([
                    'id' => DB::table('subjects')->max('id') + 1,
                    'name' => $subject['name'],
                    'code' => $subject['code'],
                    'description' => $subject['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
