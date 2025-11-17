<?php

namespace App\Http\Controllers;

use App\Models\StudentLedger;
use App\Models\StudentLedgerDeletion;
use App\Models\Enrollment;
use App\Models\Student;
use App\Http\Resources\StudentLedgerResource;
use App\Helpers\StudentLedgerPdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StudentLedgerController extends Controller
{
    /**
     * Display the ledger for a specific student enrollment.
     */
    public function show(Request $request, $enrollmentId): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $enrollment = Enrollment::with(['student', 'school', 'gradeLevel', 'classroom'])
            ->findOrFail($enrollmentId);

        $query = StudentLedger::where('enrollment_id', $enrollmentId)
            ->with(['createdBy'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $ledgerEntries = $query->get();
        $currentBalance = StudentLedger::getCurrentBalance($enrollmentId);

        return response()->json([
            'enrollment' => $enrollment,
            'ledger_entries' => StudentLedgerResource::collection($ledgerEntries),
            'current_balance' => $currentBalance,
            'summary' => [
                'total_fees' => $ledgerEntries->where('transaction_type', 'fee')->sum('amount'),
                'total_payments' => $ledgerEntries->where('transaction_type', 'payment')->sum('amount'),
                'total_discounts' => $ledgerEntries->where('transaction_type', 'discount')->sum('amount'),
                'total_refunds' => $ledgerEntries->where('transaction_type', 'refund')->sum('amount'),
                'total_adjustments' => $ledgerEntries->where('transaction_type', 'adjustment')->sum('amount'),
            ]
        ]);
    }

    /**
     * Add a new ledger entry.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'transaction_type' => ['required', Rule::in([
                StudentLedger::TYPE_FEE,
                StudentLedger::TYPE_PAYMENT,
                StudentLedger::TYPE_DISCOUNT,
                StudentLedger::TYPE_REFUND,
                StudentLedger::TYPE_ADJUSTMENT,
            ])],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'payment_method' => ['nullable', Rule::in([
                StudentLedger::PAYMENT_METHOD_CASH,
                StudentLedger::PAYMENT_METHOD_BANAK,
                StudentLedger::PAYMENT_METHOD_FAWRI,
                StudentLedger::PAYMENT_METHOD_OCASH,
            ])],
            'metadata' => 'nullable|array',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        try {
            DB::beginTransaction();

            $ledgerEntry = StudentLedger::addEntry([
                'enrollment_id' => $request->enrollment_id,
                'student_id' => $enrollment->student_id,
                'transaction_type' => $request->transaction_type,
                'description' => $request->description,
                'amount' => abs($request->amount), // Always store positive amounts, balance calculation handled in model
                'transaction_date' => $request->transaction_date,
                'reference_number' => $request->reference_number,
                'payment_method' => $request->payment_method,
                'metadata' => $request->metadata,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Ledger entry added successfully',
                'ledger_entry' => new StudentLedgerResource($ledgerEntry),
                'new_balance' => $ledgerEntry->balance_after,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add ledger entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ledger summary for multiple enrollments.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = StudentLedger::whereIn('enrollment_id', $request->enrollment_ids);

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $summary = $query->selectRaw('
                enrollment_id,
                SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END) as total_fees,
                SUM(CASE WHEN transaction_type = "payment" THEN ABS(amount) ELSE 0 END) as total_payments,
                SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END) as total_discounts,
                SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END) as total_refunds,
                SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as total_adjustments
            ')
            ->groupBy('enrollment_id')
            ->get();

        return response()->json([
            'summary' => $summary,
            'grand_total' => [
                'fees' => $summary->sum('total_fees'),
                'payments' => $summary->sum('total_payments'),
                'discounts' => $summary->sum('total_discounts'),
                'refunds' => $summary->sum('total_refunds'),
                'adjustments' => $summary->sum('total_adjustments'),
            ]
        ]);
    }

    /**
     * Get ledger entries for a specific student across all enrollments.
     */
    public function studentLedger(Request $request, $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);

        $query = StudentLedger::where('student_id', $studentId)
            ->with(['enrollment.classroom.gradeLevel', 'enrollment.classroom.school', 'createdBy'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $ledgerEntries = $query->paginate(50);

        return response()->json([
            'student' => $student,
            'ledger_entries' => StudentLedgerResource::collection($ledgerEntries),
            'pagination' => [
                'current_page' => $ledgerEntries->currentPage(),
                'last_page' => $ledgerEntries->lastPage(),
                'per_page' => $ledgerEntries->perPage(),
                'total' => $ledgerEntries->total(),
            ]
        ]);
    }

    /**
     * Get ledger entries filtered by payment method and date range.
     */
    public function byPaymentMethod(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_method' => ['required', Rule::in([
                    StudentLedger::PAYMENT_METHOD_CASH,
                    StudentLedger::PAYMENT_METHOD_BANAK,
                    StudentLedger::PAYMENT_METHOD_FAWRI,
                    StudentLedger::PAYMENT_METHOD_OCASH,
                ])],
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $query = StudentLedger::where('payment_method', $request->payment_method)
                ->with(['enrollment.student', 'enrollment.school', 'enrollment.gradeLevel', 'enrollment.classroom', 'createdBy'])
                ->orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc');

            if ($request->start_date) {
                $query->where('transaction_date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('transaction_date', '<=', $request->end_date);
            }

            $ledgerEntries = $query->get();

            $totalAmount = $ledgerEntries->sum('amount');

            return response()->json([
                'data' => StudentLedgerResource::collection($ledgerEntries),
                'total' => $ledgerEntries->count(),
                'summary' => [
                    'total_amount' => $totalAmount,
                    'total_entries' => $ledgerEntries->count(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in byPaymentMethod: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'message' => 'Failed to fetch ledger entries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for student ledger.
     */
    public function generatePdf(Request $request, $enrollmentId)
    {
        $enrollment = Enrollment::with(['student', 'school', 'gradeLevel', 'classroom'])
            ->findOrFail($enrollmentId);

        $query = StudentLedger::where('enrollment_id', $enrollmentId)
            ->with(['createdBy'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $ledgerEntries = $query->get();
        $currentBalance = StudentLedger::getCurrentBalance($enrollmentId);

        $summary = [
            'total_fees' => $ledgerEntries->where('transaction_type', 'fee')->sum('amount'),
            'total_payments' => $ledgerEntries->where('transaction_type', 'payment')->sum('amount'),
            'total_discounts' => $ledgerEntries->where('transaction_type', 'discount')->sum('amount'),
            'total_refunds' => $ledgerEntries->where('transaction_type', 'refund')->sum('amount'),
            'total_adjustments' => $ledgerEntries->where('transaction_type', 'adjustment')->sum('amount'),
        ];

        // Create PDF
        $pdf = new StudentLedgerPdf($enrollment, $ledgerEntries, $summary, $currentBalance);
        $pdf->generateLedger();

        // Generate filename
        $filename = 'student_ledger_' . ($enrollment->student->student_name ?? 'unknown') . '_' . date('Y-m-d') . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        // Output PDF
        $pdf->Output($filename, 'I');
    }

    /**
     * Delete a ledger entry and log it.
     */
    public function destroy(Request $request, $ledgerEntryId): JsonResponse
    {
        $request->validate([
            'deletion_reason' => 'required|string|max:500',
        ]);

        $ledgerEntry = StudentLedger::with(['createdBy'])->findOrFail($ledgerEntryId);

        try {
            DB::beginTransaction();

            // Log the deletion with all original data
            StudentLedgerDeletion::create([
                'ledger_entry_id' => $ledgerEntry->id,
                'enrollment_id' => $ledgerEntry->enrollment_id,
                'student_id' => $ledgerEntry->student_id,
                'transaction_type' => $ledgerEntry->transaction_type,
                'description' => $ledgerEntry->description,
                'amount' => $ledgerEntry->amount,
                'transaction_date' => $ledgerEntry->transaction_date,
                'balance_before' => $ledgerEntry->balance_before ?? 0,
                'balance_after' => $ledgerEntry->balance_after ?? 0,
                'reference_number' => $ledgerEntry->reference_number,
                'payment_method' => $ledgerEntry->payment_method,
                'metadata' => $ledgerEntry->metadata,
                'original_created_by' => $ledgerEntry->created_by,
                'original_created_at' => $ledgerEntry->created_at,
                'deleted_by' => Auth::id(),
                'deletion_reason' => $request->deletion_reason,
                'deleted_at' => now(),
            ]);

            // Delete the entry
            $ledgerEntry->delete();

            // Note: No recalculation/update on student_ledgers as requested

            DB::commit();

            return response()->json([
                'message' => 'Ledger entry deleted successfully',
                'new_balance' => StudentLedger::getCurrentBalance($ledgerEntry->enrollment_id),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete ledger entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate balances for all entries in an enrollment.
     */
    private function recalculateBalances($enrollmentId): void
    {
        $entries = StudentLedger::where('enrollment_id', $enrollmentId)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $runningBalance = 0;

        foreach ($entries as $entry) {
            $entry->balance_before = $runningBalance;

            // Determine impact on balance
            switch ($entry->transaction_type) {
                case StudentLedger::TYPE_FEE:
                    $runningBalance += $entry->amount;
                    break;
                case StudentLedger::TYPE_PAYMENT:
                case StudentLedger::TYPE_DISCOUNT:
                case StudentLedger::TYPE_REFUND:
                    $runningBalance -= $entry->amount;
                    break;
                case StudentLedger::TYPE_ADJUSTMENT:
                    // For adjustments, check metadata or assume additive
                    $runningBalance += $entry->amount;
                    break;
            }

            $entry->balance_after = $runningBalance;
            $entry->save();
        }
    }

    /**
     * Generate PDF for ledger entries by payment method.
     */
    public function generatePdfByPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', Rule::in([
                StudentLedger::PAYMENT_METHOD_CASH,
                StudentLedger::PAYMENT_METHOD_BANAK,
                StudentLedger::PAYMENT_METHOD_FAWRI,
                StudentLedger::PAYMENT_METHOD_OCASH,
            ])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = StudentLedger::where('payment_method', $request->payment_method)
            ->with(['enrollment.student', 'enrollment.school', 'enrollment.gradeLevel', 'enrollment.classroom', 'createdBy'])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $ledgerEntries = $query->get();

        // Calculate running balance
        $runningBalance = 0;
        $entriesWithBalance = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
            $amount = (float) $entry->amount;
            if ($entry->transaction_type === 'fee') {
                $runningBalance += $amount;
            } else if ($entry->transaction_type === 'payment') {
                $runningBalance += $amount;
            } else if ($entry->transaction_type === 'discount') {
                $runningBalance -= $amount;
            } else if ($entry->transaction_type === 'refund') {
                $runningBalance -= $amount;
            } else if ($entry->transaction_type === 'adjustment') {
                $runningBalance += $amount;
            }
            $entry->running_balance = $runningBalance;
            return $entry;
        });

        // Create PDF
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('دفتر الحسابات - ' . $this->translatePaymentMethod($request->payment_method));
        $pdf->SetSubject('تقرير دفتر الحسابات حسب طريقة الدفع');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        // Set RTL
        $pdf->setRTL(true);

        // Set font
        $pdf->SetFont('dejavusans', '', 10);

        // Title
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, 'دفتر الحسابات - ' . $this->translatePaymentMethod($request->payment_method), 0, 1, 'C');
        $pdf->SetFont('dejavusans', '', 10);
        
        // Date range
        if ($request->start_date && $request->end_date) {
            $pdf->Cell(0, 5, 'من تاريخ: ' . $request->start_date . ' إلى تاريخ: ' . $request->end_date, 0, 1, 'C');
        }
        $pdf->Ln(5);

        // Table header
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(25, 8, 'التاريخ', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'النوع', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'الوصف', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'المبلغ', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'رصيد الحساب', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'رقم المرجع', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'اسم الطالب', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'تم الإنشاء بواسطة', 1, 1, 'C', true);

        // Table rows
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->SetFillColor(255, 255, 255);
        foreach ($entriesWithBalance as $entry) {
            $pdf->Cell(25, 6, date('Y/m/d', strtotime($entry->transaction_date)), 1, 0, 'C');
            $pdf->Cell(20, 6, $this->translateTransactionType($entry->transaction_type), 1, 0, 'C');
            $pdf->Cell(60, 6, mb_substr($entry->description, 0, 30), 1, 0, 'R');
            $pdf->Cell(30, 6, number_format($entry->amount, 2) . ' جنيه', 1, 0, 'C');
            $pdf->Cell(30, 6, number_format($entry->running_balance, 2) . ' جنيه', 1, 0, 'C');
            $pdf->Cell(30, 6, $entry->reference_number ?? '-', 1, 0, 'C');
            $pdf->Cell(50, 6, mb_substr($entry->enrollment->student->student_name ?? '-', 0, 20), 1, 0, 'R');
            $pdf->Cell(40, 6, mb_substr($entry->createdBy->name ?? '-', 0, 15), 1, 1, 'R');
        }

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(0, 8, 'إجمالي المعاملات: ' . $ledgerEntries->count(), 0, 1, 'R');
        $pdf->Cell(0, 8, 'إجمالي المبلغ: ' . number_format($ledgerEntries->sum('amount'), 2) . ' جنيه', 0, 1, 'R');
        $pdf->Cell(0, 8, 'الرصيد النهائي: ' . number_format($runningBalance, 2) . ' جنيه', 0, 1, 'R');

        // Output PDF
        $filename = 'ledger_payment_method_' . $request->payment_method . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'I');
    }

    /**
     * Export ledger entries by payment method to Excel.
     */
    public function exportExcelByPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', Rule::in([
                StudentLedger::PAYMENT_METHOD_CASH,
                StudentLedger::PAYMENT_METHOD_BANAK,
                StudentLedger::PAYMENT_METHOD_FAWRI,
                StudentLedger::PAYMENT_METHOD_OCASH,
            ])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = StudentLedger::where('payment_method', $request->payment_method)
            ->with(['enrollment.student', 'enrollment.school', 'enrollment.gradeLevel', 'enrollment.classroom', 'createdBy'])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $ledgerEntries = $query->get();

        // Calculate running balance
        $runningBalance = 0;
        $entriesWithBalance = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
            $amount = (float) $entry->amount;
            if ($entry->transaction_type === 'fee') {
                $runningBalance += $amount;
            } else if ($entry->transaction_type === 'payment') {
                $runningBalance += $amount;
            } else if ($entry->transaction_type === 'discount') {
                $runningBalance -= $amount;
            } else if ($entry->transaction_type === 'refund') {
                $runningBalance -= $amount;
            } else if ($entry->transaction_type === 'adjustment') {
                $runningBalance += $amount;
            }
            $entry->running_balance = $runningBalance;
            return $entry;
        });

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('دفتر الحسابات');

        // Workbook defaults & RTL
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('دفتر الحسابات - ' . $this->translatePaymentMethod($request->payment_method))
            ->setSubject('تقرير دفتر الحسابات حسب طريقة الدفع');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->setRightToLeft(true);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Title
        $sheet->setCellValue('A1', 'دفتر الحسابات - ' . $this->translatePaymentMethod($request->payment_method));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Date range
        if ($request->start_date && $request->end_date) {
            $sheet->setCellValue('A2', 'من تاريخ: ' . $request->start_date . ' إلى تاريخ: ' . $request->end_date);
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Headers
        $headers = ['التاريخ', 'النوع', 'الوصف', 'المبلغ', 'رصيد الحساب', 'رقم المرجع', 'اسم الطالب', 'تم الإنشاء بواسطة'];
        $colIndex = 1; // Start from column A (1 = A, 2 = B, etc.)
        $row = 4;
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . $row, $header);
            $sheet->getStyle($colLetter . $row)->getFont()->setBold(true);
            $sheet->getStyle($colLetter . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('C0C0C0');
            $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            $colIndex++;
        }

        // Data rows
        $row = 5;
        foreach ($entriesWithBalance as $entry) {
            $colIndex = 1; // Start from column A (1 = A, 2 = B, etc.)
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, date('Y/m/d', strtotime($entry->transaction_date)));
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, $this->translateTransactionType($entry->transaction_type));
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, $entry->description);
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, number_format($entry->amount, 2));
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, number_format($entry->running_balance, 2));
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, $entry->reference_number ?? '-');
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, $entry->enrollment->student->student_name ?? '-');
            $sheet->setCellValueByColumnAndRow($colIndex++, $row, $entry->createdBy->name ?? '-');
            $row++;
        }

        // Summary
        $row++;
        $sheet->setCellValue('D' . $row, 'إجمالي المعاملات:');
        $sheet->setCellValue('E' . $row, $ledgerEntries->count());
        $row++;
        $sheet->setCellValue('D' . $row, 'إجمالي المبلغ:');
        $sheet->setCellValue('E' . $row, number_format($ledgerEntries->sum('amount'), 2) . ' جنيه');
        $row++;
        $sheet->setCellValue('D' . $row, 'الرصيد النهائي:');
        $sheet->setCellValue('E' . $row, number_format($runningBalance, 2) . ' جنيه');
        $sheet->getStyle('D' . ($row - 2) . ':E' . $row)->getFont()->setBold(true);

        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        $filename = 'ledger_payment_method_' . $request->payment_method . '_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Translate payment method to Arabic.
     */
    private function translatePaymentMethod($method): string
    {
        $translations = [
            'cash' => 'نقداً',
            'bankak' => 'بنكك',
            'Fawri' => 'فوري',
            'OCash' => 'أوكاش',
        ];
        return $translations[$method] ?? $method;
    }

    /**
     * Translate transaction type to Arabic.
     */
    private function translateTransactionType($type): string
    {
        $translations = [
            'fee' => 'رسوم',
            'payment' => 'دفع',
            'discount' => 'خصم',
            'refund' => 'استرداد',
            'adjustment' => 'تعديل',
        ];
        return $translations[$type] ?? $type;
    }
}
