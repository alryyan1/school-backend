<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School; // Import the School model
use Illuminate\Support\Facades\DB; // Optional: For truncate

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear the table first if you want to restart
        // DB::table('schools')->delete(); // Simple delete
        // School::truncate(); // Faster, but resets auto-increment and needs FK checks disabled sometimes

        $schools = [
            [
                'name' => 'مدرسة النور النموذجية',
                'code' => 'SCH-NUR-01',
                'address' => 'شارع الجمهورية، المدينة المنورة',
                'phone' => '0148223344',
                'email' => 'info@alnoor-schools.edu.sa',
                'principal_name' => 'أحمد عبد الله',
                'establishment_date' => '1995-09-01',
                'logo' => null, // Add path like 'school_logos/noor_logo.png' if you have one
            ],
            [
                'name' => 'مدارس الأمل الأهلية',
                'code' => 'SCH-AML-01',
                'address' => 'حي السلام، الرياض',
                'phone' => '0114556677',
                'email' => 'contact@alamal-schools.com',
                'principal_name' => 'فاطمة الزهراء',
                'establishment_date' => '2002-08-15',
                'logo' => null,
            ],
            // Add more schools if needed
        ];

        // Insert data using firstOrCreate to avoid duplicates if seeder runs again
        foreach ($schools as $schoolData) {
            School::firstOrCreate(
                ['code' => $schoolData['code']], // Check based on unique code
                $schoolData // Data to insert or merge if found
            );
        }
    }
}