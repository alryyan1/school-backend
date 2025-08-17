<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'manage system settings',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'manage users',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'manage roles',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'view system dashboard',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'create schools',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'view any school',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'view own school',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'edit any school',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'edit own school',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'delete schools',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'manage school-grade_levels',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'view any student',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'view own school students',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'create students',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'edit students',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'delete students',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'approve students',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'print student profile',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'view any teacher',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'view own school teachers',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'create teachers',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'edit teachers',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'delete teachers',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'assign subject to teacher',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'manage academic years',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'manage grade levels',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'manage subjects',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'manage classrooms',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'manage curriculum',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'manage student enrollments',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'view student enrollments',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'manage exams',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'manage exam schedules',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'enter exam results',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'view any exam results',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'view own school exam results',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'view student fee overview',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'manage fee installments',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'record fee payments',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'view financial reports',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'access school treasury',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'manage transport routes',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'assign students to transport',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'view transport assignments',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'view student medical records',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'edit student medical records',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'enrollment permission',
                'guard_name' => 'web',
                'created_at' => '2025-08-11 09:46:05',
                'updated_at' => '2025-08-11 09:46:05',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'assign student to academic year',
                'guard_name' => 'web',
                'created_at' => '2025-08-11 18:45:26',
                'updated_at' => '2025-08-11 18:45:26',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'set student enrollment type',
                'guard_name' => 'web',
                'created_at' => '2025-08-13 08:15:31',
                'updated_at' => '2025-08-13 08:15:31',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'apply fee discount',
                'guard_name' => 'web',
                'created_at' => '2025-08-13 08:15:31',
                'updated_at' => '2025-08-13 08:15:31',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'assign student to classroom',
                'guard_name' => 'web',
                'created_at' => '2025-08-13 08:16:03',
                'updated_at' => '2025-08-13 08:16:03',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'manage student warnings',
                'guard_name' => 'web',
                'created_at' => '2025-08-13 09:38:51',
                'updated_at' => '2025-08-13 09:38:51',
            ),
        ));
        
        
    }
}