<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClassroomsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('classrooms')->delete();
        
        \DB::table('classrooms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'A',
                'grade_level_id' => 4,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'B',
                'grade_level_id' => 4,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'A',
                'grade_level_id' => 5,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'B',
                'grade_level_id' => 5,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'A',
                'grade_level_id' => 6,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'B',
                'grade_level_id' => 6,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'A',
                'grade_level_id' => 7,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'B',
                'grade_level_id' => 7,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'A',
                'grade_level_id' => 8,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'B',
                'grade_level_id' => 8,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'A',
                'grade_level_id' => 9,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'B',
                'grade_level_id' => 9,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 2,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'A',
                'grade_level_id' => 4,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'B',
                'grade_level_id' => 4,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'A',
                'grade_level_id' => 5,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'B',
                'grade_level_id' => 5,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'A',
                'grade_level_id' => 6,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'B',
                'grade_level_id' => 6,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'A',
                'grade_level_id' => 7,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'B',
                'grade_level_id' => 7,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'A',
                'grade_level_id' => 8,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'B',
                'grade_level_id' => 8,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'A',
                'grade_level_id' => 9,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'B',
                'grade_level_id' => 9,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 3,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'A',
                'grade_level_id' => 1,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 4,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'B',
                'grade_level_id' => 1,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 4,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'A',
                'grade_level_id' => 2,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 4,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'B',
                'grade_level_id' => 2,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 4,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'A',
                'grade_level_id' => 3,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 4,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'B',
                'grade_level_id' => 3,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 4,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'A',
                'grade_level_id' => 1,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 5,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'B',
                'grade_level_id' => 1,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 5,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'A',
                'grade_level_id' => 2,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 5,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'B',
                'grade_level_id' => 2,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 5,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'A',
                'grade_level_id' => 3,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 5,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'B',
                'grade_level_id' => 3,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 5,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'A',
                'grade_level_id' => 10,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'B',
                'grade_level_id' => 10,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'A',
                'grade_level_id' => 11,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'B',
                'grade_level_id' => 11,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'A',
                'grade_level_id' => 12,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'B',
                'grade_level_id' => 12,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'A',
                'grade_level_id' => 13,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'B',
                'grade_level_id' => 13,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 6,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'A',
                'grade_level_id' => 10,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'B',
                'grade_level_id' => 10,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'A',
                'grade_level_id' => 11,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'B',
                'grade_level_id' => 11,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'A',
                'grade_level_id' => 12,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'B',
                'grade_level_id' => 12,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:15',
                'updated_at' => '2025-07-11 22:32:15',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'A',
                'grade_level_id' => 13,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:16',
                'updated_at' => '2025-07-11 22:32:16',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'B',
                'grade_level_id' => 13,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-11 22:32:16',
                'updated_at' => '2025-07-11 22:32:16',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'C',
                'grade_level_id' => 10,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-17 08:56:00',
                'updated_at' => '2025-07-17 08:56:00',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'C',
                'grade_level_id' => 11,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 7,
                'created_at' => '2025-07-17 08:56:16',
                'updated_at' => '2025-07-17 08:56:16',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'فصل براعم',
                'grade_level_id' => 14,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-17 09:20:13',
                'updated_at' => '2025-07-17 09:20:13',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'فصل الروضه مستوي اول',
                'grade_level_id' => 15,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-17 09:20:36',
                'updated_at' => '2025-07-17 09:20:36',
            ),
            56 => 
            array (
                'id' => 58,
                'name' => 'فصل الروضه مستوي ثاني',
                'grade_level_id' => 16,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-17 09:51:37',
                'updated_at' => '2025-07-17 09:51:37',
            ),
            57 => 
            array (
                'id' => 59,
                'name' => 'A',
                'grade_level_id' => 14,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-19 17:09:33',
                'updated_at' => '2025-07-19 17:09:33',
            ),
            58 => 
            array (
                'id' => 60,
                'name' => 'B',
                'grade_level_id' => 14,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-19 17:09:33',
                'updated_at' => '2025-07-19 17:09:33',
            ),
            59 => 
            array (
                'id' => 61,
                'name' => 'A',
                'grade_level_id' => 15,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-19 17:09:33',
                'updated_at' => '2025-07-19 17:09:33',
            ),
            60 => 
            array (
                'id' => 62,
                'name' => 'B',
                'grade_level_id' => 15,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-19 17:09:33',
                'updated_at' => '2025-07-19 17:09:33',
            ),
            61 => 
            array (
                'id' => 63,
                'name' => 'A',
                'grade_level_id' => 16,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-19 17:09:33',
                'updated_at' => '2025-07-19 17:09:33',
            ),
            62 => 
            array (
                'id' => 64,
                'name' => 'B',
                'grade_level_id' => 16,
                'teacher_id' => NULL,
                'capacity' => 30,
                'school_id' => 1,
                'created_at' => '2025-07-19 17:09:33',
                'updated_at' => '2025-07-19 17:09:33',
            ),
        ));
        
        
    }
}