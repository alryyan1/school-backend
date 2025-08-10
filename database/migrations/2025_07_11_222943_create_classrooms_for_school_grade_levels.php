<?php

use App\Models\Classroom;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
                $existingClassroom = DB::table('classrooms')
                    ->where('school_id', $assignment->school_id)
                    ->where('grade_level_id', $assignment->grade_level_id)
                    ->where('name', $className)
                    ->first();

                if (!$existingClassroom) {
                    DB::table('classrooms')->insert([
                        'name' => $className,
                        'school_id' => $assignment->school_id,
                        'grade_level_id' => $assignment->grade_level_id,
                        'capacity' => 30, // Default capacity
                        'teacher_id' => null, // Will be assigned later
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $createdCount++;
                }
            }
        }

        // Output the results
        echo "Created {$createdCount} classrooms for existing school-grade level assignments.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration creates data, so down() would remove all classrooms
        // Be careful with this in production!
        DB::table('classrooms')->truncate();
    }
};
