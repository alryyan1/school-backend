<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AcademicYearSubjectsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('academic_year_subjects')->delete();
        
        \DB::table('academic_year_subjects')->insert(array (
            0 => 
            array (
                'id' => 1,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 10:30:03',
                'updated_at' => '2025-07-17 10:30:03',
            ),
            1 => 
            array (
                'id' => 2,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 10:30:18',
                'updated_at' => '2025-07-17 10:30:18',
            ),
            2 => 
            array (
                'id' => 3,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 10:30:39',
                'updated_at' => '2025-07-17 10:30:39',
            ),
            3 => 
            array (
                'id' => 4,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 10:30:51',
                'updated_at' => '2025-07-17 10:30:51',
            ),
            4 => 
            array (
                'id' => 5,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'subject_id' => 11,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 11:30:11',
                'updated_at' => '2025-07-17 11:30:11',
            ),
            5 => 
            array (
                'id' => 6,
                'academic_year_id' => 1,
                'grade_level_id' => 4,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 11:30:20',
                'updated_at' => '2025-07-17 11:30:20',
            ),
            6 => 
            array (
                'id' => 7,
                'academic_year_id' => 1,
                'grade_level_id' => 5,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:19:49',
                'updated_at' => '2025-07-17 13:19:49',
            ),
            7 => 
            array (
                'id' => 8,
                'academic_year_id' => 1,
                'grade_level_id' => 5,
                'subject_id' => 11,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:19:55',
                'updated_at' => '2025-07-17 13:19:55',
            ),
            8 => 
            array (
                'id' => 9,
                'academic_year_id' => 1,
                'grade_level_id' => 5,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:20:02',
                'updated_at' => '2025-07-17 13:20:02',
            ),
            9 => 
            array (
                'id' => 10,
                'academic_year_id' => 1,
                'grade_level_id' => 5,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:20:21',
                'updated_at' => '2025-07-17 13:20:21',
            ),
            10 => 
            array (
                'id' => 11,
                'academic_year_id' => 1,
                'grade_level_id' => 5,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:20:42',
                'updated_at' => '2025-07-17 13:20:42',
            ),
            11 => 
            array (
                'id' => 12,
                'academic_year_id' => 1,
                'grade_level_id' => 5,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:20:50',
                'updated_at' => '2025-07-17 13:20:50',
            ),
            12 => 
            array (
                'id' => 13,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:21:10',
                'updated_at' => '2025-07-17 13:21:10',
            ),
            13 => 
            array (
                'id' => 14,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:21:20',
                'updated_at' => '2025-07-17 13:21:20',
            ),
            14 => 
            array (
                'id' => 15,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'subject_id' => 11,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:21:25',
                'updated_at' => '2025-07-17 13:21:25',
            ),
            15 => 
            array (
                'id' => 16,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:21:34',
                'updated_at' => '2025-07-17 13:21:34',
            ),
            16 => 
            array (
                'id' => 17,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:21:43',
                'updated_at' => '2025-07-17 13:21:43',
            ),
            17 => 
            array (
                'id' => 18,
                'academic_year_id' => 1,
                'grade_level_id' => 6,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:21:50',
                'updated_at' => '2025-07-17 13:21:50',
            ),
            18 => 
            array (
                'id' => 19,
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:22:10',
                'updated_at' => '2025-07-17 13:22:10',
            ),
            19 => 
            array (
                'id' => 20,
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:22:14',
                'updated_at' => '2025-07-17 13:22:14',
            ),
            20 => 
            array (
                'id' => 21,
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:22:18',
                'updated_at' => '2025-07-17 13:22:18',
            ),
            21 => 
            array (
                'id' => 22,
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:22:27',
                'updated_at' => '2025-07-17 13:22:27',
            ),
            22 => 
            array (
                'id' => 23,
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:22:34',
                'updated_at' => '2025-07-17 13:22:34',
            ),
            23 => 
            array (
                'id' => 24,
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:22:50',
                'updated_at' => '2025-07-17 13:22:50',
            ),
            24 => 
            array (
                'id' => 25,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:24:28',
                'updated_at' => '2025-07-17 13:24:28',
            ),
            25 => 
            array (
                'id' => 26,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:24:38',
                'updated_at' => '2025-07-17 13:24:38',
            ),
            26 => 
            array (
                'id' => 27,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:31:07',
                'updated_at' => '2025-07-17 13:31:07',
            ),
            27 => 
            array (
                'id' => 28,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:31:37',
                'updated_at' => '2025-07-17 13:31:37',
            ),
            28 => 
            array (
                'id' => 29,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:31:42',
                'updated_at' => '2025-07-17 13:31:42',
            ),
            29 => 
            array (
                'id' => 30,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:31:51',
                'updated_at' => '2025-07-17 13:31:51',
            ),
            30 => 
            array (
                'id' => 31,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 13:32:07',
                'updated_at' => '2025-07-17 13:32:07',
            ),
            31 => 
            array (
                'id' => 32,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:52:40',
                'updated_at' => '2025-07-17 14:52:40',
            ),
            32 => 
            array (
                'id' => 33,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:52:44',
                'updated_at' => '2025-07-17 14:52:44',
            ),
            33 => 
            array (
                'id' => 34,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:52:50',
                'updated_at' => '2025-07-17 14:52:50',
            ),
            34 => 
            array (
                'id' => 35,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:52:57',
                'updated_at' => '2025-07-17 14:52:57',
            ),
            35 => 
            array (
                'id' => 36,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:53:05',
                'updated_at' => '2025-07-17 14:53:05',
            ),
            36 => 
            array (
                'id' => 37,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:53:11',
                'updated_at' => '2025-07-17 14:53:11',
            ),
            37 => 
            array (
                'id' => 38,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 14:53:22',
                'updated_at' => '2025-07-17 14:53:22',
            ),
            38 => 
            array (
                'id' => 39,
                'academic_year_id' => 1,
                'grade_level_id' => 9,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:05:31',
                'updated_at' => '2025-07-17 15:05:31',
            ),
            39 => 
            array (
                'id' => 40,
                'academic_year_id' => 1,
                'grade_level_id' => 8,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:05:57',
                'updated_at' => '2025-07-17 15:05:57',
            ),
            40 => 
            array (
                'id' => 41,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:01',
                'updated_at' => '2025-07-17 15:19:01',
            ),
            41 => 
            array (
                'id' => 42,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:10',
                'updated_at' => '2025-07-17 15:19:10',
            ),
            42 => 
            array (
                'id' => 43,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:16',
                'updated_at' => '2025-07-17 15:19:16',
            ),
            43 => 
            array (
                'id' => 44,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:22',
                'updated_at' => '2025-07-17 15:19:22',
            ),
            44 => 
            array (
                'id' => 45,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'subject_id' => 11,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:31',
                'updated_at' => '2025-07-17 15:19:31',
            ),
            45 => 
            array (
                'id' => 46,
                'academic_year_id' => 2,
                'grade_level_id' => 4,
                'subject_id' => 19,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:39',
                'updated_at' => '2025-07-17 15:19:39',
            ),
            46 => 
            array (
                'id' => 47,
                'academic_year_id' => 2,
                'grade_level_id' => 5,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:19:56',
                'updated_at' => '2025-07-17 15:19:56',
            ),
            47 => 
            array (
                'id' => 48,
                'academic_year_id' => 2,
                'grade_level_id' => 5,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:03',
                'updated_at' => '2025-07-17 15:20:03',
            ),
            48 => 
            array (
                'id' => 49,
                'academic_year_id' => 2,
                'grade_level_id' => 5,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:09',
                'updated_at' => '2025-07-17 15:20:09',
            ),
            49 => 
            array (
                'id' => 50,
                'academic_year_id' => 2,
                'grade_level_id' => 5,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:17',
                'updated_at' => '2025-07-17 15:20:17',
            ),
            50 => 
            array (
                'id' => 51,
                'academic_year_id' => 2,
                'grade_level_id' => 5,
                'subject_id' => 11,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:24',
                'updated_at' => '2025-07-17 15:20:24',
            ),
            51 => 
            array (
                'id' => 52,
                'academic_year_id' => 2,
                'grade_level_id' => 5,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:29',
                'updated_at' => '2025-07-17 15:20:29',
            ),
            52 => 
            array (
                'id' => 53,
                'academic_year_id' => 2,
                'grade_level_id' => 6,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:39',
                'updated_at' => '2025-07-17 15:20:39',
            ),
            53 => 
            array (
                'id' => 54,
                'academic_year_id' => 2,
                'grade_level_id' => 6,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:47',
                'updated_at' => '2025-07-17 15:20:47',
            ),
            54 => 
            array (
                'id' => 55,
                'academic_year_id' => 2,
                'grade_level_id' => 6,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:52',
                'updated_at' => '2025-07-17 15:20:52',
            ),
            55 => 
            array (
                'id' => 56,
                'academic_year_id' => 2,
                'grade_level_id' => 6,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:20:58',
                'updated_at' => '2025-07-17 15:20:58',
            ),
            56 => 
            array (
                'id' => 57,
                'academic_year_id' => 2,
                'grade_level_id' => 6,
                'subject_id' => 11,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:21:06',
                'updated_at' => '2025-07-17 15:21:06',
            ),
            57 => 
            array (
                'id' => 58,
                'academic_year_id' => 2,
                'grade_level_id' => 6,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:21:15',
                'updated_at' => '2025-07-17 15:21:15',
            ),
            58 => 
            array (
                'id' => 59,
                'academic_year_id' => 2,
                'grade_level_id' => 7,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:23:17',
                'updated_at' => '2025-07-17 15:23:17',
            ),
            59 => 
            array (
                'id' => 60,
                'academic_year_id' => 2,
                'grade_level_id' => 7,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:23:29',
                'updated_at' => '2025-07-17 15:23:29',
            ),
            60 => 
            array (
                'id' => 61,
                'academic_year_id' => 2,
                'grade_level_id' => 7,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:23:33',
                'updated_at' => '2025-07-17 15:23:33',
            ),
            61 => 
            array (
                'id' => 62,
                'academic_year_id' => 2,
                'grade_level_id' => 7,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:23:40',
                'updated_at' => '2025-07-17 15:23:40',
            ),
            62 => 
            array (
                'id' => 63,
                'academic_year_id' => 2,
                'grade_level_id' => 7,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:23:57',
                'updated_at' => '2025-07-17 15:23:57',
            ),
            63 => 
            array (
                'id' => 64,
                'academic_year_id' => 2,
                'grade_level_id' => 7,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:01',
                'updated_at' => '2025-07-17 15:24:01',
            ),
            64 => 
            array (
                'id' => 65,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:08',
                'updated_at' => '2025-07-17 15:24:08',
            ),
            65 => 
            array (
                'id' => 66,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:12',
                'updated_at' => '2025-07-17 15:24:12',
            ),
            66 => 
            array (
                'id' => 67,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:21',
                'updated_at' => '2025-07-17 15:24:21',
            ),
            67 => 
            array (
                'id' => 68,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:35',
                'updated_at' => '2025-07-17 15:24:35',
            ),
            68 => 
            array (
                'id' => 69,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:43',
                'updated_at' => '2025-07-17 15:24:43',
            ),
            69 => 
            array (
                'id' => 70,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:46',
                'updated_at' => '2025-07-17 15:24:46',
            ),
            70 => 
            array (
                'id' => 71,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:53',
                'updated_at' => '2025-07-17 15:24:53',
            ),
            71 => 
            array (
                'id' => 72,
                'academic_year_id' => 2,
                'grade_level_id' => 8,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:24:57',
                'updated_at' => '2025-07-17 15:24:57',
            ),
            72 => 
            array (
                'id' => 73,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:25:39',
                'updated_at' => '2025-07-17 15:25:39',
            ),
            73 => 
            array (
                'id' => 74,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:25:43',
                'updated_at' => '2025-07-17 15:25:43',
            ),
            74 => 
            array (
                'id' => 75,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:25:51',
                'updated_at' => '2025-07-17 15:25:51',
            ),
            75 => 
            array (
                'id' => 76,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:26:08',
                'updated_at' => '2025-07-17 15:26:08',
            ),
            76 => 
            array (
                'id' => 77,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:26:16',
                'updated_at' => '2025-07-17 15:26:16',
            ),
            77 => 
            array (
                'id' => 78,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:26:20',
                'updated_at' => '2025-07-17 15:26:20',
            ),
            78 => 
            array (
                'id' => 79,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:26:25',
                'updated_at' => '2025-07-17 15:26:25',
            ),
            79 => 
            array (
                'id' => 80,
                'academic_year_id' => 2,
                'grade_level_id' => 9,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 15:26:30',
                'updated_at' => '2025-07-17 15:26:30',
            ),
            80 => 
            array (
                'id' => 81,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:08:23',
                'updated_at' => '2025-07-17 16:08:23',
            ),
            81 => 
            array (
                'id' => 82,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:08:32',
                'updated_at' => '2025-07-17 16:08:32',
            ),
            82 => 
            array (
                'id' => 83,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:08:36',
                'updated_at' => '2025-07-17 16:08:36',
            ),
            83 => 
            array (
                'id' => 84,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:08:44',
                'updated_at' => '2025-07-17 16:08:44',
            ),
            84 => 
            array (
                'id' => 85,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:08:50',
                'updated_at' => '2025-07-17 16:08:50',
            ),
            85 => 
            array (
                'id' => 86,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:08:58',
                'updated_at' => '2025-07-17 16:08:58',
            ),
            86 => 
            array (
                'id' => 87,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:09:07',
                'updated_at' => '2025-07-17 16:09:07',
            ),
            87 => 
            array (
                'id' => 88,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:09:13',
                'updated_at' => '2025-07-17 16:09:13',
            ),
            88 => 
            array (
                'id' => 89,
                'academic_year_id' => 5,
                'grade_level_id' => 1,
                'subject_id' => 9,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:09:20',
                'updated_at' => '2025-07-17 16:09:20',
            ),
            89 => 
            array (
                'id' => 90,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:09:32',
                'updated_at' => '2025-07-17 16:09:32',
            ),
            90 => 
            array (
                'id' => 91,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:09:37',
                'updated_at' => '2025-07-17 16:09:37',
            ),
            91 => 
            array (
                'id' => 92,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:10:19',
                'updated_at' => '2025-07-17 16:10:19',
            ),
            92 => 
            array (
                'id' => 93,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:10:23',
                'updated_at' => '2025-07-17 16:10:23',
            ),
            93 => 
            array (
                'id' => 94,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:10:33',
                'updated_at' => '2025-07-17 16:10:33',
            ),
            94 => 
            array (
                'id' => 95,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:10:37',
                'updated_at' => '2025-07-17 16:10:37',
            ),
            95 => 
            array (
                'id' => 96,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:10:50',
                'updated_at' => '2025-07-17 16:10:50',
            ),
            96 => 
            array (
                'id' => 97,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:10:58',
                'updated_at' => '2025-07-17 16:10:58',
            ),
            97 => 
            array (
                'id' => 98,
                'academic_year_id' => 5,
                'grade_level_id' => 2,
                'subject_id' => 9,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:11:40',
                'updated_at' => '2025-07-17 16:11:40',
            ),
            98 => 
            array (
                'id' => 99,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:12:28',
                'updated_at' => '2025-07-17 16:12:28',
            ),
            99 => 
            array (
                'id' => 100,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:12:34',
                'updated_at' => '2025-07-17 16:12:34',
            ),
            100 => 
            array (
                'id' => 101,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:12:39',
                'updated_at' => '2025-07-17 16:12:39',
            ),
            101 => 
            array (
                'id' => 102,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:12:47',
                'updated_at' => '2025-07-17 16:12:47',
            ),
            102 => 
            array (
                'id' => 103,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:12:51',
                'updated_at' => '2025-07-17 16:12:51',
            ),
            103 => 
            array (
                'id' => 104,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:12:59',
                'updated_at' => '2025-07-17 16:12:59',
            ),
            104 => 
            array (
                'id' => 105,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:13:04',
                'updated_at' => '2025-07-17 16:13:04',
            ),
            105 => 
            array (
                'id' => 106,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:13:10',
                'updated_at' => '2025-07-17 16:13:10',
            ),
            106 => 
            array (
                'id' => 107,
                'academic_year_id' => 5,
                'grade_level_id' => 3,
                'subject_id' => 10,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:13:17',
                'updated_at' => '2025-07-17 16:13:17',
            ),
            107 => 
            array (
                'id' => 108,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:16:32',
                'updated_at' => '2025-07-17 16:16:32',
            ),
            108 => 
            array (
                'id' => 109,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:16:40',
                'updated_at' => '2025-07-17 16:16:40',
            ),
            109 => 
            array (
                'id' => 110,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:16:50',
                'updated_at' => '2025-07-17 16:16:50',
            ),
            110 => 
            array (
                'id' => 111,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:17:03',
                'updated_at' => '2025-07-17 16:17:03',
            ),
            111 => 
            array (
                'id' => 112,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:17:07',
                'updated_at' => '2025-07-17 16:17:07',
            ),
            112 => 
            array (
                'id' => 113,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:17:11',
                'updated_at' => '2025-07-17 16:17:11',
            ),
            113 => 
            array (
                'id' => 114,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:17:19',
                'updated_at' => '2025-07-17 16:17:19',
            ),
            114 => 
            array (
                'id' => 115,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 9,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:17:24',
                'updated_at' => '2025-07-17 16:17:24',
            ),
            115 => 
            array (
                'id' => 116,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:06',
                'updated_at' => '2025-07-17 16:20:06',
            ),
            116 => 
            array (
                'id' => 117,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:14',
                'updated_at' => '2025-07-17 16:20:14',
            ),
            117 => 
            array (
                'id' => 118,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:18',
                'updated_at' => '2025-07-17 16:20:18',
            ),
            118 => 
            array (
                'id' => 119,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:23',
                'updated_at' => '2025-07-17 16:20:23',
            ),
            119 => 
            array (
                'id' => 120,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:36',
                'updated_at' => '2025-07-17 16:20:36',
            ),
            120 => 
            array (
                'id' => 121,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:50',
                'updated_at' => '2025-07-17 16:20:50',
            ),
            121 => 
            array (
                'id' => 122,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:20:58',
                'updated_at' => '2025-07-17 16:20:58',
            ),
            122 => 
            array (
                'id' => 123,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:21:08',
                'updated_at' => '2025-07-17 16:21:08',
            ),
            123 => 
            array (
                'id' => 124,
                'academic_year_id' => 6,
                'grade_level_id' => 2,
                'subject_id' => 9,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:21:33',
                'updated_at' => '2025-07-17 16:21:33',
            ),
            124 => 
            array (
                'id' => 125,
                'academic_year_id' => 6,
                'grade_level_id' => 1,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:22:18',
                'updated_at' => '2025-07-17 16:22:18',
            ),
            125 => 
            array (
                'id' => 126,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:22:27',
                'updated_at' => '2025-07-17 16:22:27',
            ),
            126 => 
            array (
                'id' => 127,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:22:33',
                'updated_at' => '2025-07-17 16:22:33',
            ),
            127 => 
            array (
                'id' => 128,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:22:38',
                'updated_at' => '2025-07-17 16:22:38',
            ),
            128 => 
            array (
                'id' => 129,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:22:45',
                'updated_at' => '2025-07-17 16:22:45',
            ),
            129 => 
            array (
                'id' => 130,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 5,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:22:59',
                'updated_at' => '2025-07-17 16:22:59',
            ),
            130 => 
            array (
                'id' => 131,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:23:05',
                'updated_at' => '2025-07-17 16:23:05',
            ),
            131 => 
            array (
                'id' => 132,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:23:12',
                'updated_at' => '2025-07-17 16:23:12',
            ),
            132 => 
            array (
                'id' => 133,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 9,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:31:03',
                'updated_at' => '2025-07-17 16:31:03',
            ),
            133 => 
            array (
                'id' => 134,
                'academic_year_id' => 6,
                'grade_level_id' => 3,
                'subject_id' => 8,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:31:10',
                'updated_at' => '2025-07-17 16:31:10',
            ),
            134 => 
            array (
                'id' => 135,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:31:40',
                'updated_at' => '2025-07-17 16:31:40',
            ),
            135 => 
            array (
                'id' => 136,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:31:46',
                'updated_at' => '2025-07-17 16:31:46',
            ),
            136 => 
            array (
                'id' => 137,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:31:52',
                'updated_at' => '2025-07-17 16:31:52',
            ),
            137 => 
            array (
                'id' => 138,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:36:26',
                'updated_at' => '2025-07-17 16:36:26',
            ),
            138 => 
            array (
                'id' => 139,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:36:34',
                'updated_at' => '2025-07-17 16:36:34',
            ),
            139 => 
            array (
                'id' => 140,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:36:38',
                'updated_at' => '2025-07-17 16:36:38',
            ),
            140 => 
            array (
                'id' => 141,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 12,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:36:52',
                'updated_at' => '2025-07-17 16:36:52',
            ),
            141 => 
            array (
                'id' => 142,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 13,
                'teacher_id' => NULL,
                'created_at' => '2025-07-17 16:37:02',
                'updated_at' => '2025-07-17 16:37:02',
            ),
            142 => 
            array (
                'id' => 144,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:20:34',
                'updated_at' => '2025-07-19 13:20:34',
            ),
            143 => 
            array (
                'id' => 145,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:20:43',
                'updated_at' => '2025-07-19 13:20:43',
            ),
            144 => 
            array (
                'id' => 146,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:20:48',
                'updated_at' => '2025-07-19 13:20:48',
            ),
            145 => 
            array (
                'id' => 147,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:20:56',
                'updated_at' => '2025-07-19 13:20:56',
            ),
            146 => 
            array (
                'id' => 148,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 12,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:04',
                'updated_at' => '2025-07-19 13:21:04',
            ),
            147 => 
            array (
                'id' => 149,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:09',
                'updated_at' => '2025-07-19 13:21:09',
            ),
            148 => 
            array (
                'id' => 150,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:13',
                'updated_at' => '2025-07-19 13:21:13',
            ),
            149 => 
            array (
                'id' => 151,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 13,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:25',
                'updated_at' => '2025-07-19 13:21:25',
            ),
            150 => 
            array (
                'id' => 152,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 14,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:33',
                'updated_at' => '2025-07-19 13:21:33',
            ),
            151 => 
            array (
                'id' => 153,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 15,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:37',
                'updated_at' => '2025-07-19 13:21:37',
            ),
            152 => 
            array (
                'id' => 154,
                'academic_year_id' => 3,
                'grade_level_id' => 11,
                'subject_id' => 16,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:21:51',
                'updated_at' => '2025-07-19 13:21:51',
            ),
            153 => 
            array (
                'id' => 155,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 16,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:04',
                'updated_at' => '2025-07-19 13:22:04',
            ),
            154 => 
            array (
                'id' => 156,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 14,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:10',
                'updated_at' => '2025-07-19 13:22:10',
            ),
            155 => 
            array (
                'id' => 157,
                'academic_year_id' => 3,
                'grade_level_id' => 10,
                'subject_id' => 15,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:14',
                'updated_at' => '2025-07-19 13:22:14',
            ),
            156 => 
            array (
                'id' => 158,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:26',
                'updated_at' => '2025-07-19 13:22:26',
            ),
            157 => 
            array (
                'id' => 159,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:31',
                'updated_at' => '2025-07-19 13:22:31',
            ),
            158 => 
            array (
                'id' => 160,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:39',
                'updated_at' => '2025-07-19 13:22:39',
            ),
            159 => 
            array (
                'id' => 161,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:46',
                'updated_at' => '2025-07-19 13:22:46',
            ),
            160 => 
            array (
                'id' => 162,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:22:59',
                'updated_at' => '2025-07-19 13:22:59',
            ),
            161 => 
            array (
                'id' => 163,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:05',
                'updated_at' => '2025-07-19 13:23:05',
            ),
            162 => 
            array (
                'id' => 164,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 12,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:13',
                'updated_at' => '2025-07-19 13:23:13',
            ),
            163 => 
            array (
                'id' => 165,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 17,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:25',
                'updated_at' => '2025-07-19 13:23:25',
            ),
            164 => 
            array (
                'id' => 166,
                'academic_year_id' => 3,
                'grade_level_id' => 12,
                'subject_id' => 19,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:29',
                'updated_at' => '2025-07-19 13:23:29',
            ),
            165 => 
            array (
                'id' => 167,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:37',
                'updated_at' => '2025-07-19 13:23:37',
            ),
            166 => 
            array (
                'id' => 168,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:43',
                'updated_at' => '2025-07-19 13:23:43',
            ),
            167 => 
            array (
                'id' => 169,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:48',
                'updated_at' => '2025-07-19 13:23:48',
            ),
            168 => 
            array (
                'id' => 170,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:23:56',
                'updated_at' => '2025-07-19 13:23:56',
            ),
            169 => 
            array (
                'id' => 171,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 15,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:24:01',
                'updated_at' => '2025-07-19 13:24:01',
            ),
            170 => 
            array (
                'id' => 172,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 14,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:24:08',
                'updated_at' => '2025-07-19 13:24:08',
            ),
            171 => 
            array (
                'id' => 173,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 16,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:24:14',
                'updated_at' => '2025-07-19 13:24:14',
            ),
            172 => 
            array (
                'id' => 174,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 18,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:24:19',
                'updated_at' => '2025-07-19 13:24:19',
            ),
            173 => 
            array (
                'id' => 175,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 13,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:24:30',
                'updated_at' => '2025-07-19 13:24:30',
            ),
            174 => 
            array (
                'id' => 176,
                'academic_year_id' => 3,
                'grade_level_id' => 13,
                'subject_id' => 19,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:24:35',
                'updated_at' => '2025-07-19 13:24:35',
            ),
            175 => 
            array (
                'id' => 177,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:02',
                'updated_at' => '2025-07-19 13:25:02',
            ),
            176 => 
            array (
                'id' => 178,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:14',
                'updated_at' => '2025-07-19 13:25:14',
            ),
            177 => 
            array (
                'id' => 179,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:23',
                'updated_at' => '2025-07-19 13:25:23',
            ),
            178 => 
            array (
                'id' => 180,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:28',
                'updated_at' => '2025-07-19 13:25:28',
            ),
            179 => 
            array (
                'id' => 181,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:33',
                'updated_at' => '2025-07-19 13:25:33',
            ),
            180 => 
            array (
                'id' => 182,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:37',
                'updated_at' => '2025-07-19 13:25:37',
            ),
            181 => 
            array (
                'id' => 183,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 12,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:44',
                'updated_at' => '2025-07-19 13:25:44',
            ),
            182 => 
            array (
                'id' => 184,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 13,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:48',
                'updated_at' => '2025-07-19 13:25:48',
            ),
            183 => 
            array (
                'id' => 185,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 16,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:56',
                'updated_at' => '2025-07-19 13:25:56',
            ),
            184 => 
            array (
                'id' => 186,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 14,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:25:59',
                'updated_at' => '2025-07-19 13:25:59',
            ),
            185 => 
            array (
                'id' => 187,
                'academic_year_id' => 4,
                'grade_level_id' => 10,
                'subject_id' => 15,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:26:04',
                'updated_at' => '2025-07-19 13:26:04',
            ),
            186 => 
            array (
                'id' => 188,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:26:15',
                'updated_at' => '2025-07-19 13:26:15',
            ),
            187 => 
            array (
                'id' => 189,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:26:20',
                'updated_at' => '2025-07-19 13:26:20',
            ),
            188 => 
            array (
                'id' => 190,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:26:36',
                'updated_at' => '2025-07-19 13:26:36',
            ),
            189 => 
            array (
                'id' => 191,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 4,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:26:45',
                'updated_at' => '2025-07-19 13:26:45',
            ),
            190 => 
            array (
                'id' => 192,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:27:25',
                'updated_at' => '2025-07-19 13:27:25',
            ),
            191 => 
            array (
                'id' => 193,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:27:29',
                'updated_at' => '2025-07-19 13:27:29',
            ),
            192 => 
            array (
                'id' => 194,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 12,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:27:43',
                'updated_at' => '2025-07-19 13:27:43',
            ),
            193 => 
            array (
                'id' => 195,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 17,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:27:53',
                'updated_at' => '2025-07-19 13:27:53',
            ),
            194 => 
            array (
                'id' => 196,
                'academic_year_id' => 4,
                'grade_level_id' => 11,
                'subject_id' => 19,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:27:58',
                'updated_at' => '2025-07-19 13:27:58',
            ),
            195 => 
            array (
                'id' => 197,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:28:12',
                'updated_at' => '2025-07-19 13:28:12',
            ),
            196 => 
            array (
                'id' => 198,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:28:17',
                'updated_at' => '2025-07-19 13:28:17',
            ),
            197 => 
            array (
                'id' => 199,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:28:22',
                'updated_at' => '2025-07-19 13:28:22',
            ),
            198 => 
            array (
                'id' => 201,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 7,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:28:37',
                'updated_at' => '2025-07-19 13:28:37',
            ),
            199 => 
            array (
                'id' => 202,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 6,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:28:43',
                'updated_at' => '2025-07-19 13:28:43',
            ),
            200 => 
            array (
                'id' => 203,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 17,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:28:52',
                'updated_at' => '2025-07-19 13:28:52',
            ),
            201 => 
            array (
                'id' => 204,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 19,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:29:00',
                'updated_at' => '2025-07-19 13:29:00',
            ),
            202 => 
            array (
                'id' => 205,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 3,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:29:20',
                'updated_at' => '2025-07-19 13:29:20',
            ),
            203 => 
            array (
                'id' => 206,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 2,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:29:26',
                'updated_at' => '2025-07-19 13:29:26',
            ),
            204 => 
            array (
                'id' => 208,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 21,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:29:53',
                'updated_at' => '2025-07-19 13:29:53',
            ),
            205 => 
            array (
                'id' => 209,
                'academic_year_id' => 4,
                'grade_level_id' => 12,
                'subject_id' => 20,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:30:11',
                'updated_at' => '2025-07-19 13:30:11',
            ),
            206 => 
            array (
                'id' => 212,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 14,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:06',
                'updated_at' => '2025-07-19 13:31:06',
            ),
            207 => 
            array (
                'id' => 213,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 15,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:11',
                'updated_at' => '2025-07-19 13:31:11',
            ),
            208 => 
            array (
                'id' => 214,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 18,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:16',
                'updated_at' => '2025-07-19 13:31:16',
            ),
            209 => 
            array (
                'id' => 215,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 16,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:21',
                'updated_at' => '2025-07-19 13:31:21',
            ),
            210 => 
            array (
                'id' => 216,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 1,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:35',
                'updated_at' => '2025-07-19 13:31:35',
            ),
            211 => 
            array (
                'id' => 217,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 13,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:43',
                'updated_at' => '2025-07-19 13:31:43',
            ),
            212 => 
            array (
                'id' => 218,
                'academic_year_id' => 4,
                'grade_level_id' => 13,
                'subject_id' => 19,
                'teacher_id' => NULL,
                'created_at' => '2025-07-19 13:31:50',
                'updated_at' => '2025-07-19 13:31:50',
            ),
        ));
        
        
    }
}