<?php

namespace App\Http\Controllers;

use App\Models\StudentLedger;
use App\Models\Enrollment;
use App\Models\Student;
use App\Http\Resources\StudentLedgerResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        $enrollment = Enrollment::with(['student', 'classroom.gradeLevel', 'classroom.school'])
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
}
