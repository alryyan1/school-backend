<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class SingleStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single student that is NOT approved
        Student::factory()->create([
            'approved' => false,
            'approved_by_user' => null,
            'aproove_date' => null,
        ]);
    }
}
