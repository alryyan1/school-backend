<?php

namespace App\Http\Controllers;

use App\Helpers\StudentListPdf;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Resources\StudentResource; // Import the API Resource class (if you create one)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Import the Validator class
use TCPDF;
use TCPDF_FONTS;
use App\Helpers\TermsConditionsPdf;
use App\Helpers\RevenueListPdf;
use App\Models\StudentLedger;
use App\Models\StudentDeportationLedger;
use App\Models\DeportationPath;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class StudentPdf extends TCPDF // Optional: Extend TCPDF for custom Headers/Footers
{
    // Page header (optional)
    public function Header()
    {
        // Set font
        $this->SetFont('dejavusans', 'B', 12); // Use a font supporting Arabic
        // Title
        $this->Cell(0, 10, 'تقرير بيانات الطالب', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(5); // Line break
    }

    // Page footer (optional)
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('dejavusans', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(
            'wishedSchool',
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.classroom',
            'enrollments.feeInstallments',
            'enrollments.deportationPath',
            'approvedByUser'
        );

        // Search term filter (supports name/phones/email/gov id, student id, or enrollment id)
        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->get('search'));

            $query->where(function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', "%{$searchTerm}%")
                  ->orWhere('father_name', 'like', "%{$searchTerm}%")
                  ->orWhere('father_phone', 'like', "%{$searchTerm}%")
                  ->orWhere('mother_name', 'like', "%{$searchTerm}%")
                  ->orWhere('mother_phone', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('goverment_id', 'like', "%{$searchTerm}%");
                  
            });

            // If numeric, also try matching student id or related enrollment id
            if (ctype_digit($searchTerm)) {
                $numeric = (int) $searchTerm;
                $query->orWhere('id', $numeric)
                      ->orWhereHas('enrollments', function ($en) use ($numeric) {
                          $en->where('id', $numeric);
                      });
            }
        }

        // Reference number filter - search by ledger reference_number
        if ($request->filled('reference_number')) {
            $referenceNumber = trim((string) $request->get('reference_number'));
            $query->whereHas('enrollments', function ($en) use ($referenceNumber) {
                $en->whereHas('studentLedgers', function ($ledger) use ($referenceNumber) {
                    $ledger->where('reference_number', 'like', "%{$referenceNumber}%");
                });
            });
        }

        // Wished school filter
        if ($request->filled('wished_school_id')) {
            $query->where('wished_school', $request->get('wished_school_id'));
        }

        // Date range filter
        if ($request->filled('date_type') && $request->get('date_type') !== ' ') {
            $dateType = $request->get('date_type');
            if ($request->filled('start_date')) {
                $query->whereDate($dateType, '>=', $request->get('start_date'));
            }
            if ($request->filled('end_date')) {
                $query->whereDate($dateType, '<=', $request->get('end_date'));
            }
        }

        // Only enrolled students filter
        if ($request->boolean('only_enrolled')) {
            $query->whereHas('enrollments');
        }

        // Only not enrolled students filter
        if ($request->boolean('only_not_enrolled')) {
            $query->whereDoesntHave('enrollments');
        }

        // Only approved students filter
        if ($request->boolean('only_approved')) {
            $query->where('approved', true);
        }

        // Only not approved students filter
        if ($request->boolean('only_not_approved')) {
            $query->where('approved', false);
        }

        // Only students with no payments filter
        if ($request->boolean('only_no_payments')) {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('studentLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }

        // School filter - filter by enrollment school
        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->get('school_id'));
            });
        }

        // Grade level filter - filter by enrollment grade level
        if ($request->filled('grade_level_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('grade_level_id', $request->get('grade_level_id'));
            });
        }

        // Classroom filter - filter by enrollment classroom
        if ($request->filled('classroom_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('classroom_id', $request->get('classroom_id'));
            });
        }
        if ($request->filled('enrollment_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('enrollment_type', $request->get('enrollment_type'));
            });
        }

        // Enrollment type filter - filter by enrollment type
        if ($request->filled('enrollment_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('enrollment_type', $request->get('enrollment_type'));
            });
        }

        // Only deportation enrollments filter
        if ($request->boolean('only_deportation')) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('deportation', true);
            });
        }

        // Deportation path filter
        if ($request->filled('deportation_path_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('deportation_path_id', $request->get('deportation_path_id'));
            });
        }

        // Deportation type filter
        if ($request->filled('deportation_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('deportation_type', $request->get('deportation_type'));
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $students = $query->paginate($perPage);

        return response()->json([
            'data' => StudentResource::collection($students),
            'pagination' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'from' => $students->firstItem(),
                'to' => $students->lastItem(),
            ]
        ]);
    }

    /**
     * Generate Revenues PDF based on student filters.
     */
    public function revenuesPdf(Request $request)
    {
        $query = Student::with([
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.classroom',
            'enrollments.feeInstallments',
        ]);

        // Apply same filters used in index for enrollments-related fields
        if ($request->boolean('only_enrolled')) {
            $query->whereHas('enrollments');
        }
        if ($request->boolean('only_approved')) {
            $query->where('approved', true);
        }
        
        // Only students with no payments filter
        if ($request->boolean('only_no_payments')) {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('studentLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }
        
        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->get('school_id'));
            });
        }
        if ($request->filled('grade_level_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('grade_level_id', $request->get('grade_level_id'));
            });
        }
        if ($request->filled('classroom_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('classroom_id', $request->get('classroom_id'));
            });
        }
        if ($request->filled('enrollment_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('enrollment_type', $request->get('enrollment_type'));
            });
        }

        $students = $query->get();

        // Collect first enrollment IDs (latest by created_at) to summarize ledgers
        $enrollmentIds = [];
        foreach ($students as $student) {
            $firstEnrollment = $student->enrollments->sortByDesc(function ($en) {
                return $en->created_at?->timestamp ?? 0;
            })->first();
            if ($firstEnrollment && $firstEnrollment->id) {
                $enrollmentIds[] = $firstEnrollment->id;
            }
        }

        $byEnrollment = [];
        $grandFees = 0.0;
        $grandPayments = 0.0;
        $grandDiscounts = 0.0;
        $grandRefunds = 0.0;
        $grandAdjustments = 0.0;
        if (!empty($enrollmentIds)) {
            $summaryRows = StudentLedger::whereIn('enrollment_id', $enrollmentIds)
                ->selectRaw('
                    enrollment_id,
                    SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END) as total_fees,
                    SUM(CASE WHEN transaction_type = "payment" THEN ABS(amount) ELSE 0 END) as total_payments,
                    SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END) as total_discounts,
                    SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END) as total_refunds,
                    SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as total_adjustments
                ')
                ->groupBy('enrollment_id')
                ->get();

            foreach ($summaryRows as $row) {
                $byEnrollment[$row->enrollment_id] = [
                    'total_fees' => (float)$row->total_fees,
                    'total_payments' => (float)$row->total_payments,
                    'total_discounts' => (float)$row->total_discounts,
                    'total_refunds' => (float)$row->total_refunds,
                    'total_adjustments' => (float)$row->total_adjustments,
                ];
                $grandFees += (float)$row->total_fees;
                $grandPayments += (float)$row->total_payments;
                $grandDiscounts += (float)$row->total_discounts;
                $grandRefunds += (float)$row->total_refunds;
                $grandAdjustments += (float)$row->total_adjustments;
            }
        }

        $global = [
            'total_expected' => $grandFees,
            'total_paid' => $grandPayments,
            'total_discounts' => $grandDiscounts,
            'total_refunds' => $grandRefunds,
            'total_adjustments' => $grandAdjustments,
            'total_balance' => max($grandFees - $grandPayments - $grandDiscounts + $grandRefunds + $grandAdjustments, 0),
            'count' => $students->count(),
            'by_enrollment' => $byEnrollment,
        ];

        $pdf = new RevenueListPdf($students, $global);
        $pdf->SetTitle('تقرير الإيرادات');
        $pdf->render();

        return response($pdf->Output('revenues.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="revenues.pdf"');
    }

    /**
     * Web endpoint to open Revenues PDF in a new tab (inline display)
     */
    public function revenuesPdfWeb(Request $request)
    {
        // Reuse the same query building logic
        $query = Student::with([
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.classroom',
            'enrollments.feeInstallments',
        ]);

        if ($request->boolean('only_enrolled')) {
            $query->whereHas('enrollments');
        }
        if ($request->boolean('only_approved')) {
            $query->where('approved', true);
        }
        
        // Only students with no payments filter
        if ($request->boolean('only_no_payments')) {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('studentLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }
        
        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->get('school_id'));
            });
        }
        if ($request->filled('grade_level_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('grade_level_id', $request->get('grade_level_id'));
            });
        }
        if ($request->filled('classroom_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('classroom_id', $request->get('classroom_id'));
            });
        }
        if ($request->filled('enrollment_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('enrollment_type', $request->get('enrollment_type'));
            });
        }

        $students = $query->get();

        // Collect first enrollment IDs (latest by created_at) to summarize ledgers
        $enrollmentIds = [];
        foreach ($students as $student) {
            $firstEnrollment = $student->enrollments->sortByDesc(function ($en) {
                return $en->created_at?->timestamp ?? 0;
            })->first();
            if ($firstEnrollment && $firstEnrollment->id) {
                $enrollmentIds[] = $firstEnrollment->id;
            }
        }

        $byEnrollment = [];
        $grandFees = 0.0;
        $grandPayments = 0.0;
        $grandDiscounts = 0.0;
        $grandRefunds = 0.0;
        $grandAdjustments = 0.0;
        if (!empty($enrollmentIds)) {
            $summaryRows = StudentLedger::whereIn('enrollment_id', $enrollmentIds)
                ->selectRaw('
                    enrollment_id,
                    SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END) as total_fees,
                    SUM(CASE WHEN transaction_type = "payment" THEN ABS(amount) ELSE 0 END) as total_payments,
                    SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END) as total_discounts,
                    SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END) as total_refunds,
                    SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as total_adjustments
                ')
                ->groupBy('enrollment_id')
                ->get();

            foreach ($summaryRows as $row) {
                $byEnrollment[$row->enrollment_id] = [
                    'total_fees' => (float)$row->total_fees,
                    'total_payments' => (float)$row->total_payments,
                    'total_discounts' => (float)$row->total_discounts,
                    'total_refunds' => (float)$row->total_refunds,
                    'total_adjustments' => (float)$row->total_adjustments,
                ];
                $grandFees += (float)$row->total_fees;
                $grandPayments += (float)$row->total_payments;
                $grandDiscounts += (float)$row->total_discounts;
                $grandRefunds += (float)$row->total_refunds;
                $grandAdjustments += (float)$row->total_adjustments;
            }
        }

        $global = [
            'total_expected' => $grandFees,
            'total_paid' => $grandPayments,
            'total_discounts' => $grandDiscounts,
            'total_refunds' => $grandRefunds,
            'total_adjustments' => $grandAdjustments,
            'total_balance' => max($grandFees - $grandPayments - $grandDiscounts + $grandRefunds + $grandAdjustments, 0),
            'count' => $students->count(),
            'by_enrollment' => $byEnrollment,
        ];

        $pdf = new RevenueListPdf($students, $global);
        $pdf->SetTitle('تقرير الإيرادات');
        $pdf->render();

        // Inline display instead of attachment
        return response($pdf->Output('revenues.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="revenues.pdf"');
    }
    public function updatePhoto(Request $request, Student $student)
    {
        // --- Authorization (Example using Policy) ---
        // Make sure you have a StudentPolicy with an 'update' or 'updatePhoto' method
        // $this->authorize('update', $student); // Or specific 'updatePhoto' ability
        // --- End Authorization ---

        // --- Validation ---
        $validator = Validator::make($request->all(), [
            // Validate the 'photo' field from the FormData
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB example
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من الصورة', 'errors' => $validator->errors()], 422);
        }
        // --- End Validation ---

        try {
            // --- Delete Old Photo ---
            if ($student->image && Storage::disk('public')->exists($student->image)) {
                Storage::disk('public')->delete($student->image);
            }
            // --- End Delete Old Photo ---

            // --- Store New Photo ---
            // Store in 'storage/app/public/students_photos'
            // The 'store' method generates a unique filename
            $path = $request->file('image')->store('students_photos', 'public');
            // --- End Store New Photo ---


            // --- Update Database ---
            // Save the relative path to the database
            $student->image = $path;
            $student->save();
            // --- End Update Database ---


            // --- Return Response ---
            // Return the updated student resource (which should generate the full URL)
            // Use fresh() to ensure you get the updated model attributes.
            return new StudentResource($student->fresh());
            // --- End Return Response ---

        } catch (\Exception $e) {
            // Handle potential storage errors or other exceptions
            report($e); // Log the error
            return response()->json(['message' => 'حدث خطأ أثناء رفع الصورة. ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_job' => 'required|string|max:255',
            'father_address' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
            'father_whatsapp' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:255',
            'mother_job' => 'required|string|max:255',
            'mother_address' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
            'mother_whatsapp' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'wished_school' => 'required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Concatenate the error messages into a single string
            $errorMessage = '';
            foreach ($errors->all() as $error) {
                $errorMessage .= $error . " \n"; // Add a space between messages
            }

            return response()->json(['message' => $errorMessage], 422);
        }
        $data = $request->all();
        $data['approved_by_user'] = Auth::id() ?? 1; // Default to user ID 1 if not authenticated
        $student = Student::create($data);

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }
        $student->load([
            'wishedSchool',
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.classroom',
            'enrollments.feeInstallments',
            'enrollments.deportationPath',
            'approvedByUser',
        ]);
        return new StudentResource($student);
    }

    public function update(Request $request, Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }

        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_job' => 'required|string|max:255',
            'father_address' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
            'father_whatsapp' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:255',
            'mother_job' => 'required|string|max:255',
            'mother_address' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
            'mother_whatsapp' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'wished_school' => 'required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => implode(' ', $validator->errors()->all())], 422); // Unprocessable Entity
        }

        // Drop system-managed fields to avoid invalid types (e.g., JSON object) being saved
        $data = $request->all();
        unset($data['approved_by_user'], $data['approved'], $data['aproove_date']);

        $student->update($data);
        return $student->fresh();
    }

    public function destroy(Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted'], 204); // No Content (successful deletion)
    }

    /**
     * Accept a student application.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        if ($student->approved) {
            return response()->json(['message' => 'Student is already approved'], 400);
        }

        $student->update([
            'approved' => true,
            'aproove_date' => now(),
            'approved_by_user' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
        ]);

        return response()->json([
            'message' => 'Student accepted successfully',
            'student' => new StudentResource($student->load(['wishedSchool', 'approvedByUser']))
        ]);
    }

    /**
     * Generate a PDF for the specified student.
     *
     * @param  \\App\\Models\\Student  $student
     * @return \\Illuminate\\Http\\Response
     */
    public function generatePdf(Student $student)
    {
        // Optional: Authorization check - can the current user view this student?
        // $this->authorize('view', $student);

        // --- Create PDF Object ---
        // Use custom class if you defined one for header/footer
        // $pdf = new StudentPdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // Or use the base class
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // --- Document Information ---
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(config('app.name')); // Use your app name
        $pdf->SetTitle('تقرير الطالب: ' . $student->student_name);
        $pdf->SetSubject('بيانات الطالب التفصيلية');
        $pdf->SetKeywords('Student, Report, PDF, School, ' . $student->student_name);

        // --- Header/Footer Data (if not using extended class) ---
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'تقرير بيانات الطالب', config('app.name'));
        // $pdf->setFooterData(array(0,64,0), array(0,64,128));
        // $pdf->setHeaderFont(Array('dejavusans', '', PDF_FONT_SIZE_MAIN));
        // $pdf->setFooterFont(Array('dejavusans', '', PDF_FONT_SIZE_DATA));

        // --- Margins & AutoPageBreak ---
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 15, 15); // Left, Top, Right
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25); // Enable auto page break with 25mm bottom margin

        // --- Font & Language ---
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Set language directionality - IMPORTANT FOR ARABIC
        $pdf->setRTL(true);
        $font_path = public_path('\fonts').'\arial.ttf';
        TCPDF_FONTS::addTTFfont($font_path);
        $font = 'arial';


        // $pdf->addtt()
        // echo $font;
        // Set default font that supports Arabic - CRUCIAL
        $pdf->SetFont($font, '', 10); // ''=regular, 'B'=bold, 'I'=italic

        // --- Add a page ---
        $pdf->AddPage();

        // --- Content Generation using Cell() ---

        // --- Title Section ---
        $pdf->SetFont($font, 'B', 16);
        $pdf->Cell(0, 12, 'بيانات الطالب', 0, 1, 'C'); // ln=1 moves to next line, align=C center
        $pdf->Ln(8); // Add vertical space

        // --- Image Section (Optional) ---
        $imagePath = null;
        if ($student->image && Storage::disk('public')->exists($student->image)) {
            $imagePath = Storage::disk('public')->path($student->image); // Get absolute path
        }
        if ($imagePath) {
            // Place image top-left (adjust x, y, w, h as needed)
            // Parameters: file, x, y, width, height, type, link, align, resize, dpi, palign, ismask, imgmask, border, fitbox, hidden, fitonpage
            $pdf->Image($imagePath, 170, 25, 25, 0, '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
            // We placed image on left (which is top-right in RTL page) before main content block
            $pdf->SetY(30); // Adjust Y position after image to avoid overlap if needed
        }

        // --- Define cell widths ---
        $labelWidth = 45;
        $valueWidth = 125; // (45 + 125 = 170) - adjust based on margins (180 usable approx)
        $lineHeight = 7; // Height of each row cell

        // --- Helper function for Key-Value pairs ---
        $printRow = function (string $label, ?string $value, bool $isBoldValue = false) use ($pdf, $labelWidth, $valueWidth, $lineHeight,$font) {
            $pdf->SetFont($font, 'B', 14); // Bold Label
            $pdf->Cell($labelWidth, $lineHeight, $label . ':', 0, 0, 'R'); // Right align label
            $pdf->SetFont($font, $isBoldValue ? 'B' : '', 14); // Value font
            $pdf->Cell($valueWidth, $lineHeight, $value ?? '-', 0, 1, 'R'); // Right align value, ln=1 to move down
        };

        // --- Basic Info Section ---
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'المعلومات الأساسية', '', 1, 'R'); // Border Bottom, Right align
        $pdf->Ln(3);
        $printRow('الاسم الكامل', $student->student_name, true); // Make name bold
        $printRow('تاريخ الميلاد', $student->date_of_birth ? $student->date_of_birth: null); // Format date
        $printRow('الجنس', $student->gender);
        $printRow('المدرسة المرغوبة', $student->wishedSchool ? $student->wishedSchool->name : null);
        $printRow('الرقم الوطني', $student->goverment_id);
        // $printRow('البريد الإلكتروني', $student->email);
        $printRow('المدرسة السابقة', $student->referred_school);
        $printRow('نسبة النجاح السابقة', $student->success_percentage ? $student->success_percentage . '%' : null);
        $printRow('الحالة الصحية', $student->medical_condition);
        $pdf->Ln(6);

        // --- Father Info Section ---
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'معلومات الأب', 'B', 1, 'R');
        $pdf->Ln(3);
        $printRow('اسم الأب', $student->father_name);
        $printRow('الوظيفة', $student->father_job);
        $printRow('الهاتف', $student->father_phone);
        $printRow('واتساب', $student->father_whatsapp);
        $printRow('العنوان', $student->father_address);
        $pdf->Ln(6);

        // --- Mother Info Section ---
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'معلومات الأم', 'B', 1, 'R');
        $pdf->Ln(3);
        $printRow('اسم الأم', $student->mother_name);
        $printRow('الوظيفة', $student->mother_job);
        $printRow('الهاتف', $student->mother_phone);
        $printRow('واتساب', $student->mother_whatsapp);
        $printRow('العنوان', $student->mother_address);
        $pdf->Ln(6);

        // --- Other Parent Info Section (Conditional) ---
        if ($student->other_parent) {
            $pdf->SetFont('dejavusans', 'B', 12);
            $pdf->Cell(0, 10, 'معلومات ولي الأمر الآخر', 'B', 1, 'R');
            $pdf->Ln(3);
            $printRow('الاسم', $student->other_parent);
            $printRow('صلة القرابة', $student->relation_of_other_parent);
            $printRow('الوظيفة', $student->relation_job);
            $printRow('الهاتف', $student->relation_phone);
            $printRow('واتساب', $student->relation_whatsapp);
            $pdf->Ln(6);
        }

        // --- Closest Person Info Section ---
        // $pdf->SetFont('dejavusans', 'B', 12);
        // $pdf->Cell(0, 10, 'أقرب شخص للطالب', 'B', 1, 'R');
        // $pdf->Ln(3);
        // $printRow('الاسم', $student->closest_name);
        // $printRow('الهاتف', $student->closest_phone);
        $pdf->Ln(6);

        // --- Approval Status Section ---
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'حالة القبول', 'B', 1, 'R');
        $pdf->Ln(3);
        $printRow('الحالة', $student->approved ? 'مقبول' : 'قيد المراجعة');
        $printRow('تاريخ القبول', $student->aproove_date ? $student->aproove_date: null); // Check spelling 'aproove_date'
        $printRow('تم القبول بواسطة (ID)', $student->approved_by_user ? (string)$student->approved_by_user : null);
        // $printRow('تم إرسال الرسالة', $student->message_sent ? 'نعم' : 'لا');
        $pdf->Ln(6);


        // --- Output the PDF ---
        // 'I': Inline display in browser
        // 'D': Force download
        // 'F': Save to file path
        // 'S': Return as string
        $pdf->Output('student_report_' . $student->id . '.pdf', 'I');
        exit; // Stop script execution after PDF output

    }
    /**
     * Generate a PDF list of students.
     * GET /reports/students/list-pdf
     * Allows filtering via query params (e.g., ?status=active&grade_id=5)
     */
    public function generateListPdf(Request $request) // Inject Request
    {
        // Authorization check (e.g., only admins/teachers)
        // $this->authorize('viewAny', Student::class);

        // --- Fetch Students with Filters ---
        $query = Student::with('wishedSchool'); // Start query builder with relationship

        // Search term filter
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('student_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('father_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('mother_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('father_phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('goverment_id', 'like', '%' . $searchTerm . '%');
            });
        }

        // Wished school filter
        if ($request->filled('wished_school_id')) {
            $query->where('wished_school', $request->input('wished_school_id'));
        }

        // Date range filter
        if ($request->filled('date_type') && ($request->filled('start_date') || $request->filled('end_date'))) {
            $dateType = $request->input('date_type');
            if ($request->filled('start_date')) {
                $query->where($dateType, '>=', $request->input('start_date'));
            }
            if ($request->filled('end_date')) {
                $query->where($dateType, '<=', $request->input('end_date'));
            }
        }

        // Enrollment filter
        if ($request->filled('only_enrolled') && $request->input('only_enrolled') === 'true') {
            $query->whereHas('enrollments');
        }

        // Not enrolled filter
        if ($request->filled('only_not_enrolled') && $request->input('only_not_enrolled') === 'true') {
            $query->whereDoesntHave('enrollments');
        }

        // Approval filter
        if ($request->filled('only_approved') && $request->input('only_approved') === 'true') {
            $query->where('approved', true);
        }

        // Not approved filter
        if ($request->filled('only_not_approved') && $request->input('only_not_approved') === 'true') {
            $query->where('approved', false);
        }

        // Only students with no payments filter
        if ($request->filled('only_no_payments') && $request->input('only_no_payments') === 'true') {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('studentLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $students = $query->get();
        // --- End Fetch Students ---


        // --- PDF Creation ---
        $pdf = new StudentListPdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false); // Portrait A4

        // --- Set Filter Info for Header ---
        $filterText = "جميع الطلاب"; // Default
        $filters = [];
        
        if ($request->filled('search')) {
            $filters[] = "بحث: " . $request->input('search');
        }
        
        if ($request->filled('wished_school_id')) {
            $school = \App\Models\School::find($request->input('wished_school_id'));
            if ($school) {
                $filters[] = "المدرسة: " . $school->name;
            }
        }
        
        if ($request->filled('date_type') && ($request->filled('start_date') || $request->filled('end_date'))) {
            $dateType = $request->input('date_type') === 'created_at' ? 'تاريخ التسجيل' : 'تاريخ الميلاد';
            $dateRange = [];
            if ($request->filled('start_date')) {
                $dateRange[] = "من: " . $request->input('start_date');
            }
            if ($request->filled('end_date')) {
                $dateRange[] = "إلى: " . $request->input('end_date');
            }
            $filters[] = $dateType . " (" . implode(' - ', $dateRange) . ")";
        }

        // Add enrollment filter info
        if ($request->filled('only_enrolled') && $request->input('only_enrolled') === 'true') {
            $filters[] = "المسجلون فقط";
        }

        // Add not enrolled filter info
        if ($request->filled('only_not_enrolled') && $request->input('only_not_enrolled') === 'true') {
            $filters[] = "غير المسجلين";
        }

        // Add approval filter info
        if ($request->filled('only_approved') && $request->input('only_approved') === 'true') {
            $filters[] = "المقبولون فقط";
        }

        // Add not approved filter info
        if ($request->filled('only_not_approved') && $request->input('only_not_approved') === 'true') {
            $filters[] = "غير المقبولين";
        }

        // Add no payments filter info
        if ($request->filled('only_no_payments') && $request->input('only_no_payments') === 'true') {
            $filters[] = "غير مدفوع";
        }
        
        if (!empty($filters)) {
            $filterText = "فلترة: " . implode(' | ', $filters);
        }
        
        $pdf->filterInfo = $filterText;
        // --- End Filter Info ---


        // --- Metadata & Setup ---
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('قائمة الطلاب');
        $pdf->SetSubject('قائمة ببيانات الطلاب المسجلين');
        $pdf->SetMargins(10, 55, 10); // L, T, R (adjust top margin for header)
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->SetFont('dejavusans', '', 9); // Base font size
        $pdf->setRTL(true);
        $pdf->AddPage();
        // --- End Metadata & Setup ---


        // --- Content: Table ---
        // Define Column Widths (Total usable width approx 190mm for A4 Portrait)
        $w = [
            'id' => 15,
            'name' => 55,
            'gov_id' => 30,
            'gender' => 15,
            'dob' => 25,
            'phone' => 30,
            // 'grade' => 30, // Add if filtering/showing grade
            'status' => 20, // Example status column
        ];
        $lineHeight = 6;

        // -- Table Header --
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(128);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell($w['id'], $lineHeight, 'الرقم', 1, 0, 'C', true);
        $pdf->Cell($w['name'], $lineHeight, 'اسم الطالب', 1, 0, 'C', true);
        $pdf->Cell($w['gov_id'], $lineHeight, 'الرقم الوطني', 1, 0, 'C', true);
        $pdf->Cell($w['gender'], $lineHeight, 'الجنس', 1, 0, 'C', true);
        $pdf->Cell($w['dob'], $lineHeight, 'تاريخ الميلاد', 1, 0, 'C', true);
        $pdf->Cell($w['phone'], $lineHeight, 'هاتف الأب', 1, 0, 'C', true);
        $pdf->Cell($w['status'], $lineHeight, 'الحالة', 1, 1, 'C', true); // ln=1
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetFillColor(255);

        // -- Table Body --
        if ($students->isEmpty()) {
            $pdf->Cell(array_sum($w), $lineHeight * 2, 'لا يوجد طلاب لعرضهم حسب الفلتر المحدد.', 'LRB', 1, 'C');
        } else {
            $fill = false; // Alternate row fill
            foreach ($students as $student) {
                // Example Status (assuming you have a way to get current status)
                // $status = $student->currentEnrollment?->status ?? 'غير مسجل';
                $status = $student->approved ? 'مقبول' : 'قيد المراجعة'; // Using approval status for now

                // Set background fill for row
                 $pdf->SetFillColor($fill ? 245 : 255);

                $pdf->Cell($w['id'], $lineHeight, $student->id, 'LR', 0, 'C', $fill);
                // Use MultiCell for Name if it might wrap
                $startX = $pdf->GetX(); $startY = $pdf->GetY();
                $pdf->MultiCell($w['name'], $lineHeight, $student->student_name ?? '-', 0, 'R', $fill, 0, $startX, $startY, true, 0, false, true, $lineHeight, 'M');
                 $pdf->SetXY($startX + $w['name'], $startY); // Reset position after MultiCell

                $pdf->Cell($w['gov_id'], $lineHeight, $student->goverment_id ?? '-', 'R', 0, 'C', $fill);
                $pdf->Cell($w['gender'], $lineHeight, $student->gender ?? '-', 'R', 0, 'C', $fill);
                $pdf->Cell($w['dob'], $lineHeight, $student->date_of_birth ? $student->date_of_birth : '-', 'R', 0, 'C', $fill);
                $pdf->Cell($w['phone'], $lineHeight, $student->father_phone ?? '-', 'R', 0, 'C', $fill);
                $pdf->Cell($w['status'], $lineHeight, $status, 'R', 1, 'C', $fill); // ln=1 moves down

                // Draw bottom border for the row
                $pdf->Line($pdf->getMargins()['left'], $pdf->GetY(), $pdf->getPageWidth() - $pdf->getMargins()['right'], $pdf->GetY());
                $fill = !$fill; // Toggle fill
            }
        }

        // --- Output ---
        $fileName = 'student_list_report.pdf';
        if (!empty($filters)) {
            $fileName = 'student_list_filtered_' . date('Y-m-d_H-i-s') . '.pdf';
        }
        $pdf->Output($fileName, 'I');
        exit;
    }

    /**
     * Search student by ID.
     * GET /students/search/{id}
     */
    public function searchById($id)
    {
        $student = Student::with([
            'wishedSchool',
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.classroom',
            'enrollments.feeInstallments',
            'enrollments.deportationPath',
        ])->find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return new StudentResource($student);
    }

    /**
     * Generate Terms & Conditions PDF.
     * GET /reports/terms-and-conditions
     */
    public function generateTermsAndConditionsPdf()
    {
        $pdf = new TermsConditionsPdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('الشروط والأحكام');
        $pdf->SetSubject('الشروط والأحكام للتسجيل');
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();
        $pdf->addTermsBody();
        $pdf->Output('terms_and_conditions.pdf', 'I');
        exit;
    }

    /**
     * Export revenues data to Excel.
     * GET /reports/revenues-excel
     */
    public function exportRevenuesExcel(Request $request)
    {
        // Build the same query as the PDF method
        $query = Student::with([
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.classroom',
            'enrollments.feeInstallments',
        ]);

        // Apply same filters used in index for enrollments-related fields
        if ($request->boolean('only_enrolled')) {
            $query->whereHas('enrollments');
        }
        if ($request->boolean('only_approved')) {
            $query->where('approved', true);
        }
        
        // Only students with no payments filter
        if ($request->boolean('only_no_payments')) {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('studentLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }
        
        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->get('school_id'));
            });
        }
        if ($request->filled('grade_level_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('grade_level_id', $request->get('grade_level_id'));
            });
        }
        if ($request->filled('classroom_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('classroom_id', $request->get('classroom_id'));
            });
        }
        if ($request->filled('enrollment_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('enrollment_type', $request->get('enrollment_type'));
            });
        }
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', "%{$searchTerm}%")
                  ->orWhere('goverment_id', 'like', "%{$searchTerm}%");
            });
        }

        $students = $query->orderBy('id', 'desc')->get();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('الايرادات - رسوم الطلاب');

        // Workbook defaults & RTL
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('الايرادات - رسوم الطلاب')
            ->setSubject('تقرير ايرادات رسوم الطلاب');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->setRightToLeft(true);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Build filter info text early (used in row 2)
        $filterInfo = [];
        if ($request->filled('school_id')) {
            $school = \App\Models\School::find($request->input('school_id'));
            if ($school) {
                $filterInfo[] = "المدرسة: " . $school->name;
            }
        }
        if ($request->filled('grade_level_id')) {
            $gradeLevel = \App\Models\GradeLevel::find($request->input('grade_level_id'));
            if ($gradeLevel) {
                $filterInfo[] = "المرحلة: " . $gradeLevel->name;
            }
        }
        if ($request->filled('classroom_id')) {
            $classroom = \App\Models\Classroom::find($request->input('classroom_id'));
            if ($classroom) {
                $filterInfo[] = "الفصل: " . $classroom->name;
            }
        }
        if ($request->boolean('only_no_payments')) {
            $filterInfo[] = "غير مدفوع";
        }
        if ($request->filled('enrollment_type')) {
            $typeLabels = [
                'regular' => 'عادي',
                'scholarship' => 'منحة',
                'free' => 'إعفاء'
            ];
            $filterInfo[] = "نوع التسجيل: " . ($typeLabels[$request->input('enrollment_type')] ?? $request->input('enrollment_type'));
        }
        if ($request->filled('search')) {
            $filterInfo[] = "البحث: " . $request->input('search');
        }

        // Title row (row 1)
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'الايرادات - رسوم الطلاب (' . now()->format('Y-m-d H:i') . ')');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Filter info row (row 2)
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'بدون فلاتر' : ('فلترة: ' . implode(' | ', $filterInfo)));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]
        ]);

        // Headers (row 3)
        $headers = [
            'A3' => 'رقم التسجيل',
            'B3' => 'اسم الطالب',
            'C3' => 'المدرسة',
            'D3' => 'المرحلة',
            'E3' => 'الفصل',
            'F3' => 'الرسوم (دفتر)',
            'G3' => 'المدفوع (دفتر)',
            'H3' => 'الخصومات',
            'I3' => 'المتبقي'
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerRange = 'A3:I3';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        // Freeze panes below header
        $sheet->freezePane('A4');

        // Get ledger summaries for all enrollments
        $enrollmentIds = $students->pluck('enrollments')->flatten()->pluck('id')->unique()->toArray();
        $ledgerSummaries = [];
        
        if (!empty($enrollmentIds)) {
            $summaries = StudentLedger::selectRaw('
                enrollment_id,
                SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END) as total_fees,
                SUM(CASE WHEN transaction_type = "payment" THEN amount ELSE 0 END) as total_payments,
                SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END) as total_discounts,
                SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END) as total_refunds,
                SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as total_adjustments
            ')
            ->whereIn('enrollment_id', $enrollmentIds)
            ->groupBy('enrollment_id')
            ->get();

            foreach ($summaries as $summary) {
                $ledgerSummaries[$summary->enrollment_id] = $summary;
            }
        }

        // Add data rows
        $dataStartRow = 4;
        $row = $dataStartRow;
        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            if (!$enrollment) continue;

            $enrollmentId = $enrollment->id;
            $summary = $ledgerSummaries[$enrollmentId] ?? null;

            // Calculate values
            $totalFees = $summary ? $summary->total_fees : $enrollment->fees;
            $totalPayments = $summary ? $summary->total_payments : 0;
            $totalDiscounts = $summary ? $summary->total_discounts : 0;
            $remaining = max(0, $totalFees - $totalPayments - $totalDiscounts);

            $sheet->setCellValue("A{$row}", (int)$enrollmentId);
            $sheet->setCellValue("B{$row}", $student->student_name ?? '');
            $sheet->setCellValue("C{$row}", $enrollment->school->name ?? '');
            $sheet->setCellValue("D{$row}", $enrollment->gradeLevel->name ?? '');
            $sheet->setCellValue("E{$row}", $enrollment->classroom->name ?? '');
            $sheet->setCellValue("F{$row}", (float)$totalFees);
            $sheet->setCellValue("G{$row}", (float)$totalPayments);
            $sheet->setCellValue("H{$row}", (float)$totalDiscounts);
            $sheet->setCellValue("I{$row}", (float)$remaining);

            // Style data rows
            $dataRange = "A{$row}:I{$row}";
            $sheet->getStyle($dataRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            // Highlight rows with discounts or no payments
            if ($totalDiscounts > 0) {
                $sheet->getStyle($dataRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC']
                    ]
                ]);
            } elseif ($totalPayments == 0) {
                $sheet->getStyle($dataRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6E6']
                    ]
                ]);
            }

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Number formats for numeric columns
        $dataEndRow = $row - 1;
        if ($dataEndRow >= $dataStartRow) {
            $sheet->getStyle("F{$dataStartRow}:I{$dataEndRow}")
                ->getNumberFormat()->setFormatCode('#,##0');

            // Auto filter on header + data
            $sheet->setAutoFilter("A3:I{$dataEndRow}");

            // Totals row
            $totalsRow = $dataEndRow + 1;
            $sheet->setCellValue("E{$totalsRow}", 'الإجمالي');
            $sheet->setCellValue("F{$totalsRow}", "=SUBTOTAL(9,F{$dataStartRow}:F{$dataEndRow})");
            $sheet->setCellValue("G{$totalsRow}", "=SUBTOTAL(9,G{$dataStartRow}:G{$dataEndRow})");
            $sheet->setCellValue("H{$totalsRow}", "=SUBTOTAL(9,H{$dataStartRow}:H{$dataEndRow})");
            $sheet->setCellValue("I{$totalsRow}", "=SUBTOTAL(9,I{$dataStartRow}:I{$dataEndRow})");
            $sheet->getStyle("E{$totalsRow}:I{$totalsRow}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM]
                ]
            ]);
            $sheet->getStyle("F{$totalsRow}:I{$totalsRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // Page setup for printing
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.3)->setRight(0.3);

        // Generate filename
        $filename = 'revenues_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Generate Deportation Revenues PDF based on student filters.
     * GET /reports/deportation-revenues
     */
    public function deportationRevenuesPdf(Request $request)
    {
        $query = Student::with([
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.deportationPath',
        ]);

        // Apply same filters used in index for enrollments-related fields
        if ($request->boolean('only_enrolled')) {
            $query->whereHas('enrollments');
        }
        if ($request->boolean('only_approved')) {
            $query->where('approved', true);
        }
        
        // Only deportation enrollments
        $query->whereHas('enrollments', function ($q) {
            $q->where('deportation', true);
        });
        
        // Only students with no payments filter (using deportation ledgers)
        if ($request->boolean('only_no_payments')) {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('deportationLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }
        
        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->get('school_id'));
            });
        }
        if ($request->filled('grade_level_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('grade_level_id', $request->get('grade_level_id'));
            });
        }
        if ($request->filled('deportation_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('deportation_type', $request->get('deportation_type'));
            });
        }
        if ($request->filled('deportation_path_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('deportation_path_id', $request->get('deportation_path_id'));
            });
        }
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', "%{$searchTerm}%")
                  ->orWhere('goverment_id', 'like', "%{$searchTerm}%");
            });
        }

        $students = $query->get();

        // Collect first enrollment IDs (latest by created_at) to summarize ledgers
        $enrollmentIds = [];
        foreach ($students as $student) {
            $firstEnrollment = $student->enrollments->sortByDesc(function ($en) {
                return $en->created_at?->timestamp ?? 0;
            })->first();
            if ($firstEnrollment && $firstEnrollment->id) {
                $enrollmentIds[] = $firstEnrollment->id;
            }
        }

        $byEnrollment = [];
        $grandFees = 0.0;
        $grandPayments = 0.0;
        $grandDiscounts = 0.0;
        $grandRefunds = 0.0;
        $grandAdjustments = 0.0;
        if (!empty($enrollmentIds)) {
            $summaryRows = StudentDeportationLedger::whereIn('enrollment_id', $enrollmentIds)
                ->selectRaw('
                    enrollment_id,
                    SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END) as total_fees,
                    SUM(CASE WHEN transaction_type = "payment" THEN ABS(amount) ELSE 0 END) as total_payments,
                    SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END) as total_discounts,
                    SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END) as total_refunds,
                    SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as total_adjustments
                ')
                ->groupBy('enrollment_id')
                ->get();

            foreach ($summaryRows as $row) {
                $byEnrollment[$row->enrollment_id] = [
                    'total_fees' => (float)$row->total_fees,
                    'total_payments' => (float)$row->total_payments,
                    'total_discounts' => (float)$row->total_discounts,
                    'total_refunds' => (float)$row->total_refunds,
                    'total_adjustments' => (float)$row->total_adjustments,
                ];
                $grandFees += (float)$row->total_fees;
                $grandPayments += (float)$row->total_payments;
                $grandDiscounts += (float)$row->total_discounts;
                $grandRefunds += (float)$row->total_refunds;
                $grandAdjustments += (float)$row->total_adjustments;
            }
        }

        $global = [
            'total_expected' => $grandFees,
            'total_paid' => $grandPayments,
            'total_discounts' => $grandDiscounts,
            'total_refunds' => $grandRefunds,
            'total_adjustments' => $grandAdjustments,
            'total_balance' => max($grandFees - $grandPayments - $grandDiscounts + $grandRefunds + $grandAdjustments, 0),
            'count' => $students->count(),
            'by_enrollment' => $byEnrollment,
        ];

        $pdf = new RevenueListPdf($students, $global);
        $pdf->SetTitle('تقرير الإيرادات - تسجيلات الترحيل');
        $pdf->render();

        // Inline display instead of attachment
        return response($pdf->Output('deportation-revenues.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="deportation-revenues.pdf"');
    }

    /**
     * Export deportation revenues data to Excel.
     * GET /reports/deportation-revenues-excel
     */
    public function exportDeportationRevenuesExcel(Request $request)
    {
        // Build the same query as the PDF method
        $query = Student::with([
            'enrollments.school',
            'enrollments.gradeLevel',
            'enrollments.deportationPath',
        ]);

        // Apply same filters used in index for enrollments-related fields
        if ($request->boolean('only_enrolled')) {
            $query->whereHas('enrollments');
        }
        if ($request->boolean('only_approved')) {
            $query->where('approved', true);
        }
        
        // Only deportation enrollments
        $query->whereHas('enrollments', function ($q) {
            $q->where('deportation', true);
        });
        
        // Only students with no payments filter (using deportation ledgers)
        if ($request->boolean('only_no_payments')) {
            $query->whereHas('enrollments', function ($q) {
                $q->whereDoesntHave('deportationLedgers', function ($ledgerQuery) {
                    $ledgerQuery->where('transaction_type', 'payment');
                });
            });
        }
        
        if ($request->filled('school_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('school_id', $request->get('school_id'));
            });
        }
        if ($request->filled('grade_level_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('grade_level_id', $request->get('grade_level_id'));
            });
        }
        if ($request->filled('deportation_type')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('deportation_type', $request->get('deportation_type'));
            });
        }
        if ($request->filled('deportation_path_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('deportation_path_id', $request->get('deportation_path_id'));
            });
        }
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', "%{$searchTerm}%")
                  ->orWhere('goverment_id', 'like', "%{$searchTerm}%");
            });
        }

        $students = $query->orderBy('id', 'desc')->get();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('الايرادات - تسجيلات الترحيل');

        // Workbook defaults & RTL
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('الايرادات - تسجيلات الترحيل')
            ->setSubject('تقرير ايرادات تسجيلات الترحيل');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->setRightToLeft(true);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Build filter info text early (used in row 2)
        $filterInfo = [];
        if ($request->filled('school_id')) {
            $school = \App\Models\School::find($request->input('school_id'));
            if ($school) {
                $filterInfo[] = "المدرسة: " . $school->name;
            }
        }
        if ($request->filled('grade_level_id')) {
            $gradeLevel = \App\Models\GradeLevel::find($request->input('grade_level_id'));
            if ($gradeLevel) {
                $filterInfo[] = "المرحلة: " . $gradeLevel->name;
            }
        }
        if ($request->filled('deportation_type')) {
            $filterInfo[] = "نوع الترحيل: " . $request->input('deportation_type');
        }
        if ($request->filled('deportation_path_id')) {
            $deportationPath = DeportationPath::find($request->input('deportation_path_id'));
            if ($deportationPath) {
                $filterInfo[] = "مسار الترحيل: " . $deportationPath->name;
            }
        }
        if ($request->boolean('only_no_payments')) {
            $filterInfo[] = "غير مدفوع";
        }
        if ($request->filled('search')) {
            $filterInfo[] = "البحث: " . $request->input('search');
        }

        // Title row (row 1)
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'الايرادات - تسجيلات الترحيل (' . now()->format('Y-m-d H:i') . ')');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Filter info row (row 2)
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'بدون فلاتر' : ('فلترة: ' . implode(' | ', $filterInfo)));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]
        ]);

        // Headers (row 3)
        $headers = [
            'A3' => 'رقم التسجيل',
            'B3' => 'اسم الطالب',
            'C3' => 'المدرسة',
            'D3' => 'نوع الترحيل',
            'E3' => 'مسار الترحيل',
            'F3' => 'الرسوم (دفتر)',
            'G3' => 'المدفوع (دفتر)',
            'H3' => 'الخصومات',
            'I3' => 'المتبقي'
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerRange = 'A3:I3';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        // Freeze panes below header
        $sheet->freezePane('A4');

        // Get ledger summaries for all enrollments using deportation ledgers
        $enrollmentIds = $students->pluck('enrollments')->flatten()->pluck('id')->unique()->toArray();
        $ledgerSummaries = [];
        
        if (!empty($enrollmentIds)) {
            $summaries = StudentDeportationLedger::selectRaw('
                enrollment_id,
                SUM(CASE WHEN transaction_type = "fee" THEN amount ELSE 0 END) as total_fees,
                SUM(CASE WHEN transaction_type = "payment" THEN ABS(amount) ELSE 0 END) as total_payments,
                SUM(CASE WHEN transaction_type = "discount" THEN amount ELSE 0 END) as total_discounts,
                SUM(CASE WHEN transaction_type = "refund" THEN amount ELSE 0 END) as total_refunds,
                SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as total_adjustments
            ')
            ->whereIn('enrollment_id', $enrollmentIds)
            ->groupBy('enrollment_id')
            ->get();

            foreach ($summaries as $summary) {
                $ledgerSummaries[$summary->enrollment_id] = $summary;
            }
        }

        // Add data rows
        $dataStartRow = 4;
        $row = $dataStartRow;
        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            if (!$enrollment) continue;

            $enrollmentId = $enrollment->id;
            $summary = $ledgerSummaries[$enrollmentId] ?? null;

            // Calculate values
            $totalFees = $summary ? $summary->total_fees : 0;
            $totalPayments = $summary ? $summary->total_payments : 0;
            $totalDiscounts = $summary ? $summary->total_discounts : 0;
            $remaining = max(0, $totalFees - $totalPayments - $totalDiscounts);

            $sheet->setCellValue("A{$row}", (int)$enrollmentId);
            $sheet->setCellValue("B{$row}", $student->student_name ?? '');
            $sheet->setCellValue("C{$row}", $enrollment->school->name ?? '');
            $sheet->setCellValue("D{$row}", $enrollment->deportation_type ?? '');
            $sheet->setCellValue("E{$row}", $enrollment->deportationPath->name ?? '');
            $sheet->setCellValue("F{$row}", (float)$totalFees);
            $sheet->setCellValue("G{$row}", (float)$totalPayments);
            $sheet->setCellValue("H{$row}", (float)$totalDiscounts);
            $sheet->setCellValue("I{$row}", (float)$remaining);

            // Style data rows
            $dataRange = "A{$row}:I{$row}";
            $sheet->getStyle($dataRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            // Highlight rows with discounts or no payments
            if ($totalDiscounts > 0) {
                $sheet->getStyle($dataRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC']
                    ]
                ]);
            } elseif ($totalPayments == 0) {
                $sheet->getStyle($dataRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6E6']
                    ]
                ]);
            }

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Number formats for numeric columns
        $dataEndRow = $row - 1;
        if ($dataEndRow >= $dataStartRow) {
            $sheet->getStyle("F{$dataStartRow}:I{$dataEndRow}")
                ->getNumberFormat()->setFormatCode('#,##0');

            // Auto filter on header + data
            $sheet->setAutoFilter("A3:I{$dataEndRow}");

            // Totals row
            $totalsRow = $dataEndRow + 1;
            $sheet->setCellValue("E{$totalsRow}", 'الإجمالي');
            $sheet->setCellValue("F{$totalsRow}", "=SUBTOTAL(9,F{$dataStartRow}:F{$dataEndRow})");
            $sheet->setCellValue("G{$totalsRow}", "=SUBTOTAL(9,G{$dataStartRow}:G{$dataEndRow})");
            $sheet->setCellValue("H{$totalsRow}", "=SUBTOTAL(9,H{$dataStartRow}:H{$dataEndRow})");
            $sheet->setCellValue("I{$totalsRow}", "=SUBTOTAL(9,I{$dataStartRow}:I{$dataEndRow})");
            $sheet->getStyle("E{$totalsRow}:I{$totalsRow}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM]
                ]
            ]);
            $sheet->getStyle("F{$totalsRow}:I{$totalsRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // Page setup for printing
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.3)->setRight(0.3);

        // Generate filename
        $filename = 'deportation-revenues_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
