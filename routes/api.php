<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AcademicYearSubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
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
Route::post('/register', [UserController::class, 'register']);


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

    Route::post('/students/{student}/photo', [StudentController::class, 'updatePhoto'])
    ->name('students.updatePhoto'); // Optional: Give it 
       // --- CLASSROOM ROUTES ---
       Route::apiResource('/classrooms', ClassroomController::class); // <-- Add this
});
