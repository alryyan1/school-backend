<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentNotesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('student_notes')->delete();
        
        \DB::table('student_notes')->insert(array (
            0 => 
            array (
                'id' => 4,
                'student_academic_years_id' => 1,
                'note' => 'يحتاج إلى تطوير مهارات التواصل',
                'user_id' => 1,
                'created_at' => '2025-07-22 16:14:17',
                'updated_at' => '2025-07-22 16:14:17',
            ),
            1 => 
            array (
                'id' => 5,
                'student_academic_years_id' => 1,
                'note' => 'الطالب قائد طبيعي بين زملائه',
                'user_id' => 1,
                'created_at' => '2025-07-22 16:17:13',
                'updated_at' => '2025-07-22 16:17:13',
            ),
            2 => 
            array (
                'id' => 6,
                'student_academic_years_id' => 1,
                'note' => 'يحتاج إلى تحسين في الانضباط والالتزام',
                'user_id' => 1,
                'created_at' => '2025-07-22 16:17:20',
                'updated_at' => '2025-07-22 16:17:20',
            ),
            3 => 
            array (
                'id' => 7,
                'student_academic_years_id' => 1,
                'note' => 'تالاتالاتلتالتا',
                'user_id' => 1,
                'created_at' => '2025-07-22 19:36:15',
                'updated_at' => '2025-07-22 19:36:15',
            ),
            4 => 
            array (
                'id' => 8,
                'student_academic_years_id' => 3,
                'note' => 'الطالب لديه مواهب فنية مميزة',
                'user_id' => 1,
                'created_at' => '2025-07-22 19:38:06',
                'updated_at' => '2025-07-22 19:38:06',
            ),
            5 => 
            array (
                'id' => 9,
                'student_academic_years_id' => 3,
                'note' => 'الطالب يواجه صعوبة في مادة الرياضيات',
                'user_id' => 1,
                'created_at' => '2025-07-22 19:38:22',
                'updated_at' => '2025-07-22 19:38:22',
            ),
        ));
        
        
    }
}