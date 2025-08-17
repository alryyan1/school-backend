<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'super-manager',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'general-manager',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'accountant',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'school-principal',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:52',
                'updated_at' => '2025-07-11 21:45:52',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'nurse',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:53',
                'updated_at' => '2025-07-11 21:45:53',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'transport-manager',
                'guard_name' => 'web',
                'created_at' => '2025-07-11 21:45:53',
                'updated_at' => '2025-07-11 21:45:53',
            ),
        ));
        
        
    }
}