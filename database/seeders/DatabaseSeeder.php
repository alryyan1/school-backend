<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AcademicYear;
use App\Models\Student;
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
        $this->call(RolesAndPermissionsSeeder::class);

        // $this->call(SchoolSeeder::class);
        // $this->call(GradeLevelSeeder::class);
        // $this->call(SubjectSeeder::class);
        // $this->call(StudentSeeder::class);
        // $this->call(TeacherSeeder::class);

        //create admin user

        User::create([
            'username' => 'admin',
            'password' => bcrypt('12345678'),
            'email' => 'admin@gmail.com',
            'name' => 'admin',
            'role'=>'admin'

        ]);
        // Student::truncate();
        // \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Create 10 fake students using the factory
        Student::factory()->count(10)->create();
        // AcademicYear::create([
        //     'name'=>'2025',
        //     'school_id'=>1,
        //     'start_date'=>Carbon::now(),
        //     'end_date'=>Carbon::now()->addMonths(12),
            
        // ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
