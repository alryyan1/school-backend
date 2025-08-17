<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AcademicYearFeesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('academic_year_fees')->delete();
        
        
        
    }
}