<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Resources\StudentResource; // Import the API Resource class (if you create one)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Import the Validator class
use TCPDF;

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
    public function index()
    {
        $students = Student::all();
        return  StudentResource::collection($students);
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
            'wished_level' => 'required|in:روضه,ابتدائي,متوسط,ثانوي',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Concatenate the error messages into a single string
            $errorMessage = '';
            foreach ($errors->all() as $error) {
                $errorMessage .= $error . ' '; // Add a space between messages
            }

            return response()->json(['message' => $errorMessage], 422);
        }
        $data = $request->all();
        $data['approved_by_user'] = Auth::id();
        $student = Student::create($data);

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }
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
            'wished_level' => 'required|in:روضه,ابتدائي,متوسط,ثانوي',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => implode(' ', $validator->errors()->all())], 422); // Unprocessable Entity
        }

        $student->update($request->all());
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
        // Set default font that supports Arabic - CRUCIAL
        $pdf->SetFont('dejavusans', '', 10); // ''=regular, 'B'=bold, 'I'=italic

        // --- Add a page ---
        $pdf->AddPage();

        // --- Content Generation using Cell() ---

        // --- Title Section ---
        $pdf->SetFont('dejavusans', 'B', 16);
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
        $printRow = function (string $label, ?string $value, bool $isBoldValue = false) use ($pdf, $labelWidth, $valueWidth, $lineHeight) {
            $pdf->SetFont('dejavusans', 'B', 10); // Bold Label
            $pdf->Cell($labelWidth, $lineHeight, $label . ':', 0, 0, 'R'); // Right align label
            $pdf->SetFont('dejavusans', $isBoldValue ? 'B' : '', 10); // Value font
            $pdf->Cell($valueWidth, $lineHeight, $value ?? '-', 0, 1, 'R'); // Right align value, ln=1 to move down
        };

        // --- Basic Info Section ---
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'المعلومات الأساسية', 'B', 1, 'R'); // Border Bottom, Right align
        $pdf->Ln(3);
        $printRow('الاسم الكامل', $student->student_name, true); // Make name bold
        $printRow('تاريخ الميلاد', $student->date_of_birth ? $student->date_of_birth: null); // Format date
        $printRow('الجنس', $student->gender);
        $printRow('المرحلة المرغوبة', $student->wished_level);
        $printRow('الرقم الوطني', $student->goverment_id);
        $printRow('البريد الإلكتروني', $student->email);
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
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'أقرب شخص للطالب', 'B', 1, 'R');
        $pdf->Ln(3);
        $printRow('الاسم', $student->closest_name);
        $printRow('الهاتف', $student->closest_phone);
        $pdf->Ln(6);

        // --- Approval Status Section ---
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'حالة القبول', 'B', 1, 'R');
        $pdf->Ln(3);
        $printRow('الحالة', $student->approved ? 'مقبول' : 'قيد المراجعة');
        $printRow('تاريخ القبول', $student->aproove_date ? $student->aproove_date: null); // Check spelling 'aproove_date'
        $printRow('تم القبول بواسطة (ID)', $student->approved_by_user ? (string)$student->approved_by_user : null);
        $printRow('تم إرسال الرسالة', $student->message_sent ? 'نعم' : 'لا');
        $pdf->Ln(6);


        // --- Output the PDF ---
        // 'I': Inline display in browser
        // 'D': Force download
        // 'F': Save to file path
        // 'S': Return as string
        $pdf->Output('student_report_' . $student->id . '.pdf', 'I');
        exit; // Stop script execution after PDF output

    }
}
