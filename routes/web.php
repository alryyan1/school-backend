<?php

use App\Http\Controllers\FeeInstallmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeePaymentController;
use App\Http\Controllers\StudentWarningController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route to generate PDF for a specific student
Route::get('/students/{student}/pdf', [StudentController::class, 'generatePdf']);
   
Route::get('/enrollments/{studentAcademicYear}/fee-statement-pdf', [FeeInstallmentController::class, 'generateStatementPdf'])
->name('enrollments.fees.pdf'); // Route name for easy linking
 // --- NEW ROUTE for Installment Payment Details PDF ---
 Route::get('/fee-installments/{feeInstallment}/payments-pdf', [StudentFeePaymentController::class, 'generatePaymentsPdf'])
 ->name('installments.payments.pdf');

    // --- Route for Student List PDF ---
    Route::get('/reports/students/list-pdf', [StudentController::class, 'generateListPdf'])
         ->name('reports.students.list.pdf');

    // --- Route for Terms & Conditions PDF ---
    Route::get('/reports/terms-and-conditions', [StudentController::class, 'generateTermsAndConditionsPdf'])
         ->name('reports.terms.pdf');

    // --- Student Warning Notice PDF (web route for direct access) ---
    Route::get('/student-warnings/{studentWarning}/pdf', [StudentWarningController::class, 'generatePdf'])
         ->name('student-warnings.pdf');