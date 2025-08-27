<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EnrollmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('enrollments')->delete();
        
        \DB::table('enrollments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'student_id' => 1,
                'school_id' => 1,
                'academic_year' => '2024/2025',
                'grade_level_id' => 1,
                'classroom_id' => 1,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 50000,
                'discount' => 0,
                'created_at' => '2025-01-27 10:00:00',
                'updated_at' => '2025-01-27 10:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'student_id' => 2,
                'school_id' => 1,
                'academic_year' => '2024/2025',
                'grade_level_id' => 1,
                'classroom_id' => 1,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 50000,
                'discount' => 5000,
                'created_at' => '2025-01-27 10:00:00',
                'updated_at' => '2025-01-27 10:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'student_id' => 3,
                'school_id' => 1,
                'academic_year' => '2024/2025',
                'grade_level_id' => 2,
                'classroom_id' => 2,
                'status' => 'active',
                'enrollment_type' => 'scholarship',
                'fees' => 50000,
                'discount' => 25000,
                'created_at' => '2025-01-27 10:00:00',
                'updated_at' => '2025-01-27 10:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'student_id' => 4,
                'school_id' => 1,
                'academic_year' => '2024/2025',
                'grade_level_id' => 2,
                'classroom_id' => 2,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 50000,
                'discount' => 0,
                'created_at' => '2025-01-27 10:00:00',
                'updated_at' => '2025-01-27 10:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'student_id' => 5,
                'school_id' => 1,
                'academic_year' => '2024/2025',
                'grade_level_id' => 3,
                'classroom_id' => 3,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 50000,
                'discount' => 0,
                'created_at' => '2025-01-27 10:00:00',
                'updated_at' => '2025-01-27 10:00:00',
            ),
        ));
        
        
    }
}
