<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GradeLevelsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('grade_levels')->delete();
        
        \DB::table('grade_levels')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'اول متوسط',
                'code' => 'G7-MID',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'ثاني متوسط',
                'code' => 'G8-MID',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'ثالث متوسط',
                'code' => 'G9-MID',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'الصف الاول ابتدائي',
                'code' => 'G1-ELEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'الصف الثاني ابتدائي',
                'code' => 'G2-ELEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'الصف الثالث ابتدائي',
                'code' => 'G3-ELEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'الصف الرابع ابتدائي',
                'code' => 'G4-ELEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'الصف الخامس ابتدائي',
                'code' => 'G5-ELEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'الصف السادس ابتدائي',
                'code' => 'G6-ELEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'الصف الاول ثانوي',
                'code' => 'G10-SEC',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'الصف الثاني ثانوي',
                'code' => 'G11-SEC',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'الصف الثالث ادبي',
                'code' => 'G12-LIT',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'الصف الثالث علمي',
                'code' => 'G12-SCI',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'البراعم',
                'code' => 'P1',
                'description' => NULL,
                'created_at' => '2025-07-17 08:59:16',
                'updated_at' => '2025-07-17 08:59:16',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'روضه مستوي اول',
                'code' => 'P2',
                'description' => NULL,
                'created_at' => '2025-07-17 08:59:33',
                'updated_at' => '2025-07-17 08:59:33',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'روضه مستوي ثاني',
                'code' => 'P3',
                'description' => NULL,
                'created_at' => '2025-07-17 08:59:45',
                'updated_at' => '2025-07-17 08:59:45',
            ),
        ));
        
        
    }
}