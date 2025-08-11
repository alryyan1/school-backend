<?php // app/Http/Controllers/StudentFeePaymentController.php
namespace App\Http\Controllers;

use App\Models\StudentFeePayment;
use App\Models\FeeInstallment; // Import
use Illuminate\Http\Request;
use App\Http\Resources\StudentFeePaymentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // For potential transaction
use TCPDF;
// --- Optional: Custom PDF Class for Payment Detail Header/Footer ---
class PaymentDetailPdf extends TCPDF
{
    public $installmentTitle = 'Installment';
    public $studentName = 'Student';
    public $dueDate = '';

    public function Header()
    {
        $this->SetFont('dejavusans', 'B', 10);
        $this->Cell(0, 7, 'تفاصيل دفعات القسط: ' . $this->installmentTitle, 0, true, 'R'); // ln=true
        $this->SetFont('dejavusans', '', 9);
        $this->Cell(0, 6, 'الطالب: ' . $this->studentName . ' | تاريخ الاستحقاق: ' . $this->dueDate, 0, true, 'R'); // ln=true
        $this->Ln(4);
        $this->Line($this->GetX(), $this->GetY(), $this->getPageWidth() - $this->original_rMargin, $this->GetY());
        $this->Ln(2);
    }
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('dejavusans', '', 8);
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
class StudentFeePaymentController extends Controller
{
    public function index(Request $request)
    { // Get payments for a specific INSTALLMENT
        $validator = Validator::make($request->all(), [
            'fee_installment_id' => 'required|integer|exists:fee_installments,id', // <-- Filter by installment
        ]);
        if ($validator->fails()) return response()->json(['message' => 'Installment ID required', 'errors' => $validator->errors()], 422);
        $payments = StudentFeePayment::where('fee_installment_id', $request->input('fee_installment_id'))
            ->orderBy('payment_date', 'desc')->get();
        return StudentFeePaymentResource::collection($payments);
    }
    public function store(Request $request)
    {
        // Permission check: only users with 'record fee payments' may create payments
        if (!auth()->user() || !auth()->user()->can('record fee payments')) {
            return response()->json(['message' => 'غير مخول لإضافة دفعات'], 403);
        }
        $validator = Validator::make($request->all(), [
            'fee_installment_id' => 'required|integer|exists:fee_installments,id', // <-- Link to installment
            'payment_date' => 'required|date_format:Y-m-d',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => [
                'required',

                Rule::in(['cash','bank'])

                // <-- Validate payment method
            ],
            // Add validation: ensure payment amount <= remaining amount on installment?
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    $installment = FeeInstallment::find($request->input('fee_installment_id'));
                    if ($installment && ($installment->amount_paid + $value) > $installment->amount_due) {
                        $remaining = $installment->amount_due - $installment->amount_paid;
                        $fail("المبلغ المدفوع يتجاوز المبلغ المتبقي للقسط ({$remaining}).");
                    }
                },
            ],
        ]);
        if ($validator->fails()) return response()->json(['message' => implode(',', $validator->errors()->all()), 'errors' => implode(',', $validator->errors()->all())], 422);

        $payment = DB::transaction(function () use ($validator) { // Use transaction
            $payment = StudentFeePayment::create($validator->validated());
            $this->updateInstallmentStatus($payment->fee_installment_id); // Update parent status
            return $payment;
        });

        return new StudentFeePaymentResource($payment);
    }
    public function show(StudentFeePayment $studentFeePayment)
    { /* ... */
    }
    public function update(Request $request, StudentFeePayment $studentFeePayment)
    {
        // Permission check: only users with 'record fee payments' may update payments
        if (!auth()->user() || !auth()->user()->can('record fee payments')) {
            return response()->json(['message' => 'غير مخول لتعديل الدفعات'], 403);
        }
        $installmentId = $studentFeePayment->fee_installment_id; // Get installment ID before update
        $validator = Validator::make($request->all(), [
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'], // Keep existing rules
            'payment_date' => 'sometimes|required|date_format:Y-m-d',
            'payment_method' => ['sometimes', 'required', Rule::in(['cash', 'bank'])], // <-- Add validation
            'notes' => 'nullable|string|max:1000',
            // Add overpayment check for amount here if needed
        ]);
        
        if ($validator->fails()) return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);

        DB::transaction(function () use ($studentFeePayment, $validator) {
            $studentFeePayment->update($validator->validated());
            $this->updateInstallmentStatus($studentFeePayment->fee_installment_id);
        });

        return new StudentFeePaymentResource($studentFeePayment->fresh());
    }
    public function destroy(StudentFeePayment $studentFeePayment)
    {
        // Permission check: only users with 'record fee payments' may delete payments
        if (!auth()->user() || !auth()->user()->can('record fee payments')) {
            return response()->json(['message' => 'غير مخول لحذف الدفعات'], 403);
        }
        $installmentId = $studentFeePayment->fee_installment_id; // Get ID before deleting
        DB::transaction(function () use ($studentFeePayment, $installmentId) {
            $studentFeePayment->delete();
            $this->updateInstallmentStatus($installmentId);
        });
        return response()->json(['message' => 'تم حذف سجل الدفعة بنجاح.'], 200);
    }

    // --- Helper to update Installment Status ---
    protected function updateInstallmentStatus(int $installmentId): void
    {
        $installment = FeeInstallment::with('payments')->find($installmentId);
        if (!$installment) return;

        $totalPaid = $installment->payments->sum('amount');
        $newStatus = 'pending'; // Default

        if ($totalPaid > 0 && $totalPaid < $installment->amount_due) {
            $newStatus = 'دفع جزئي';
        } elseif ($totalPaid >= $installment->amount_due) {
            $newStatus = 'مدفوع';
            $totalPaid = $installment->amount_due; // Cap paid amount at due amount
        } elseif ($totalPaid <= 0 && $installment->due_date < now()->format('Y-m-d')) {
            $newStatus = 'متاخر السداد'; // Only set overdue if due and unpaid
        } elseif ($totalPaid <= 0) {
            $newStatus = 'pending';
        }

        $installment->amount_paid = $totalPaid;
        $installment->status = $newStatus;
        $installment->saveQuietly(); // Use saveQuietly to prevent triggering loops if using observers
    }
    /**
     * Generate a PDF detailing payments for a specific Fee Installment.
     * GET /fee-installments/{feeInstallment}/payments-pdf
     */
    public function generatePaymentsPdf(FeeInstallment $feeInstallment)
    {
        // Authorization check - can user view this installment/student?
        // $this->authorize('view', $feeInstallment);

        // Eager load needed data
        $feeInstallment->load([
            'payments' => function ($query) {
                $query->orderBy('payment_date', 'desc');
            }, // Order payments
            'studentAcademicYear.student:id,student_name',
            'studentAcademicYear.academicYear:id,name'
        ]);

        // --- PDF Creation ---
        // Use Portrait orientation for payment list usually
        $pdf = new PaymentDetailPdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set data for header
        $pdf->installmentTitle = $feeInstallment->title ?? 'قسط';
        $pdf->studentName = $feeInstallment->studentAcademicYear?->student?->student_name ?? 'غير معروف';
        $pdf->dueDate = $feeInstallment->due_date?->format('Y/m/d') ?? '-';

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('تفاصيل دفعات: ' . $pdf->installmentTitle . ' للطالب: ' . $pdf->studentName);
        $pdf->SetSubject('تفاصيل الدفعات');

        // Margins & Font
        $pdf->SetMargins(15, 30, 15); // Adjusted top margin for header
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->setRTL(true);

        // Add page
        $pdf->AddPage();

        // --- Content ---

        // --- Installment Summary ---
        $pdf->SetFont('dejavusans', 'B', 11);
        $pdf->Cell(0, 8, 'ملخص القسط', 0, 1, 'R');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 7, 'المبلغ المستحق:', 0, 0, 'R');
        $pdf->Cell(50, 7, number_format((float)$feeInstallment->amount_due, 1), 0, 0, 'R');
        $pdf->Cell(40, 7, 'تاريخ الاستحقاق:', 0, 0, 'R');
        $pdf->Cell(50, 7, $pdf->dueDate, 0, 1, 'R');
        $pdf->Cell(40, 7, 'المبلغ المدفوع:', 0, 0, 'R');
        $pdf->Cell(50, 7, number_format((float)$feeInstallment->amount_paid, 1), 0, 0, 'R');
        $pdf->Cell(40, 7, 'الحالة:', 0, 0, 'R');
        $pdf->Cell(50, 7, $feeInstallment->status, 0, 1, 'R'); // Add translation if needed
        $pdf->Ln(5);


        // --- Payment Table ---
        $pdf->SetFont('dejavusans', 'B', 11);
        $pdf->Cell(0, 8, 'سجل الدفعات', 0, 1, 'R');
        $pdf->Ln(2);

        // Column Widths (Approx 180mm usable)
        $w_date = 35;
        $w_amount = 45;
        $w_payment_method = 30;
        $w_notes = 70; // Flexible notes
        $lineHeight = 7;

        // Header
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(128, 128, 128);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell($w_date, $lineHeight, 'تاريخ الدفعة', 1, 0, 'C', true);
        $pdf->Cell($w_amount, $lineHeight, 'المبلغ المدفوع', 1, 0, 'C', true);
        $pdf->Cell($w_payment_method, $lineHeight, 'طريقه الدفع', 1, 0, 'C', true);
        $pdf->Cell($w_notes, $lineHeight, 'الملاحظات', 1, 1, 'C', true);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetFillColor(255);

        // Body
        if ($feeInstallment->payments->isEmpty()) {
            $pdf->Cell($w_date + $w_amount + $w_notes, $lineHeight * 2, 'لا توجد دفعات مسجلة لهذا القسط.', 'LRB', 1, 'C');
        } else {
            foreach ($feeInstallment->payments as $payment) {
                $pdf->Cell($w_date, $lineHeight, $payment->payment_date->format('Y/m/d'), 'LR', 0, 'C');
                $pdf->Cell($w_amount, $lineHeight, number_format((float)$payment->amount, 2), 'R', 0, 'R');
                // Use MultiCell for notes to allow wrapping
                $startX = $pdf->GetX();
                $startY = $pdf->GetY();
                $pdf->MultiCell($w_notes, $lineHeight, $payment->notes ?? '-', 'R', 'R', false, 1, $startX, $startY, true, 0, false, true, $lineHeight, 'M');
                // Draw bottom border for the row after MultiCell
                // --- THIS IS THE CORRECTED LINE ---
                $pdf->Line(
                    $pdf->getMargins()['left'], // Get the current left margin setting
                    $pdf->GetY(),              // Get the current Y position
                    $pdf->getPageWidth() - $pdf->getMargins()['right'], // Calculate the right edge based on page width and right margin
                    $pdf->GetY()               // Use the same Y position to draw a horizontal line
                );
            }
        }

        // Footer (Total Payments for this installment)
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell($w_date, $lineHeight, 'إجمالي الدفعات', 'TLRB', 0, 'C', true);
        $pdf->Cell($w_amount, $lineHeight, number_format((float)$feeInstallment->payments->sum('amount'), 2), 'TRB', 0, 'R', true);
        $pdf->Cell($w_payment_method, $lineHeight, '', 'TRB', 0, 'R', true);
        $pdf->Cell($w_notes, $lineHeight, '', 'TRB', 1, 'R', true);


        // --- Output ---
        $fileName = 'payment_details_installment_' . $feeInstallment->id . '.pdf';
        $pdf->Output($fileName, 'I');
        exit;
    }
}
