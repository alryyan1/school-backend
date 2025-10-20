<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\EnrollmentLog;
use App\Models\GradeLevel;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnrollmentLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that grade level changes are logged.
     */
    public function test_grade_level_change_is_logged(): void
    {
        // Create test data
        $user = User::factory()->create();
        $school = School::factory()->create();
        $student = Student::factory()->create(['wished_school' => $school->id]);
        $oldGradeLevel = GradeLevel::factory()->create();
        $newGradeLevel = GradeLevel::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'grade_level_id' => $oldGradeLevel->id,
            'academic_year' => '2025/2026',
            'status' => 'active',
        ]);

        // Act as the user
        $this->actingAs($user);

        // Update the enrollment grade level
        $response = $this->putJson("/api/enrollments/{$enrollment->id}", [
            'grade_level_id' => $newGradeLevel->id,
        ]);

        // Assert the update was successful
        $response->assertStatus(200);

        // Assert that a log entry was created
        $this->assertDatabaseHas('enrollment_logs', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'user_id' => $user->id,
            'action_type' => 'grade_level_change',
            'field_name' => 'grade_level_id',
            'old_value' => (string) $oldGradeLevel->id,
            'new_value' => (string) $newGradeLevel->id,
        ]);

        // Assert the log entry has the correct description
        $log = EnrollmentLog::where('enrollment_id', $enrollment->id)->first();
        $this->assertStringContainsString($oldGradeLevel->name, $log->description);
        $this->assertStringContainsString($newGradeLevel->name, $log->description);
    }

    /**
     * Test that status changes are logged.
     */
    public function test_status_change_is_logged(): void
    {
        // Create test data
        $user = User::factory()->create();
        $school = School::factory()->create();
        $student = Student::factory()->create(['wished_school' => $school->id]);
        $gradeLevel = GradeLevel::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'grade_level_id' => $gradeLevel->id,
            'academic_year' => '2025/2026',
            'status' => 'active',
        ]);

        // Act as the user
        $this->actingAs($user);

        // Update the enrollment status
        $response = $this->putJson("/api/enrollments/{$enrollment->id}", [
            'status' => 'transferred',
        ]);

        // Assert the update was successful
        $response->assertStatus(200);

        // Assert that a log entry was created
        $this->assertDatabaseHas('enrollment_logs', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'user_id' => $user->id,
            'action_type' => 'status_change',
            'field_name' => 'status',
            'old_value' => 'active',
            'new_value' => 'transferred',
        ]);
    }

    /**
     * Test that enrollment logs can be retrieved.
     */
    public function test_enrollment_logs_can_be_retrieved(): void
    {
        // Create test data
        $user = User::factory()->create();
        $school = School::factory()->create();
        $student = Student::factory()->create(['wished_school' => $school->id]);
        $gradeLevel = GradeLevel::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'grade_level_id' => $gradeLevel->id,
            'academic_year' => '2025/2026',
            'status' => 'active',
        ]);

        // Create a log entry
        EnrollmentLog::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => $student->id,
            'user_id' => $user->id,
            'action_type' => 'grade_level_change',
            'field_name' => 'grade_level_id',
            'old_value' => '1',
            'new_value' => '2',
            'description' => 'Test change',
            'changed_at' => now(),
        ]);

        // Act as the user
        $this->actingAs($user);

        // Get enrollment logs
        $response = $this->getJson("/api/enrollments/{$enrollment->id}/logs");

        // Assert the response is successful
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'enrollment_id',
                    'student_id',
                    'user_id',
                    'action_type',
                    'field_name',
                    'old_value',
                    'new_value',
                    'description',
                    'changed_at',
                    'user' => [
                        'id',
                        'name',
                    ],
                ],
            ],
            'enrollment',
        ]);
    }
}
