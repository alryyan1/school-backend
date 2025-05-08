<?php // app/Http/Controllers/FeeInstallmentController.php
namespace App\Http\Controllers;

use App\Models\FeeInstallment;
use App\Models\StudentAcademicYear;
use Illuminate\Http\Request;
use App\Http\Resources\FeeInstallmentResource;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use TCPDF;
use TCPDF_FONTS;

// --- Optional: Custom PDF Class for Header/Footer ---
class FeeStatementPdf extends TCPDF
{

    public $schoolName = 'School Name'; // Default
    public $studentName = 'Student Name';
    public $academicYearName = 'Year';

    // Page header
    public function Header()
    {
        // Set font
        $font_path = public_path('\fonts') . '\arial.ttf';
        TCPDF_FONTS::addTTFfont($font_path);
        $font = 'arial';


        $this->SetFont($font, 'B', 10); // Use a font supporting Arabic
        // School Name Top Right
        $this->Cell(0, 7, $this->schoolName, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(5); // Line break
        $this->SetFont($font, 'B', 14);
        // Title Center
        $this->Cell(0, 9, 'كشف حساب الأقساط الدراسية', 0, true, 'C', 0, '', 0, false, 'M', 'M'); // ln=true
        $this->SetFont($font, '', 10);
        // Student Info Left
        $this->Cell(0, 7, 'الطالب: ' . $this->studentName . '  |  العام الدراسي: ' . $this->academicYearName, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln(8); // Line break after header info
        // Draw a line under the header
        $this->Line($this->GetX(), $this->GetY(), $this->getPageWidth() - $this->original_rMargin, $this->GetY());
        $this->Ln(2);
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $font_path = public_path('\fonts') . '\arial.ttf';
        TCPDF_FONTS::addTTFfont($font_path);
        $font = 'arial';
        $this->SetFont($font, 'I', 8);
        // Date Generated
        $this->Cell(0, 10, 'تاريخ الطباعة: ' . Carbon::now()->format('Y/m/d H:i'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
        // Page number
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
// --- End Custom PDF Class ---

class FeeInstallmentController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => 'required|integer|exists:student_academic_years,id',
        ]);
        if ($validator->fails()) return response()->json(['message' => 'Enrollment ID required', 'errors' => $validator->errors()], 422);
        $installments = FeeInstallment::with([ /* --- ADD EAGER LOADING HERE AS IN getDueSoon --- */
            'studentAcademicYear.student',
            'studentAcademicYear.academicYear',
            'studentAcademicYear.school',
        ])
            ->where('student_academic_year_id', $request->input('student_academic_year_id'))
            ->orderBy('due_date')->get();
        return FeeInstallmentResource::collection($installments);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => 'required|integer|exists:student_academic_years,id',
            'title' => 'required|string|max:255',
            'amount_due' => 'required|numeric|min:0.01',
            'due_date' => 'required|date_format:Y-m-d',
            'status' => ['sometimes', 'required', Rule::in(['قيد الانتظار', 'دفع جزئي', 'مدفوع', 'متأخر السداد'])], // Usually starts pending
            'notes' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        $data = $validator->validated();
        // Ensure status defaults correctly if not provided
        if (!isset($data['status'])) $data['status'] = 'قيد الانتظار';
        $installment = FeeInstallment::create($data);
        return new FeeInstallmentResource(
            $installment->load([ /* --- ADD EAGER LOADING HERE AS IN getDueSoon --- */
                'studentAcademicYear.student',
                'studentAcademicYear.academicYear',
                'studentAcademicYear.school',
            ])
        );
    }
    public function show(FeeInstallment $feeInstallment)
    {
        return new FeeInstallmentResource(
            $feeInstallment->load([ /* --- ADD EAGER LOADING HERE AS IN getDueSoon --- */
                'studentAcademicYear.student',
                'studentAcademicYear.academicYear',
                'studentAcademicYear.school',
            ])
        );
    }
    public function update(Request $request, FeeInstallment $feeInstallment)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'amount_due' => 'sometimes|required|numeric|min:0.01',
            'due_date' => 'sometimes|required|date_format:Y-m-d',
            'status' => ['sometimes', 'required', Rule::in(['قيد الانتظار', 'دفع جزئي', 'مدفوع', 'متأخر السداد'])],
            'notes' => 'nullable|string|max:1000',
            // Do not allow changing student_academic_year_id
        ]);
        if ($validator->fails()) return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        $feeInstallment->update($validator->validated());
        // ** TODO: Add logic here to recalculate/update amount_paid and status based on payments **
        // This requires fetching related payments, summing them, and updating the installment.
        // Consider doing this via an Observer on the StudentFeePayment model instead.
        $feeInstallment->update($validator->validated());
        return new FeeInstallmentResource(
            $feeInstallment->fresh()->load([ /* --- ADD EAGER LOADING HERE AS IN getDueSoon --- */
                'studentAcademicYear.student',
                'studentAcademicYear.academicYear',
                'studentAcademicYear.school',
            ])
        );
    }
    public function destroy(FeeInstallment $feeInstallment)
    {
        // ** CHECK: Prevent deletion if payments exist for this installment? **
        if ($feeInstallment->payments()->exists()) {
            return response()->json(['message' => 'لا يمكن حذف القسط لوجود دفعات مسجلة له.'], 409); // Conflict
        }
        $feeInstallment->delete();
        return response()->json(['message' => 'تم حذف القسط بنجاح.'], 200);
    }
    /**
     * Automatically generate fee installments for a student's enrollment.
     * POST /api/student-enrollments/{studentAcademicYear}/generate-installments
     */
    public function generateInstallments(Request $request, StudentAcademicYear $studentAcademicYear)
    {
        // Authorization check - can user manage fees for this enrollment?
        // $this->authorize('update', $studentAcademicYear); // Or a specific policy

        // --- Validation ---
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|numeric|min:0.01',
            'number_of_installments' => 'required|integer|min:1|max:12', // Limit installments
            // Optional: Allow specifying the start date, otherwise use academic year start
            // 'period_start_date' => 'sometimes|required|date_format:Y-m-d',
            // 'period_end_date' => 'sometimes|required|date_format:Y-m-d|after:period_start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // --- Check for Existing Installments ---
        if ($studentAcademicYear->feeInstallments()->exists()) {
            return response()->json(['message' => 'لا يمكن إنشاء أقساط تلقائية، يوجد أقساط مسجلة بالفعل لهذا العام.'], 409); // Conflict
        }

        // --- Calculation ---
        $validated = $validator->validated();
        $totalAmount = (float) $validated['total_amount'];
        $numInstallments = (int) $validated['number_of_installments'];
        $amountPerInstallment = round($totalAmount / $numInstallments, 2);
        // Calculate remainder for the last installment to ensure total matches
        $remainder = round($totalAmount - ($amountPerInstallment * $numInstallments), 2);

        // Determine period dates (use Academic Year dates from the enrollment)
        // Eager load academic year if not automatically loaded by binding
        $studentAcademicYear->loadMissing('academicYear');
        if (!$studentAcademicYear->academicYear) {
            return response()->json(['message' => 'Academic Year details missing for this enrollment.'], 400);
        }
        $startDate = Carbon::parse($studentAcademicYear->academicYear->start_date);
        $endDate = Carbon::parse($studentAcademicYear->academicYear->end_date);

        // Calculate months between installments (approximate)
        $totalMonths = $endDate->diffInMonths($startDate);
        // Avoid division by zero, default to monthly if numInstallments > totalMonths
        $monthsBetween = $numInstallments > 1 ? floor(max(1, $totalMonths) / ($numInstallments - 1)) : $totalMonths;
        if ($numInstallments === 1) {
            $monthsBetween = 0; // Only one installment due at start
        } else if ($monthsBetween < 1) {
            $monthsBetween = 1; // Default to monthly if too many installments for the period
        }


        $installmentsToCreate = [];
        $currentDueDate = $startDate->copy()->startOfMonth(); // Start from beginning of start month

        // --- Generate Installment Data ---
        $arabicNumerals = ['الأول', 'الثاني', 'الثالث', 'الرابع', 'الخامس', 'السادس', 'السابع', 'الثامن', 'التاسع', 'العاشر', 'الحادي عشر', 'الثاني عشر'];
        $cumulativeAmount = 0;

        for ($i = 1; $i <= $numInstallments; $i++) {
            $title = "القسط " . ($arabicNumerals[$i - 1] ?? $i); // Use Arabic numeral or number
            $dueDate = $currentDueDate->copy();

            // Ensure due date doesn't exceed the period end date
            if ($dueDate->isAfter($endDate)) {
                $dueDate = $endDate->copy();
            }

            $installmentAmount = $amountPerInstallment;
            // Add remainder to the last installment
            if ($i === $numInstallments) {
                $installmentAmount += $remainder;
                // Ensure final due date is not after end date
                $dueDate = min($dueDate, $endDate->copy());
            }

            $installmentsToCreate[] = [
                'student_academic_year_id' => $studentAcademicYear->id,
                'title' => $title,
                'amount_due' => $installmentAmount,
                'amount_paid' => 0.00, // Start unpaid
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => 'قيد الانتظار',
                'notes' => 'تم إنشاؤه تلقائياً',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Calculate next due date (add months, set to start of month)
            if ($numInstallments > 1 && $i < $numInstallments) {
                $currentDueDate->addMonths($monthsBetween)->startOfMonth();
            }
        }

        // --- Database Insertion ---
        try {
            // Use transaction for safety
            DB::transaction(function () use ($installmentsToCreate) {
                FeeInstallment::insert($installmentsToCreate); // Bulk insert
            });
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => "حدث خطأ أثناء حفظ الأقساط. " . $e->getMessage(), 'data' => $installmentsToCreate], 500);
        }

        // --- Response ---
        // Fetch the newly created installments to return them
        $newInstallments = $studentAcademicYear->feeInstallments()->get();
        return FeeInstallmentResource::collection($newInstallments); // Return created installments
    }
    /**
     * Generate a PDF Statement of Fee Installments for a specific enrollment.
     * GET /enrollments/{studentAcademicYear}/fee-statement-pdf
     */
    public function generateStatementPdf(StudentAcademicYear $studentAcademicYear)
    {
        // Authorization Check (Example: only admin or parent/student linked to enrollment)
        // $this->authorize('viewFeeStatement', $studentAcademicYear); // Define this policy ability

        // Eager load necessary data
        $studentAcademicYear->load([
            'student', // Select only needed student fields
            'academicYear', // Need school_id here
            'school', // Load school details
            'gradeLevel',
            'feeInstallments' // Load all installments
        ]);

        if (!$studentAcademicYear->student || !$studentAcademicYear->academicYear || !$studentAcademicYear->school) {
            abort(404, 'Enrollment details missing.'); // Or handle more gracefully
        }

        // --- PDF Creation ---
        $pdf = new FeeStatementPdf('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set data for custom header/footer
        $pdf->schoolName = $studentAcademicYear->school->name ?? 'اسم المدرسة';
        $pdf->studentName = $studentAcademicYear->student->student_name ?? 'اسم الطالب';
        $pdf->academicYearName = $studentAcademicYear->academicYear->name ?? 'العام الدراسي';
        $font_path = public_path('\fonts') . '\arial.ttf';
        TCPDF_FONTS::addTTFfont($font_path);
        $font = 'arial';


        // $pdf->addtt()
        // echo $font;
        // Set default font that supports Arabic - CRUCIAL
        $pdf->SetFont($font, '', 10); // ''=regular, 'B'=bold, 'I'=italic
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('كشف حساب أقساط الطالب: ' . $pdf->studentName);
        $pdf->SetSubject('تفاصيل الأقساط والمدفوعات');

        // Set margins and page breaks
        $pdf->SetMargins(15, 35, 15); // Left, Top (increased for header), Right
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(TRUE, 25);

        // Set font and RTL
        $pdf->setRTL(true);

        // Add page
        $pdf->AddPage();

        // --- Content ---

        // --- Define Column Widths (Adjust for Landscape A4 - usable width approx 267mm) ---
        $w_title = 70; // Increase width for title/link
        $w_due_date = 35;
        $w_amount_due = 40;
        $w_amount_paid = 40;
        $w_remaining = 40;
        $w_status = 42; // Fill remaining space
        $lineHeight = 8; // Slightly taller for readability maybe
        // Total = 70+35+40+40+40+42 = 267

        // --- Table Header (same as before, just uses new widths) ---
        $pdf->SetFont($font, 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(128);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell($w_title, $lineHeight, 'بيان القسط', 1, 0, 'C', true);
        $pdf->Cell($w_due_date, $lineHeight, 'تاريخ الاستحقاق', 1, 0, 'C', true);
        $pdf->Cell($w_amount_due, $lineHeight, 'المبلغ المستحق', 1, 0, 'C', true);
        $pdf->Cell($w_amount_paid, $lineHeight, 'المبلغ المدفوع', 1, 0, 'C', true);
        $pdf->Cell($w_remaining, $lineHeight, 'المبلغ المتبقي', 1, 0, 'C', true);
        $pdf->Cell($w_status, $lineHeight, 'الحالة', 1, 1, 'C', true); // ln=1 to move to next line
        $pdf->SetFont($font, '', 10); // Reset font
        $pdf->SetFillColor(255); // Reset fill color
        $pdf->SetTextColor(0); // Reset text color
        // $pdf->Cell($w_status, $lineHeight, 'الحالة', 1, 1, 'C', true);
        $pdf->SetFont($font, '', 10);
        $pdf->SetFillColor(255);
        $pdf->SetTextColor(0);
        // -- Table Body --
        $totalDue = 0;
        $totalPaid = 0;

        if ($studentAcademicYear->feeInstallments->isEmpty()) {
            $pdf->Cell(0, $lineHeight * 2, 'لا توجد أقساط مسجلة لهذا التسجيل.', 'LRB', 1, 'C');
        } else {
            foreach ($studentAcademicYear->feeInstallments as $installment) {
                // ... (calculate due, paid, remaining, statusText, set text color as before) ...
                $due = (float) $installment->amount_due;
                $paid = (float) $installment->amount_paid;
                $remaining = $due - $paid;
                $totalDue += $due;
                $totalPaid += $paid;

                // Format status for display
                $statusText = match ($installment->status) {
                    'pending' => 'مستحق',
                    'partially_paid' => 'مدفوع جزئياً',
                    'paid' => 'مدفوع بالكامل',
                    'overdue' => 'متأخر',
                    default => $installment->status,
                };

                // Set text color based on status or remaining amount
                if ($remaining <= 0 && $paid > 0) $pdf->SetTextColor(0, 128, 0); // Green for paid
                else if ($installment->status === 'overdue') $pdf->SetTextColor(255, 0, 0); // Red for overdue
                else if ($installment->status === 'partially_paid') $pdf->SetTextColor(200, 100, 0); // Orange for partial
                else $pdf->SetTextColor(0); // Black otherwise
                // --- LINK IMPLEMENTATION ---
                // Generate the URL for the payment detail PDF for this specific installment
                $paymentDetailUrl = route('installments.payments.pdf', ['feeInstallment' => $installment->id]);
                // Print the first cell (Title) as a link
                // Cell(width, height, text, border, ln, align, fill, link, ...)
                $pdf->Cell($w_title, $lineHeight, $installment->title, 'LR', 0, 'R', false, $paymentDetailUrl);
                $pdf->Cell($w_due_date, $lineHeight, $installment->due_date->format('Y/m/d'), 'R', 0, 'C');
                $pdf->Cell($w_amount_due, $lineHeight, number_format($due, 2), 'R', 0, 'R');
                $pdf->Cell($w_amount_paid, $lineHeight, number_format($paid, 2), 'R', 0, 'R');
                $pdf->Cell($w_remaining, $lineHeight, number_format($remaining, 2), 'R', 0, 'R');
                $pdf->Cell($w_status, $lineHeight, $statusText, 'R', 1, 'C'); // ln=1
            }
            $pdf->SetTextColor(0); // Reset text color after loop
        }


        // -- Table Footer (Totals) --
        $pdf->SetFont($font, 'B', 10); // Bold totals
        $pdf->SetFillColor(245, 245, 245); // Slightly different background for footer
        $pdf->Cell($w_title + $w_due_date, $lineHeight, 'الإجماليات', 'TLRB', 0, 'C', true); // Use Border LRTB for footer
        $pdf->Cell($w_amount_due, $lineHeight, number_format($totalDue, 2), 'TRB', 0, 'R', true);
        $pdf->Cell($w_amount_paid, $lineHeight, number_format($totalPaid, 2), 'TRB', 0, 'R', true);
        $pdf->Cell($w_remaining, $lineHeight, number_format($totalDue - $totalPaid, 2), 'TRB', 0, 'R', true);
        $pdf->Cell($w_status, $lineHeight, '', 'TRB', 1, 'C', true); // Empty status cell


        // --- Output ---
        $fileName = 'fee_statement_' . $studentAcademicYear->student->id . '_' . $studentAcademicYear->academicYear->name . '.pdf';
        // Clean filename (replace slashes in year name)
        $fileName = str_replace('/', '-', $fileName);

        $pdf->Output($fileName, 'I'); // 'I' for inline display
        exit;
    }
    /**
     * Get fee installments due within the next N days.
     * GET /api/fee-installments/due-soon
     */
    public function getDueSoon(Request $request)
    {
        // $this->authorize('viewAny', FeeInstallment::class); // Authorization

        $daysAhead = $request->input('days', default: 30); // Default to 7 days, allow customization
        $today = Carbon::today();
        $dueDateLimit = $today->copy()->addDays($daysAhead);

        $installmentsDueSoon = FeeInstallment::with([
            // --- EAGER LOAD NESTED DATA ---
            'studentAcademicYear' => function ($query) {
                $query->with([
                    'student',
                    'academicYear', // Relationship on StudentAcademicYear is 'academicYear'
                    'school',     // Relationship on StudentAcademicYear is 'school'
                    'gradeLevel'  // Relationship on StudentAcademicYear is 'gradeLevel'
                ]);
            }
            // ------------------------------
        ])
            ->whereBetween('due_date', [$today->toDateString(), $dueDateLimit->toDateString()])
            // Filter out already fully paid installments
            ->where('status', '!=', 'paid')
            // Optionally filter by specific school if needed based on logged-in user context
            // ->whereHas('studentAcademicYear.school', function($q) use ($userSchoolId) {
            //     $q->where('id', $userSchoolId);
            // })
            ->orderBy('due_date')
            ->get();

        return FeeInstallmentResource::collection($installmentsDueSoon);
    }
}
