<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\TeacherFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(SchoolSeeder::class);
        $this->call(GradeLevelSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(TeacherSeeder::class);

        //create admin user

        User::create([
            'username' => 'roony',
            'password' => bcrypt('alryyan1'),
            'email' => 'alryyan.dev@gmail.com',
            'name' => 'alryyan',
            'role'=>'admin'

        ]);

        AcademicYear::create([
            'name'=>'2025',
            'school_id'=>1,
            'start_date'=>Carbon::now(),
            'end_date'=>Carbon::now()->addMonths(12),
            
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
