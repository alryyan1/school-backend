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
            // Create the admin user
            // UserSeeder::class, // Or direct creation as before
            // Create base entities
            SchoolsTableSeeder::class,
            AcademicYearsTableSeeder::class,
            SubjectSeeder::class,
            GradeLevelSeeder::class,
            // --- Linking/Pivot Table Data (Run after base entities) ---
            SchoolGradeLevelSeeder::class, // <-- Assigns Grades to Schools with fees
            ClassroomSeeder::class, // <-- Creates classrooms for each school-grade assignment
            RolesAndPermissionsSeeder::class,
            // --- Other Seeders that use factories and might need the above data ---
            StudentSeeder::class,
            // TeacherSeeder::class,
            // AcademicYearSeeder::class,
            // ExamSeeder::class,
        ]);
       
        $this->call(SchoolsTableSeeder::class);
        $this->call(GradeLevelsTableSeeder::class);
        $this->call(StudentsTableSeeder::class);
        $this->call(TeachersTableSeeder::class);
        $this->call(SubjectsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(ClassroomsTableSeeder::class);
        $this->call(SubjectTeacherTableSeeder::class);
        $this->call(TimetablesTableSeeder::class);
        $this->call(ExamsTableSeeder::class);
        $this->call(AcademicYearsTableSeeder::class);
        $this->call(StudentAcademicYearsTableSeeder::class);
        $this->call(AcademicYearFeesTableSeeder::class);
        $this->call(AcademicYearSubjectsTableSeeder::class);
        $this->call(ExamSchedulesTableSeeder::class);
        $this->call(StudentFeePaymentsTableSeeder::class);
        $this->call(ExamResultsTableSeeder::class);
        $this->call(SchoolGradeLevelsTableSeeder::class);
        $this->call(TransportRoutesTableSeeder::class);
        $this->call(StudentTransportAssignmentsTableSeeder::class);
        $this->call(FeeInstallmentsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(ModelHasPermissionsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(StudentNotesTableSeeder::class);
        $this->call(StudentAbsencesTableSeeder::class);
        $this->call(StudentWarningsTableSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);
    }
}