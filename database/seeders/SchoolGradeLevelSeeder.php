<?php
// database/seeders/SchoolGradeLevelSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\GradeLevel;

class SchoolGradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the school you want to assign grades to.
        // Let's assume you have a school with ID 1.
        $school = School::find(1);

        if (!$school) {
            $this->command->error('School with ID 1 not found. Please run SchoolSeeder first.');
            return;
        }

        // Get all grade level IDs
        $gradeLevelIds = GradeLevel::pluck('id');

        // Prepare data for pivot table, including default basic_fees
        $pivotData = [];
        foreach ($gradeLevelIds as $gradeId) {
            // Assign a default fee for each grade level, you can customize this
            $pivotData[$gradeId] = ['basic_fees' => $this->getFeeForGrade($gradeId)];
        }

        // Sync the relationships.
        // This will attach all grade levels to the school.
        // It's idempotent: running it again won't create duplicates.
        $school->gradeLevels()->sync($pivotData);

        $this->command->info("Assigned " . count($gradeLevelIds) . " grade levels to '{$school->name}'.");

        // Optional: Assign grades to a second school if it exists
        // $school2 = School::find(2);
        // if ($school2) {
        //     $school2->gradeLevels()->sync($pivotData);
        //     $this->command->info("Assigned " . count($gradeLevelIds) . " grade levels to '{$school2->name}'.");
        // }
    }

    /**
     * Helper function to get a default fee based on grade level (example logic).
     * @param int $gradeLevelId
     * @return int
     */
    private function getFeeForGrade(int $gradeLevelId): int
    {
        // Simple example: fees increase with grade ID
        if ($gradeLevelId <= 6) { // Elementary
            return 5000;
        } elseif ($gradeLevelId <= 9) { // Middle School
            return 7000;
        } else { // High School
            return 9000;
        }
    }
}