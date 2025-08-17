<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentAbsencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('student_absences')->delete();
        
        \DB::table('student_absences')->insert(array (
            0 => 
            array (
                'id' => 1,
                'student_academic_year_id' => 5,
                'absent_date' => '2025-08-13',
                'reason' => 'erer',
                'excused' => 0,
                'created_at' => '2025-08-13 12:14:16',
                'updated_at' => '2025-08-13 12:14:16',
            ),
        ));
        
        
    }
}