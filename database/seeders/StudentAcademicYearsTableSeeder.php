<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentAcademicYearsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('student_academic_years')->delete();
        
        \DB::table('student_academic_years')->insert(array (
            0 => 
            array (
                'id' => 1,
                'student_id' => 1,
                'school_id' => 6,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'classroom_id' => 37,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 0,
                'discount' => 0,
                'created_at' => '2025-07-17 16:41:55',
                'updated_at' => '2025-07-17 16:43:02',
            ),
            1 => 
            array (
                'id' => 2,
                'student_id' => 1,
                'school_id' => 3,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'classroom_id' => 17,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 0,
                'discount' => 0,
                'created_at' => '2025-07-19 14:04:51',
                'updated_at' => '2025-07-20 19:09:10',
            ),
            2 => 
            array (
                'id' => 3,
                'student_id' => 63,
                'school_id' => 6,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'classroom_id' => NULL,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 500000,
                'discount' => 0,
                'created_at' => '2025-07-22 19:18:58',
                'updated_at' => '2025-07-22 19:31:35',
            ),
            3 => 
            array (
                'id' => 4,
                'student_id' => 52,
                'school_id' => 1,
                'academic_year_id' => 7,
                'grade_level_id' => 14,
                'classroom_id' => NULL,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 0,
                'discount' => 0,
                'created_at' => '2025-08-04 17:40:50',
                'updated_at' => '2025-08-04 17:40:50',
            ),
            4 => 
            array (
                'id' => 5,
                'student_id' => 64,
                'school_id' => 3,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'classroom_id' => NULL,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 500000,
                'discount' => 0,
                'created_at' => '2025-08-04 18:06:03',
                'updated_at' => '2025-08-13 09:09:05',
            ),
            5 => 
            array (
                'id' => 6,
                'student_id' => 48,
                'school_id' => 3,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'classroom_id' => NULL,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 0,
                'discount' => 0,
                'created_at' => '2025-08-04 18:21:25',
                'updated_at' => '2025-08-04 18:21:25',
            ),
            6 => 
            array (
                'id' => 7,
                'student_id' => 43,
                'school_id' => 2,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'classroom_id' => NULL,
                'status' => 'active',
                'enrollment_type' => 'regular',
                'fees' => 250000,
                'discount' => 0,
                'created_at' => '2025-08-11 08:54:59',
                'updated_at' => '2025-08-11 08:54:59',
            ),
        ));
        
        
    }
}