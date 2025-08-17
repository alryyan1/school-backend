<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentWarningsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('student_warnings')->delete();
        
        \DB::table('student_warnings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'student_academic_year_id' => 5,
                'issued_by_user_id' => 1,
                'severity' => 'low',
                'reason' => 'zeeee',
                'issued_at' => '2025-08-13 08:17:29',
                'created_at' => '2025-08-13 10:17:30',
                'updated_at' => '2025-08-13 10:17:30',
            ),
        ));
        
        
    }
}