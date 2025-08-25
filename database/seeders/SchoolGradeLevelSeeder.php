<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\GradeLevel;

class SchoolGradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure base data exists
        if (School::count() === 0 || GradeLevel::count() === 0) {
            $this->command->warn('Schools or GradeLevels not found. Run SchoolsTableSeeder and GradeLevelSeeder first.');
            return;
        }

        // Attach all grade levels to all schools if not already attached
        $schools = School::all(['id', 'name']);
        $gradeLevels = GradeLevel::all(['id', 'name']);

        $created = 0;
        foreach ($schools as $school) {
            foreach ($gradeLevels as $grade) {
                $exists = DB::table('school_grade_levels')
                    ->where('school_id', $school->id)
                    ->where('grade_level_id', $grade->id)
                    ->exists();

                if (!$exists) {
                    DB::table('school_grade_levels')->insert([
                        'school_id' => $school->id,
                        'grade_level_id' => $grade->id,
                        'basic_fees' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $created++;
                }
            }
        }

        $this->command->info("Assigned grade levels to schools. New assignments: {$created}");
    }
}



