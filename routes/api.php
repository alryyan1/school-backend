<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AcademicYearSubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentAcademicYearController;
use App\Http\Controllers\StudentFeePaymentController;
use App\Http\Controllers\StudentTransportAssignmentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['index']);

    // Special endpoint for admins only
    Route::get('users', [UserController::class, 'index'])
        ->middleware('can:viewAny,App\Models\User');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);


Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
});

// routes/api.php

Route::middleware('auth:sanctum')->get('/dashboard-stats', function () {
    $studentCount = \App\Models\Student::count();
    $teacherCount = \App\Models\Teacher::count();
    // $courseCount = \App\Models\Course::count();

    return response()->json([
        'studentCount' => $studentCount,
        'teacherCount' => $teacherCount,
        'courseCount' => 10,
    ]);
});
// routes/api.php


Route::middleware('auth:sanctum')->get('/auth/verify', [VerificationController::class, 'verify']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/students', StudentController::class);
    // --- School Grade Level Assignment Routes ---
    Route::put('/schools/{school}/grade-levels', [SchoolController::class, 'updateAssignedGradeLevels']);
    Route::get('/schools/{school}/grade-levels', [SchoolController::class, 'getAssignedGradeLevels'])->name('schools.grades.index');
    Route::post('/schools/{school}/grade-levels', [SchoolController::class, 'attachGradeLevels'])->name('schools.grades.attach');
    Route::put('/schools/{school}/grade-levels/{grade_level}', [SchoolController::class, 'updateGradeLevelFee'])->name('schools.grades.updateFee');
    Route::delete('/schools/{school}/grade-levels/{grade_level}', [SchoolController::class, 'detachGradeLevel'])->name('schools.grades.detach');
    // --- End Assignment Routes ---

    Route::apiResource('/teachers', TeacherController::class);
    Route::apiResource('/schools', SchoolController::class);
    // --- ACADEMIC YEAR ROUTES ---
    Route::apiResource('/academic-years', AcademicYearController::class); // <-- Add this
    // --- GRADE LEVEL ROUTES ---
    Route::apiResource('/grade-levels', GradeLevelController::class); // <-- Add this
    // --- SUBJECT ROUTES ---
    Route::apiResource('/subjects', SubjectController::class); // <-- Add this
    // --- ACADEMIC YEAR SUBJECT ROUTES ---
    // Usually accessed via index with filters, but include all methods for flexibility
    Route::apiResource('/academic-year-subjects', AcademicYearSubjectController::class);
    Route::get('/teachers/{teacher}/subjects', [TeacherController::class, 'getSubjects']); // Get assigned subjects
    Route::post('/students/{student}/photo', [StudentController::class, 'updatePhoto'])
        ->name('students.updatePhoto'); // Optional: Give it 
    Route::put('/teachers/{teacher}/subjects', [TeacherController::class, 'updateSubjects']); // Update assigned subjects
    // --- CLASSROOM ROUTES ---
    Route::apiResource('/classrooms', ClassroomController::class); // <-- Add this
    Route::apiResource('/student-enrollments', StudentAcademicYearController::class);
    // --- STUDENT ENROLLMENT ROUTES ---
    Route::get('/enrollable-students', [StudentAcademicYearController::class, 'getEnrollableStudents']);
    Route::get('search', [StudentAcademicYearController::class, 'search']);

    // --- STUDENT FEE PAYMENT ROUTES ---
    Route::apiResource('/student-fee-payments', StudentFeePaymentController::class);
    // --- EXAM ROUTES ---
    Route::apiResource('/exams', ExamController::class);


    // --- USER MANAGEMENT ROUTES ---
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword'); // Change password
    Route::apiResource('users', UserController::class); // Standard CRUD (index requires policy)
    // --- EXAM SCHEDULE ROUTES ---
    Route::apiResource('/exam-schedules', ExamScheduleController::class); // <-- Add this

    // --- TRANSPORTATION ROUTES ---
    Route::apiResource('/transport-routes', TransportRouteController::class);
    Route::apiResource('/student-transport-assignments', StudentTransportAssignmentController::class)->except(['show']); // show 
});
