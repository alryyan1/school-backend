<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\SubjectSeeder;
use Database\Seeders\GradeLevelSeeder;
use Database\Seeders\SchoolSeeder;
use Database\Seeders\SchoolGradeLevelSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClassroomSeeder;
use Database\Seeders\StudentSeeder;

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
            // Create base entities
            SchoolsTableSeeder::class,
            SubjectSeeder::class,
            GradeLevelSeeder::class,
            PaymentMethodsTableSeeder::class,
            
            // --- Linking/Pivot Table Data (Run after base entities) ---
            SchoolGradeLevelSeeder::class, // <-- Assigns Grades to Schools with fees
            ClassroomSeeder::class, // <-- Creates classrooms for each school-grade assignment
            RolesAndPermissionsSeeder::class, // <-- Create roles and permissions first
            
            // Create the admin user (after roles exist)
            UserSeeder::class, // Create superadmin and admin users
            
            // --- Student and Related Data ---
            StudentSeeder::class,
            EnrollmentsTableSeeder::class,
            FeeInstallmentsTableSeeder::class,
            StudentFeePaymentsTableSeeder::class,
            StudentWarningsTableSeeder::class,
            StudentAbsencesTableSeeder::class,
            StudentNotesTableSeeder::class,
            
            // --- Legacy seeders (commented out) ---
            // SchoolGradeLevelsTableSeeder::class, // Legacy table seeder with hard-coded IDs (do not run)
            // TeacherSeeder::class,
            // ExamSeeder::class,
        ]);
    }
}