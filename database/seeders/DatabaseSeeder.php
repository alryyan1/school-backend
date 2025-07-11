<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\SubjectSeeder;
use Database\Seeders\GradeLevelSeeder;
use Database\Seeders\SchoolSeeder;
use Database\Seeders\SchoolGradeLevelSeeder;
use Database\Seeders\UserSeeder;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // --- Foundational Data (Run first) ---
            // Create the admin user
            // UserSeeder::class, // Or direct creation as before
            // Create base entities
            SchoolsTableSeeder::class,
            AcademicYearsTableSeeder::class,
            SubjectSeeder::class,
            GradeLevelSeeder::class,
            // --- Linking/Pivot Table Data (Run after base entities) ---
            SchoolGradeLevelSeeder::class, // <-- Assigns Grades to Schools with fees
            RolesAndPermissionsSeeder::class,
            // --- Other Seeders that use factories and might need the above data ---
            // TeacherSeeder::class,
            // AcademicYearSeeder::class,
            // ClassroomSeeder::class,
            // ExamSeeder::class,
            // StudentSeeder::class,
        ]);
       
    }
}