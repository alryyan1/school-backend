<?php

use App\Http\Controllers\FeeInstallmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeePaymentController;
use App\Http\Controllers\StudentWarningController;
use App\Http\Controllers\StudentNoteController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;

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
   
Route::get('/enrollments/{enrollment}/fee-statement-pdf', [FeeInstallmentController::class, 'generateStatementPdf'])
->name('enrollments.fees.pdf'); // Route name for easy linking
 // --- NEW ROUTE for Installment Payment Details PDF ---
 Route::get('/fee-installments/{feeInstallment}/payments-pdf', [StudentFeePaymentController::class, 'generatePaymentsPdf'])
 ->name('installments.payments.pdf');

    // --- Route for Student List PDF ---
    Route::get('/reports/students/list-pdf', [StudentController::class, 'generateListPdf'])
         ->name('reports.students.list.pdf');
    Route::get('/reports/students/list-excel', [StudentController::class, 'exportStudentListExcel'])
         ->name('reports.students.list.excel');

    // --- Route for Terms & Conditions PDF ---
    Route::get('/reports/terms-and-conditions', [StudentController::class, 'generateTermsAndConditionsPdf'])
         ->name('reports.terms.pdf');

    // --- Student Warning Notice PDF (web route for direct access) ---
    Route::get('/student-warnings/{studentWarning}/pdf', [StudentWarningController::class, 'generatePdf'])
         ->name('student-warnings.web.pdf');

    // --- Student Ledger PDF (web route for direct access) ---
    Route::get('/student-ledgers/enrollment/{enrollmentId}/pdf', [\App\Http\Controllers\StudentLedgerController::class, 'generatePdf'])
         ->name('student-ledgers.pdf');

    // --- Student Deportation Ledger PDF (web route for direct access) ---
    Route::get('/student-deportation-ledgers/enrollment/{enrollmentId}/pdf', [\App\Http\Controllers\StudentDeportationLedgerController::class, 'generatePdf'])
         ->name('student-deportation-ledgers.pdf');

    // --- Ledger by Payment Method PDF and Excel ---
    Route::get('/student-ledgers/by-payment-method/pdf', [\App\Http\Controllers\StudentLedgerController::class, 'generatePdfByPaymentMethod'])
         ->name('student-ledgers.by-payment-method.pdf');
    Route::get('/student-ledgers/by-payment-method/excel', [\App\Http\Controllers\StudentLedgerController::class, 'exportExcelByPaymentMethod'])
         ->name('student-ledgers.by-payment-method.excel');

    // --- Revenues PDF (web route to open in new tab) ---
    Route::get('/reports/revenues', [StudentController::class, 'revenuesPdfWeb'])
         ->name('reports.revenues.pdf');

    // --- Revenues Excel (web route to download Excel file) ---
    Route::get('/reports/revenues-excel', [StudentController::class, 'exportRevenuesExcel'])
         ->name('reports.revenues.excel');

    // --- Deportation Revenues PDF (web route to open in new tab) ---
    Route::get('/reports/deportation-revenues', [StudentController::class, 'deportationRevenuesPdf'])
         ->name('reports.deportation-revenues.pdf');

    // --- Deportation Revenues Excel (web route to download Excel file) ---
    Route::get('/reports/deportation-revenues-excel', [StudentController::class, 'exportDeportationRevenuesExcel'])
         ->name('reports.deportation-revenues.excel');

    // --- Expenses PDF (web route to open in new tab) ---
    Route::get('/reports/expenses', [ExpenseController::class, 'pdfWeb'])
         ->name('reports.expenses.pdf');

    // --- Expenses Excel (web route to download Excel file) ---
    Route::get('/reports/expenses-excel', [ExpenseController::class, 'exportExcel'])
         ->name('reports.expenses.excel');

    // --- Other Revenues PDF (web route to open in new tab) ---
    Route::get('/reports/other-revenues', [\App\Http\Controllers\OtherRevenueController::class, 'pdfWeb'])
         ->name('reports.other-revenues.pdf');

    // --- Other Revenues Excel (web route to download Excel file) ---
    Route::get('/reports/other-revenues-excel', [\App\Http\Controllers\OtherRevenueController::class, 'exportExcel'])
         ->name('reports.other-revenues.excel');

    // --- Student Enrollment Notes PDF (web route to open in new tab) ---
    Route::get('/student-notes/pdf', [StudentNoteController::class, 'generatePdf'])
         ->name('student-notes.web.pdf');

    // --- Teacher Profile PDF (web route to open in new tab) ---
    Route::get('/teachers/{teacher}/pdf', [TeacherController::class, 'pdfWeb'])
         ->name('teachers.web.pdf');