<?php

namespace App\Http\Controllers;

use App\Models\StudentFeePayment;
use App\Models\StudentAcademicYear; // Import
use App\Rules\PaymentExceedTotal;
use Illuminate\Http\Request;
use App\Http\Resources\StudentFeePaymentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentFeePaymentController extends Controller
{
    /**
     * Display a listing of payments for a specific student enrollment.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', StudentFeePayment::class);

        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => 'required|integer|exists:student_academic_years,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'معرف تسجيل الطالب مطلوب', 'errors' => $validator->errors()], 422);
        }

        $payments = StudentFeePayment::where('student_academic_year_id', $request->input('student_academic_year_id'))
            ->orderBy('payment_date', 'desc') // Show most recent first
            ->get();

        return StudentFeePaymentResource::collection($payments);
    }

    /**
     * Store a newly created payment record.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', StudentFeePayment::class);

        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => 'required|integer|exists:student_academic_years,id',
            'amount' => [
                'required',
                new PaymentExceedTotal($request->input('student_academic_year_id'))
            ], // Ensure positive amount
            'payment_date' => 'required|date_format:Y-m-d',
            'notes' => 'nullable|string|max:1000',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $payment = StudentFeePayment::create($validator->validated());

        return new StudentFeePaymentResource($payment); // 201 implicit
    }

    /**
     * Display the specified payment.
     */
    public function show(StudentFeePayment $studentFeePayment) // Route model binding
    {
        // $this->authorize('view', $studentFeePayment);
        // You might want to load enrollment data here if needed for context
        // $studentFeePayment->load('studentAcademicYear.student', 'studentAcademicYear.academicYear');
        return new StudentFeePaymentResource($studentFeePayment);
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, StudentFeePayment $studentFeePayment)
    {
        // $this->authorize('update', $studentFeePayment);

         $validator = Validator::make($request->all(), [
             // Can only update amount, date, notes. Not the enrollment ID.
             'amount' => 'sometimes|required|numeric|min:0.01',
             'payment_date' => 'sometimes|required|date_format:Y-m-d',
             'notes' => 'nullable|string|max:1000',
         ]);

         if ($validator->fails()) {
             return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
         }

         $studentFeePayment->update($validator->validated());

         return new StudentFeePaymentResource($studentFeePayment->fresh());
    }

    /**
     * Remove the specified payment record.
     */
    public function destroy(StudentFeePayment $studentFeePayment)
    {
        // $this->authorize('delete', $studentFeePayment);

        $studentFeePayment->delete();

        return response()->json(['message' => 'تم حذف سجل الدفعة بنجاح.'], 200);
    }
}