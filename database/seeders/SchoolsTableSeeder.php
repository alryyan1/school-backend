<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('schools')->delete();
        
        \DB::table('schools')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'الروضه',
                'code' => 'sch-001',
                'address' => 'الروضه',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:09:57',
                'updated_at' => '2025-07-11 17:09:57',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'الابتدائي بنين',
                'code' => 'sch-002',
                'address' => 'الابتدائي بنين',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:10:50',
                'updated_at' => '2025-07-11 17:10:50',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'الابتدائي بنات',
                'code' => 'sch-003',
                'address' => 'الابتدائي بنات',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:11:35',
                'updated_at' => '2025-07-11 17:11:35',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'المتوسط بنين',
                'code' => 'sch-004',
                'address' => 'المتوسط بنين',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:12:37',
                'updated_at' => '2025-07-11 17:12:37',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'المتوسط بنات',
                'code' => 'sch-005',
                'address' => 'المتوسط بنات',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:13:39',
                'updated_at' => '2025-07-11 17:13:39',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'الثانويه بنين',
                'code' => 'sch-006',
                'address' => 'الثانويه بنين',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:14:11',
                'updated_at' => '2025-07-11 17:14:11',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'الثانويه بنات',
                'code' => 'sch-007',
                'address' => 'الثانويه بنات',
                'phone' => '0',
                'email' => 'alfanar@gmail.com',
                'principal_name' => NULL,
                'establishment_date' => NULL,
                'logo' => NULL,
                'created_at' => '2025-07-11 17:14:38',
                'updated_at' => '2025-07-11 17:14:38',
            ),
        ));
        
        
    }
}