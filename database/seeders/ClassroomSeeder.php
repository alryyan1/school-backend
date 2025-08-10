<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\School;
use App\Models\GradeLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all school-grade level assignments
        $schoolGradeAssignments = DB::table('school_grade_levels')
            ->join('schools', 'school_grade_levels.school_id', '=', 'schools.id')
            ->join('grade_levels', 'school_grade_levels.grade_level_id', '=', 'grade_levels.id')
            ->select(
                'school_grade_levels.school_id',
                'school_grade_levels.grade_level_id',
                'schools.name as school_name',
                'grade_levels.name as grade_name',
                'grade_levels.code as grade_code'
            )
            ->get();

        $createdCount = 0;

        foreach ($schoolGradeAssignments as $assignment) {
            // Create two classrooms (A and B) for each school-grade combination
            $classroomNames = ['A', 'B'];
            
            foreach ($classroomNames as $className) {
                // Check if classroom already exists
                $existingClassroom = Classroom::where('school_id', $assignment->school_id)
                    ->where('grade_level_id', $assignment->grade_level_id)
                    ->where('name', $className)
                    ->first();

                if (!$existingClassroom) {
                    Classroom::create([
                        'name' => $className,
                        'school_id' => $assignment->school_id,
                        'grade_level_id' => $assignment->grade_level_id,
                        'capacity' => 30, // Default capacity
                        'teacher_id' => null, // Will be assigned later
                    ]);

                    $createdCount++;
                    
                    $this->command->info("Created classroom: {$assignment->school_name} - {$assignment->grade_name} ({$assignment->grade_code}) - Class {$className}");
                } else {
                    $this->command->info("Classroom already exists: {$assignment->school_name} - {$assignment->grade_name} ({$assignment->grade_code}) - Class {$className}");
                }
            }
        }

        $this->command->info("Classroom seeder completed. Created {$createdCount} new classrooms.");
    }
} 