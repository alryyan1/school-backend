<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentMethodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_methods')->delete();
        
        \DB::table('payment_methods')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'cash',
                'created_at' => '2025-08-11 23:54:46',
                'updated_at' => '2025-08-11 23:54:46',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'bank',
                'created_at' => '2025-08-11 23:54:46',
                'updated_at' => '2025-08-11 23:54:46',
            ),
        ));
        
        
    }
}