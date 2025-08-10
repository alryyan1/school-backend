<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\School;
use Illuminate\Console\Command;

class TestStudentFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:student-factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the StudentFactory with various methods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing StudentFactory methods...');

        // Check if schools exist
        if (School::count() === 0) {
            $this->error('No schools found. Please run SchoolSeeder first.');
            return 1;
        }

        $school = School::first();

        // Test basic factory
        $student1 = Student::factory()->create();
        $this->info("✓ Basic student created: {$student1->student_name}");

        // Test approved factory
        $student2 = Student::factory()->approved()->create();
        $this->info("✓ Approved student created: {$student2->student_name} (Approved: " . ($student2->approved ? 'Yes' : 'No') . ")");

        // Test pending factory
        $student3 = Student::factory()->pending()->create();
        $this->info("✓ Pending student created: {$student3->student_name} (Approved: " . ($student3->approved ? 'Yes' : 'No') . ")");

        // Test with specific wished school
        $student4 = Student::factory()->withWishedSchool($school)->create();
        $this->info("✓ Student with wished school created: {$student4->student_name} (School ID: {$student4->wished_school})");

        // Test without wished school
        $student5 = Student::factory()->withoutWishedSchool()->create();
        $this->info("✓ Student without wished school created: {$student5->student_name} (School ID: " . ($student5->wished_school ?? 'null') . ")");

        // Test male student
        $student6 = Student::factory()->male()->create();
        $this->info("✓ Male student created: {$student6->student_name} (Gender: {$student6->gender})");

        // Test female student
        $student7 = Student::factory()->female()->create();
        $this->info("✓ Female student created: {$student7->student_name} (Gender: {$student7->gender})");

        // Test combination
        $student8 = Student::factory()
            ->approved()
            ->male()
            ->withWishedSchool($school)
            ->create();
        $this->info("✓ Combined student created: {$student8->student_name} (Approved: " . ($student8->approved ? 'Yes' : 'No') . ", Gender: {$student8->gender}, School ID: {$student8->wished_school})");

        $this->info('All StudentFactory tests completed successfully!');
        return 0;
    }
}
