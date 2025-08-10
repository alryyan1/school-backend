<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have schools before creating students
        if (School::count() === 0) {
            $this->command->warn('No schools found. Please run SchoolSeeder first.');
            return;
        }

        // Create a mix of approved and pending students
        $totalStudents = 50;
        $approvedCount = (int) ($totalStudents * 0.7); // 70% approved
        $pendingCount = $totalStudents - $approvedCount;

        // Create approved students
        Student::factory($approvedCount)
            ->approved()
            ->create();

        // Create pending students
        Student::factory($pendingCount)
            ->pending()
            ->create();

        $this->command->info("Created {$totalStudents} students ({$approvedCount} approved, {$pendingCount} pending)");
    }
}
