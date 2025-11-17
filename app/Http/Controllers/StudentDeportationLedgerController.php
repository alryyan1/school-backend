<?php

namespace App\Http\Controllers;

use App\Models\StudentDeportationLedger;
use App\Models\Enrollment;
use App\Models\Student;
use App\Http\Resources\StudentDeportationLedgerResource;
use App\Helpers\StudentDeportationLedgerPdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentDeportationLedgerController extends Controller
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

        $query = StudentDeportationLedger::where('enrollment_id', $enrollmentId)
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
        $currentBalance = StudentDeportationLedger::getCurrentBalance($enrollmentId);

        return response()->json([
            'enrollment' => $enrollment,
            'ledger_entries' => StudentDeportationLedgerResource::collection($ledgerEntries),
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
                StudentDeportationLedger::TYPE_FEE,
                StudentDeportationLedger::TYPE_PAYMENT,
                StudentDeportationLedger::TYPE_DISCOUNT,
                StudentDeportationLedger::TYPE_REFUND,
                StudentDeportationLedger::TYPE_ADJUSTMENT,
            ])],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'payment_method' => ['nullable', Rule::in([
                StudentDeportationLedger::PAYMENT_METHOD_CASH,
                StudentDeportationLedger::PAYMENT_METHOD_BANAK,
                StudentDeportationLedger::PAYMENT_METHOD_FAWRI,
                StudentDeportationLedger::PAYMENT_METHOD_OCASH,
            ])],
            'metadata' => 'nullable|array',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        try {
            DB::beginTransaction();

            $ledgerEntry = StudentDeportationLedger::addEntry([
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
                'ledger_entry' => new StudentDeportationLedgerResource($ledgerEntry),
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

        $query = StudentDeportationLedger::whereIn('enrollment_id', $request->enrollment_ids);

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $summary = $query->selectRaw('
                enrollment_id,
                COALESCE(SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END), 0) as total_fees,
                COALESCE(SUM(CASE WHEN transaction_type = "payment" THEN ABS(amount) ELSE 0 END), 0) as total_payments,
                COALESCE(SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END), 0) as total_discounts,
                COALESCE(SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END), 0) as total_refunds,
                COALESCE(SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END), 0) as total_adjustments
            ')
            ->groupBy('enrollment_id')
            ->get();

        // Ensure all requested enrollment_ids are in the response, even if they have no ledger entries
        $summaryMap = $summary->keyBy('enrollment_id');
        $result = [];
        foreach ($request->enrollment_ids as $enrollmentId) {
            if ($summaryMap->has($enrollmentId)) {
                $result[] = $summaryMap->get($enrollmentId);
            } else {
                $result[] = (object)[
                    'enrollment_id' => (int)$enrollmentId,
                    'total_fees' => 0,
                    'total_payments' => 0,
                    'total_discounts' => 0,
                    'total_refunds' => 0,
                    'total_adjustments' => 0,
                ];
            }
        }

        return response()->json([
            'summary' => $result,
            'grand_total' => [
                'fees' => collect($result)->sum('total_fees'),
                'payments' => collect($result)->sum('total_payments'),
                'discounts' => collect($result)->sum('total_discounts'),
                'refunds' => collect($result)->sum('total_refunds'),
                'adjustments' => collect($result)->sum('total_adjustments'),
            ]
        ]);
    }

    /**
     * Get ledger entries for a specific student across all enrollments.
     */
    public function studentLedger(Request $request, $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);

        $query = StudentDeportationLedger::where('student_id', $studentId)
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
            'ledger_entries' => StudentDeportationLedgerResource::collection($ledgerEntries),
            'pagination' => [
                'current_page' => $ledgerEntries->currentPage(),
                'last_page' => $ledgerEntries->lastPage(),
                'per_page' => $ledgerEntries->perPage(),
                'total' => $ledgerEntries->total(),
            ]
        ]);
    }

    /**
     * Generate PDF for student deportation ledger.
     */
    public function generatePdf(Request $request, $enrollmentId)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $enrollment = Enrollment::with(['student', 'school', 'gradeLevel', 'classroom'])
            ->findOrFail($enrollmentId);

        $query = StudentDeportationLedger::where('enrollment_id', $enrollmentId)
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
        $currentBalance = StudentDeportationLedger::getCurrentBalance($enrollmentId);

        $summary = [
            'total_fees' => $ledgerEntries->where('transaction_type', 'fee')->sum('amount'),
            'total_payments' => $ledgerEntries->where('transaction_type', 'payment')->sum('amount'),
            'total_discounts' => $ledgerEntries->where('transaction_type', 'discount')->sum('amount'),
            'total_refunds' => $ledgerEntries->where('transaction_type', 'refund')->sum('amount'),
            'total_adjustments' => $ledgerEntries->where('transaction_type', 'adjustment')->sum('amount'),
        ];

        // Create PDF using dedicated deportation ledger PDF helper
        $pdf = new StudentDeportationLedgerPdf($enrollment, $ledgerEntries, $summary, $currentBalance);
        $pdf->generateLedger();

        // Generate filename
        $filename = 'student_deportation_ledger_' . ($enrollment->student->student_name ?? 'unknown') . '_' . date('Y-m-d') . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        // Output PDF
        $pdf->Output($filename, 'I');
    }
}


