<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentFeePaymentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('student_fee_payments')->delete();
        
        \DB::table('student_fee_payments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'fee_installment_id' => 1,
                'amount' => '2000.00',
                'payment_method_id' => 1,
                'payment_date' => '2025-07-19',
                'notes' => NULL,
                'created_at' => '2025-07-19 15:25:52',
                'updated_at' => '2025-07-19 15:25:52',
            ),
            1 => 
            array (
                'id' => 2,
                'fee_installment_id' => 4,
                'amount' => '12500.00',
                'payment_method_id' => 1,
                'payment_date' => '2025-07-19',
                'notes' => NULL,
                'created_at' => '2025-07-19 15:28:42',
                'updated_at' => '2025-07-19 15:28:42',
            ),
            2 => 
            array (
                'id' => 3,
                'fee_installment_id' => 5,
                'amount' => '166000.00',
                'payment_method_id' => 1,
                'payment_date' => '2025-07-22',
                'notes' => NULL,
                'created_at' => '2025-07-22 19:21:36',
                'updated_at' => '2025-07-22 19:21:36',
            ),
            3 => 
            array (
                'id' => 4,
                'fee_installment_id' => 8,
                'amount' => '125000.00',
                'payment_method_id' => 2,
                'payment_date' => '2025-08-04',
                'notes' => 'تم دفع قسط اول',
                'created_at' => '2025-08-04 18:19:53',
                'updated_at' => '2025-08-04 18:19:53',
            ),
        ));
        
        
    }
}