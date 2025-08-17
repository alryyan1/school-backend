<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FeeInstallmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('fee_installments')->delete();
        
        \DB::table('fee_installments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'student_academic_year_id' => 2,
                'title' => 'القسط الأول',
                'amount_due' => '12500.00',
                'amount_paid' => '2000.00',
                'due_date' => '2025-07-01',
                'status' => 'دفع جزئي',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-19 14:59:07',
                'updated_at' => '2025-07-19 15:25:52',
            ),
            1 => 
            array (
                'id' => 2,
                'student_academic_year_id' => 2,
                'title' => 'القسط الثاني',
                'amount_due' => '12500.00',
                'amount_paid' => '0.00',
                'due_date' => '2025-12-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-19 14:59:07',
                'updated_at' => '2025-07-19 14:59:07',
            ),
            2 => 
            array (
                'id' => 3,
                'student_academic_year_id' => 2,
                'title' => 'القسط الثالث',
                'amount_due' => '12500.00',
                'amount_paid' => '0.00',
                'due_date' => '2026-05-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-19 14:59:07',
                'updated_at' => '2025-07-19 14:59:07',
            ),
            3 => 
            array (
                'id' => 4,
                'student_academic_year_id' => 2,
                'title' => 'القسط الرابع',
                'amount_due' => '12500.00',
                'amount_paid' => '12500.00',
                'due_date' => '2026-10-01',
                'status' => 'مدفوع',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-19 14:59:07',
                'updated_at' => '2025-07-19 15:28:42',
            ),
            4 => 
            array (
                'id' => 5,
                'student_academic_year_id' => 3,
                'title' => 'القسط الأول',
                'amount_due' => '166666.67',
                'amount_paid' => '166000.00',
                'due_date' => '2025-01-01',
                'status' => 'دفع جزئي',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-22 19:20:37',
                'updated_at' => '2025-07-22 19:21:36',
            ),
            5 => 
            array (
                'id' => 6,
                'student_academic_year_id' => 3,
                'title' => 'القسط الثاني',
                'amount_due' => '166666.67',
                'amount_paid' => '0.00',
                'due_date' => '2025-12-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-22 19:20:37',
                'updated_at' => '2025-07-22 19:20:37',
            ),
            6 => 
            array (
                'id' => 7,
                'student_academic_year_id' => 3,
                'title' => 'القسط الثالث',
                'amount_due' => '166666.66',
                'amount_paid' => '0.00',
                'due_date' => '2026-11-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-07-22 19:20:37',
                'updated_at' => '2025-07-22 19:20:37',
            ),
            7 => 
            array (
                'id' => 8,
                'student_academic_year_id' => 5,
                'title' => 'القسط الأول',
                'amount_due' => '125000.00',
                'amount_paid' => '125000.00',
                'due_date' => '2025-07-01',
                'status' => 'مدفوع',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:07:48',
                'updated_at' => '2025-08-04 18:19:53',
            ),
            8 => 
            array (
                'id' => 9,
                'student_academic_year_id' => 5,
                'title' => 'القسط الثاني',
                'amount_due' => '125000.00',
                'amount_paid' => '0.00',
                'due_date' => '2025-12-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:07:48',
                'updated_at' => '2025-08-04 18:07:48',
            ),
            9 => 
            array (
                'id' => 10,
                'student_academic_year_id' => 5,
                'title' => 'القسط الثالث',
                'amount_due' => '125000.00',
                'amount_paid' => '0.00',
                'due_date' => '2026-05-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:07:48',
                'updated_at' => '2025-08-04 18:07:48',
            ),
            10 => 
            array (
                'id' => 11,
                'student_academic_year_id' => 5,
                'title' => 'القسط الرابع',
                'amount_due' => '125000.00',
                'amount_paid' => '0.00',
                'due_date' => '2026-10-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:07:48',
                'updated_at' => '2025-08-04 18:07:48',
            ),
            11 => 
            array (
                'id' => 12,
                'student_academic_year_id' => 5,
                'title' => 'القسط الخامس',
                'amount_due' => '7000.00',
                'amount_paid' => '0.00',
                'due_date' => '2025-09-04',
                'status' => 'قيد الانتظار',
                'notes' => NULL,
                'created_at' => '2025-08-04 18:10:33',
                'updated_at' => '2025-08-04 18:10:33',
            ),
            12 => 
            array (
                'id' => 13,
                'student_academic_year_id' => 6,
                'title' => 'القسط الأول',
                'amount_due' => '166666.67',
                'amount_paid' => '0.00',
                'due_date' => '2025-07-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:22:54',
                'updated_at' => '2025-08-04 18:22:54',
            ),
            13 => 
            array (
                'id' => 14,
                'student_academic_year_id' => 6,
                'title' => 'القسط الثاني',
                'amount_due' => '166666.67',
                'amount_paid' => '0.00',
                'due_date' => '2026-03-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:22:54',
                'updated_at' => '2025-08-04 18:22:54',
            ),
            14 => 
            array (
                'id' => 15,
                'student_academic_year_id' => 6,
                'title' => 'القسط الثالث',
                'amount_due' => '166666.66',
                'amount_paid' => '0.00',
                'due_date' => '2026-11-01',
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => '2025-08-04 18:22:54',
                'updated_at' => '2025-08-04 18:22:54',
            ),
        ));
        
        
    }
}