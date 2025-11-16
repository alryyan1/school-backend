<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AcademicYearSubjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GradeLevelSubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\FeeInstallmentController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SchoolController;

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EnrollmentLogController;
use App\Http\Controllers\StudentFeePaymentController;
use App\Http\Controllers\StudentTransportAssignmentController;
use App\Http\Controllers\StudentWarningController;
use App\Http\Controllers\StudentAbsenceController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentNoteController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\UltramsgController;
use App\Http\Controllers\DeportationPathController;

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

// Publicly accessible user routes (auth removed)
Route::apiResource('users', UserController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

Route::post('/logout', function (Request $request) {
    $token = $request->user()?->currentAccessToken();
    if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
        $token->delete();
    }
    return response()->json(['message' => 'Logged out successfully']);
});

// routes/api.php

Route::get('/dashboard-stats', function () {
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

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/students', StudentController::class);
    Route::get('/revenues-pdf', [StudentController::class, 'revenuesPdf'])->name('revenues.pdf');
    Route::get('/students/search/{id}', [StudentController::class, 'searchById'])->name('students.searchById');
    Route::post('/students/{student}/accept', [StudentController::class, 'accept'])->name('students.accept');
    // --- School Grade Level Assignment Routes ---
    Route::put('/schools/{school}/grade-levels', [SchoolController::class, 'updateAssignedGradeLevels']);
    Route::get('/schools/{school}/grade-levels', [SchoolController::class, 'getAssignedGradeLevels'])->name('schools.grades.index');
    Route::post('/schools/{school}/grade-levels', [SchoolController::class, 'attachGradeLevels'])->name('schools.grades.attach');
    Route::put('/schools/{school}/grade-levels/{grade_level}', [SchoolController::class, 'updateGradeLevelFee'])->name('schools.grades.updateFee');
    Route::delete('/schools/{school}/grade-levels/{grade_level}', [SchoolController::class, 'detachGradeLevel'])->name('schools.grades.detach');
    // --- End Assignment Routes ---

    Route::apiResource('/teachers', TeacherController::class);
    Route::post('/teachers/{teacher}/documents', [TeacherController::class, 'uploadDocuments']);
    Route::get('/teachers/{teacher}/documents', [TeacherController::class, 'listDocuments']);
    Route::apiResource('/schools', SchoolController::class);
    Route::delete('/schools/{school}/user', [SchoolController::class, 'unassignUser'])->name('schools.unassignUser');
    // --- ACADEMIC YEAR ROUTES ---
    Route::apiResource('/academic-years', AcademicYearController::class); // <-- Add this
    // --- GRADE LEVEL ROUTES ---
    Route::apiResource('/grade-levels', GradeLevelController::class); // <-- Add this
    // --- SUBJECT ROUTES ---
    Route::apiResource('/subjects', SubjectController::class); // <-- Add this
    // --- ACADEMIC YEAR SUBJECT ROUTES ---
    // Usually accessed via index with filters, but include all methods for flexibility
    Route::apiResource('/academic-year-subjects', AcademicYearSubjectController::class);
    
    // --- GRADE LEVEL SUBJECT ROUTES ---
    Route::get('/grade-level-subjects/{gradeLevelId}', [GradeLevelSubjectController::class, 'getAllByGradeLevel']);
    Route::post('/grade-level-subjects', [GradeLevelSubjectController::class, 'create']);
    Route::put('/grade-level-subjects/{id}', [GradeLevelSubjectController::class, 'update']);
    Route::delete('/grade-level-subjects/{id}', [GradeLevelSubjectController::class, 'delete']);
    Route::get('/teachers/{teacher}/subjects', [TeacherController::class, 'getSubjects']); // Get assigned subjects
    Route::post('/students/{student}/photo', [StudentController::class, 'updatePhoto'])
        ->name('students.updatePhoto'); // Optional: Give it 
    Route::put('/teachers/{teacher}/subjects', [TeacherController::class, 'updateSubjects']); // Update assigned subjects
    // --- CLASSROOM ROUTES ---
    Route::apiResource('/classrooms', ClassroomController::class); // <-- Add this
    // --- ENROLLMENT ROUTES ---
    Route::apiResource('/enrollments', EnrollmentController::class);
    Route::apiResource('/deportation-paths', DeportationPathController::class);
    Route::get('/enrollable-students', [EnrollmentController::class, 'getEnrollableStudents']);
    Route::get('/enrollments/search', [EnrollmentController::class, 'search']);
    Route::get('/unassigned-students-for-grade', [EnrollmentController::class, 'getUnassignedStudentsForGrade']);
    Route::get('/assigned-students-for-grade', [EnrollmentController::class, 'getAssignedStudentsForGrade']);
    Route::put('/enrollments/{enrollment}/assign-classroom', [EnrollmentController::class, 'assignToClassroom']);
    Route::put('/enrollments/{enrollment}/change-type', [EnrollmentController::class, 'changeEnrollmentType']);
    Route::put('/enrollments/{enrollment}/deportation', [EnrollmentController::class, 'updateDeportation']);
    
    // Enrollment logs routes
    Route::get('/enrollments/{enrollment}/logs', [EnrollmentController::class, 'getLogs']);
    Route::get('/students/{student}/enrollment-logs', [EnrollmentController::class, 'getStudentLogs']);
    
    // General enrollment logs routes
    Route::get('/enrollment-logs', [EnrollmentLogController::class, 'index']);
    Route::get('/enrollment-logs/statistics', [EnrollmentLogController::class, 'statistics']);
    Route::get('/enrollment-logs/{enrollmentLog}', [EnrollmentLogController::class, 'show']);

    // --- STUDENT FEE PAYMENT ROUTES ---
    Route::apiResource('/student-fee-payments', StudentFeePaymentController::class);
    
    // --- STUDENT LEDGER ROUTES ---
Route::prefix('student-ledgers')->group(function () {
    Route::get('/enrollment/{enrollmentId}', [\App\Http\Controllers\StudentLedgerController::class, 'show']);
    Route::post('/', [\App\Http\Controllers\StudentLedgerController::class, 'store']);
    Route::post('/summary', [\App\Http\Controllers\StudentLedgerController::class, 'summary']);
    Route::get('/student/{studentId}', [\App\Http\Controllers\StudentLedgerController::class, 'studentLedger']);
    Route::get('/by-payment-method', [\App\Http\Controllers\StudentLedgerController::class, 'byPaymentMethod']);
    Route::delete('/{ledgerEntryId}', [\App\Http\Controllers\StudentLedgerController::class, 'destroy']);
});

    // --- STUDENT DEPORTATION LEDGER ROUTES ---
Route::prefix('student-deportation-ledgers')->group(function () {
    Route::get('/enrollment/{enrollmentId}', [\App\Http\Controllers\StudentDeportationLedgerController::class, 'show']);
    Route::post('/', [\App\Http\Controllers\StudentDeportationLedgerController::class, 'store']);
    Route::post('/summary', [\App\Http\Controllers\StudentDeportationLedgerController::class, 'summary']);
    Route::get('/student/{studentId}', [\App\Http\Controllers\StudentDeportationLedgerController::class, 'studentLedger']);
});

    // --- STUDENT LEDGER DELETION ROUTES ---
Route::prefix('student-ledger-deletions')->group(function () {
    Route::get('/', [\App\Http\Controllers\StudentLedgerDeletionController::class, 'index']);
    Route::get('/{id}', [\App\Http\Controllers\StudentLedgerDeletionController::class, 'show']);
});
    
    // --- FEE INSTALLMENT ROUTES ---
    Route::apiResource('/fee-installments', FeeInstallmentController::class); // <-- Add this
    // --- Route to get installments due soon ---
    Route::get('/fee-installment/due-soon', [FeeInstallmentController::class, 'getDueSoon'])
         ->name('installments.dueSoon');
    // --- Add route for generating installments ---
    Route::post('/enrollments/{enrollment}/generate-installments', [FeeInstallmentController::class, 'generateInstallments'])
        ->name('enrollments.installments.generate');
    // --- EXAM ROUTES ---
    Route::apiResource('/exams', ExamController::class);


    // --- USER MANAGEMENT ROUTES ---
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword'); // Change password
    Route::apiResource('users', UserController::class); // Standard CRUD (index requires policy)
    Route::post('/users/purge-non-admins', [UserController::class, 'purgeNonAdminUsers'])->name('users.purgeNonAdmins');
    // --- EXAM SCHEDULE ROUTES ---
    Route::apiResource('/exam-schedules', ExamScheduleController::class); // <-- Add this

    // --- TRANSPORTATION ROUTES ---
    Route::apiResource('/transport-routes', TransportRouteController::class);
    Route::apiResource('/student-transport-assignments', StudentTransportAssignmentController::class)->except(['show']); // show 
     // --- Route for sending installment reminder ---
     Route::post('/notify/whatsapp/installment/{feeInstallment}', [NotificationController::class, 'sendInstallmentReminder'])
     ->name('notify.installment.whatsapp');


     Route::apiResource('/payment-methods', \App\Http\Controllers\PaymentMethodController::class)->only(['index','store']);
 
     // --- ROLE ROUTE ---
     Route::get('/roles', [RoleController::class, 'index'])->name('roles.index'); // Get all roles
 
     // --- USER MANAGEMENT ROUTES ---
     Route::get('/curriculum/subjects-for-grade', [AcademicYearSubjectController::class, 'getSubjectsForGradeLevel'])->name('curriculum.subjectsForGrade');

     Route::post('/exams/{exam}/quick-add-schedules', [ExamScheduleController::class, 'quickAddSchedulesForGrade'])->name('exams.schedules.quickAdd'); // <-- New Route
     // --- EXAM RESULT ROUTES ---
     Route::get('/students/{student}/relevant-exams', [ExamController::class, 'getRelevantExamsForStudent'])->name('students.relevantExams');
     Route::get('/exam-schedules/{examSchedule}/results', [ExamResultController::class, 'getResultsForSchedule']);
     Route::get('/exam-schedules/{examSchedule}/pending-students-for-results', [ExamResultController::class, 'getPendingStudentsForResults']);
     Route::post('/exam-schedules/{examSchedule}/results/bulk-upsert', [ExamResultController::class, 'bulkUpsertResults']);
     // Keep these if you want to manage individual results, otherwise, they can be removed
     Route::apiResource('/exam-results', ExamResultController::class)->except(['index', 'store']);

    // --- ROLE & PERMISSION ROUTES ---
    Route::get('/permissions', [RoleController::class, 'getAllPermissions'])->name('permissions.index'); // <-- THIS IS THE ROUTE
    Route::apiResource('/roles', RoleController::class);
    // --- End Role & Permission Routes ---

    Route::apiResource('student-notes', StudentNoteController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('student-notes/pdf', [StudentNoteController::class, 'generatePdf'])->name('student-notes.api.pdf');
    // --- Student Warnings ---
    Route::apiResource('student-warnings', StudentWarningController::class)->only(['index','store','update','destroy']);
    Route::get('student-warnings/{studentWarning}/pdf', [StudentWarningController::class, 'generatePdf'])->name('student-warnings.api.pdf');
    // --- Student Absences ---
    Route::apiResource('student-absences', StudentAbsenceController::class)->only(['index','store','update','destroy']);
    
    // --- EXPENSE ROUTES ---
    Route::apiResource('expenses', ExpenseController::class);
    Route::get('expenses-statistics', [ExpenseController::class, 'statistics'])->name('expenses.statistics');
    Route::get('expenses-pdf', [ExpenseController::class, 'pdf'])->name('expenses.pdf');
    // --- EXPENSE CATEGORY ROUTES ---
    Route::apiResource('expense-categories', ExpenseCategoryController::class);
    Route::get('expense-categories-active', [ExpenseCategoryController::class, 'active'])->name('expense-categories.active');

    // --- WhatsApp (Ultramsg) ---
    Route::post('/whatsapp/send-text', [UltramsgController::class, 'sendText'])->name('whatsapp.sendText');
    Route::post('/whatsapp/send-document', [UltramsgController::class, 'sendDocument'])->name('whatsapp.sendDocument');
    Route::post('/whatsapp/bulk-send-text', [UltramsgController::class, 'bulkSendText'])->name('whatsapp.bulkSendText');
    Route::get('/whatsapp/bulk-send-status/{id}', [UltramsgController::class, 'getBulkSendStatus'])->name('whatsapp.bulkSendStatus');
});
