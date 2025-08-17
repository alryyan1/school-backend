<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubjectsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('subjects')->delete();
        
        \DB::table('subjects')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'تربية اسلامية',
                'code' => 'ISLM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'لغة عربية',
                'code' => 'ARAB',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'English',
                'code' => 'ENGL',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'رياضيات',
                'code' => 'MATH',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'علوم',
                'code' => 'SCI',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'جغرافيا',
                'code' => 'GEO',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'تاريخ',
                'code' => 'HIST',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'تكنولوجيا',
                'code' => 'TECH',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'تربية تقنية',
                'code' => 'TECH-ED',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'تربية فنية',
                'code' => 'ART-ED',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'فنية',
                'code' => 'ART',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'دراسات اسلامية',
                'code' => 'ISLM-ST',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'هندسية',
                'code' => 'ENGIN',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'فيزياء',
                'code' => 'PHYS',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'كيمياء',
                'code' => 'CHEM',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'احياء',
                'code' => 'BIO',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'أدب انجليزي',
                'code' => 'ENGL-LIT',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'حاسوب',
                'code' => 'COMP-SCI',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'فنون',
                'code' => 'FINE-ART',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'رياضيات اساسية',
                'code' => 'MATH-B',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'رياضيات متخصصة',
                'code' => 'MATH-A',
                'description' => NULL,
                'created_at' => '2025-07-11 21:45:51',
                'updated_at' => '2025-07-11 21:45:51',
            ),
        ));
        
        
    }
}