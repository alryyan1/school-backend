<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AcademicYearsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('academic_years')->delete();
        
        \DB::table('academic_years')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '2025-2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 3,
                'created_at' => '2025-07-11 18:10:48',
                'updated_at' => '2025-07-11 18:10:48',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '2025-2026',
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 2,
                'created_at' => '2025-07-11 18:56:00',
                'updated_at' => '2025-07-11 18:56:00',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '2025-2026',
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 7,
                'created_at' => '2025-07-11 18:56:23',
                'updated_at' => '2025-07-11 18:56:23',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '2025-2026',
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 6,
                'created_at' => '2025-07-11 20:38:22',
                'updated_at' => '2025-07-11 20:38:22',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '2025-2026',
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 5,
                'created_at' => '2025-07-11 20:38:42',
                'updated_at' => '2025-07-11 20:38:42',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '2025-2026',
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 4,
                'created_at' => '2025-07-11 20:39:08',
                'updated_at' => '2025-07-11 20:39:08',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '2025-2026',
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'is_current' => 0,
                'school_id' => 1,
                'created_at' => '2025-07-11 20:39:28',
                'updated_at' => '2025-07-11 20:39:28',
            ),
        ));
        
        
    }
}