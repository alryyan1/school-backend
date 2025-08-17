<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolGradeLevelsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('school_grade_levels')->delete();
        
        \DB::table('school_grade_levels')->insert(array (
            0 => 
            array (
                'id' => 14,
                'school_id' => 4,
                'grade_level_id' => 1,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:57:37',
                'updated_at' => '2025-07-11 21:57:37',
            ),
            1 => 
            array (
                'id' => 15,
                'school_id' => 4,
                'grade_level_id' => 2,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:57:46',
                'updated_at' => '2025-07-11 21:57:46',
            ),
            2 => 
            array (
                'id' => 16,
                'school_id' => 4,
                'grade_level_id' => 3,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:57:57',
                'updated_at' => '2025-07-11 21:57:57',
            ),
            3 => 
            array (
                'id' => 17,
                'school_id' => 5,
                'grade_level_id' => 1,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:58:20',
                'updated_at' => '2025-07-11 21:58:20',
            ),
            4 => 
            array (
                'id' => 18,
                'school_id' => 5,
                'grade_level_id' => 2,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:58:24',
                'updated_at' => '2025-07-11 21:58:24',
            ),
            5 => 
            array (
                'id' => 19,
                'school_id' => 5,
                'grade_level_id' => 3,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:58:29',
                'updated_at' => '2025-07-11 21:58:29',
            ),
            6 => 
            array (
                'id' => 20,
                'school_id' => 7,
                'grade_level_id' => 10,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:58:52',
                'updated_at' => '2025-07-11 21:58:52',
            ),
            7 => 
            array (
                'id' => 21,
                'school_id' => 7,
                'grade_level_id' => 11,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:58:56',
                'updated_at' => '2025-07-11 21:58:56',
            ),
            8 => 
            array (
                'id' => 22,
                'school_id' => 7,
                'grade_level_id' => 12,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:59:03',
                'updated_at' => '2025-07-11 21:59:03',
            ),
            9 => 
            array (
                'id' => 23,
                'school_id' => 7,
                'grade_level_id' => 13,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:59:07',
                'updated_at' => '2025-07-11 21:59:07',
            ),
            10 => 
            array (
                'id' => 24,
                'school_id' => 6,
                'grade_level_id' => 10,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:59:14',
                'updated_at' => '2025-07-11 21:59:14',
            ),
            11 => 
            array (
                'id' => 25,
                'school_id' => 6,
                'grade_level_id' => 11,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:59:19',
                'updated_at' => '2025-07-11 21:59:19',
            ),
            12 => 
            array (
                'id' => 26,
                'school_id' => 6,
                'grade_level_id' => 12,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:59:24',
                'updated_at' => '2025-07-11 21:59:24',
            ),
            13 => 
            array (
                'id' => 27,
                'school_id' => 6,
                'grade_level_id' => 13,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 21:59:28',
                'updated_at' => '2025-07-11 21:59:28',
            ),
            14 => 
            array (
                'id' => 28,
                'school_id' => 3,
                'grade_level_id' => 4,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:03:30',
                'updated_at' => '2025-07-11 22:03:30',
            ),
            15 => 
            array (
                'id' => 29,
                'school_id' => 3,
                'grade_level_id' => 5,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:03:35',
                'updated_at' => '2025-07-11 22:03:35',
            ),
            16 => 
            array (
                'id' => 30,
                'school_id' => 3,
                'grade_level_id' => 6,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:03:40',
                'updated_at' => '2025-07-11 22:03:40',
            ),
            17 => 
            array (
                'id' => 31,
                'school_id' => 3,
                'grade_level_id' => 7,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:03:43',
                'updated_at' => '2025-07-11 22:03:43',
            ),
            18 => 
            array (
                'id' => 32,
                'school_id' => 3,
                'grade_level_id' => 8,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:03:47',
                'updated_at' => '2025-07-11 22:03:47',
            ),
            19 => 
            array (
                'id' => 33,
                'school_id' => 3,
                'grade_level_id' => 9,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:03:52',
                'updated_at' => '2025-07-11 22:03:52',
            ),
            20 => 
            array (
                'id' => 34,
                'school_id' => 2,
                'grade_level_id' => 4,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:04:06',
                'updated_at' => '2025-07-11 22:04:06',
            ),
            21 => 
            array (
                'id' => 35,
                'school_id' => 2,
                'grade_level_id' => 5,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:04:09',
                'updated_at' => '2025-07-11 22:04:09',
            ),
            22 => 
            array (
                'id' => 36,
                'school_id' => 2,
                'grade_level_id' => 6,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:04:13',
                'updated_at' => '2025-07-11 22:04:13',
            ),
            23 => 
            array (
                'id' => 37,
                'school_id' => 2,
                'grade_level_id' => 7,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:04:17',
                'updated_at' => '2025-07-11 22:04:17',
            ),
            24 => 
            array (
                'id' => 38,
                'school_id' => 2,
                'grade_level_id' => 8,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:04:21',
                'updated_at' => '2025-07-11 22:04:21',
            ),
            25 => 
            array (
                'id' => 39,
                'school_id' => 2,
                'grade_level_id' => 9,
                'basic_fees' => 0,
                'created_at' => '2025-07-11 22:04:25',
                'updated_at' => '2025-07-11 22:04:25',
            ),
            26 => 
            array (
                'id' => 40,
                'school_id' => 1,
                'grade_level_id' => 14,
                'basic_fees' => 0,
                'created_at' => '2025-07-17 09:00:39',
                'updated_at' => '2025-07-17 09:00:39',
            ),
            27 => 
            array (
                'id' => 41,
                'school_id' => 1,
                'grade_level_id' => 15,
                'basic_fees' => 0,
                'created_at' => '2025-07-17 09:00:49',
                'updated_at' => '2025-07-17 09:00:49',
            ),
            28 => 
            array (
                'id' => 42,
                'school_id' => 1,
                'grade_level_id' => 16,
                'basic_fees' => 0,
                'created_at' => '2025-07-17 09:00:54',
                'updated_at' => '2025-07-17 09:00:54',
            ),
        ));
        
        
    }
}