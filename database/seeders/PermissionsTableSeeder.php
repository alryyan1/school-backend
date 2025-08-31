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
                'name' => 'قبول الطالب',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'اضافه طالب',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'تعديل بيانات الطالب',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'سداد رسوم الطالب',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'تعيين الطالب',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
        
        
    }
}