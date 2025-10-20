<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectResource;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\TeacherResource; // Import the resource
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import Storage
use Illuminate\Validation\Rule; // For unique validation on update
use TCPDF; // TCPDF for PDF generation

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optional: Add Authorization Check
        // $this->authorize('viewAny', Teacher::class);

        // Use pagination for better performance
        $teachers = Teacher::latest()->paginate(15); // Example: 15 per page
        return TeacherResource::collection($teachers);
    }
 /**
     * Get the subjects assigned to a specific teacher.
     * GET /api/teachers/{teacher}/subjects
     */
    public function getSubjects(Teacher $teacher)
    {
        // $this->authorize('view', $teacher); // Optional: Check if user can view teacher details

        // Eager load the subjects relationship
        $teacher->load('subjects');

        // Return the collection of subjects using SubjectResource
        return SubjectResource::collection($teacher->subjects);
    }

    /**
     * Update/Sync the subjects assigned to a specific teacher.
     * PUT /api/teachers/{teacher}/subjects
     */
    public function updateSubjects(Request $request, Teacher $teacher)
    {
        // $this->authorize('update', $teacher); // Optional: Check if user can update teacher details

        $validator = Validator::make($request->all(), [
            // Expect an array of subject IDs. Allow empty array to remove all subjects.
            'subject_ids' => 'present|array', // 'present' ensures the key exists, even if empty array
            'subject_ids.*' => 'integer|exists:subjects,id' // Validate each item in the array
        ]);

        if ($validator->fails()) {
            // Get the first error message from each field
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            
            // Join all error messages into a single message
            $consolidatedMessage = implode('، ', $errorMessages);
            
            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
        }

        // Use sync to update the pivot table.
        // This adds missing IDs, removes IDs not present in the array.
        $teacher->subjects()->sync($validator->validated()['subject_ids']);

        // Return success response, maybe with the updated list
         $teacher->load('subjects'); // Reload the relationship
         return SubjectResource::collection($teacher->subjects);
        // Or just a success message:
        // return response()->json(['message' => 'تم تحديث مواد المدرس بنجاح']);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Optional: Add Authorization Check
        // $this->authorize('create', Teacher::class);

        $validator = Validator::make($request->all(), [
            // Required core fields
            'national_id' => 'required|string|max:20|unique:teachers,national_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:teachers,email',
            'gender' => 'required|in:ذكر,انثي',
            'qualification' => 'required|string|max:255',
            'hire_date' => 'required|date_format:Y-m-d',

            // Optional contact
            'phone' => 'nullable|string|max:15',
            'secondary_phone' => 'nullable|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',

            // Optional personal details
            'birth_date' => 'nullable|date_format:Y-m-d',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'document_type' => 'nullable|in:جواز سفر,البطاقة الشخصية,الرقم الوطني',
            'document_number' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:اعزب,متزوج,مطلق,ارمل',
            'number_of_children' => 'nullable|integer',
            'children_in_school' => 'nullable|integer',

            // Optional education/professional
            'highest_qualification' => 'nullable|in:جامعي,ثانوي',
            'specialization' => 'nullable|string|max:255',
            'academic_degree' => 'nullable|in:دبلوم,بكالوريوس,ماجستير,دكتوراه',
            'appointment_date' => 'nullable|date_format:Y-m-d',
            'years_of_teaching_experience' => 'nullable|integer',
            'training_courses' => 'nullable|string',

            // Files/paths
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'academic_qualifications_doc_path' => 'nullable|string',
            'personal_id_doc_path' => 'nullable|string',
            'cv_doc_path' => 'nullable|string',

            // Flags
            // 'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            // Get the first error message from each field
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            
            // Join all error messages into a single message
            $consolidatedMessage = implode('، ', $errorMessages);
            
            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $photoPath = null;

        // Handle File Upload
        if ($request->hasFile('photo')) {
            // Store in 'public/teachers' directory, returns 'teachers/filename.jpg'
            $photoPath = $request->file('photo')->store('teachers', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Ensure is_active defaults correctly if not sent
        $validatedData['is_active'] = $request->boolean('is_active', true);

        $teacher = Teacher::create($validatedData);

        return new TeacherResource($teacher); // Return resource with 201 status (implicit)
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher) // Route model binding
    {
        // Optional: Add Authorization Check
        // $this->authorize('view', $teacher);

        return new TeacherResource($teacher);
    }

    /**
     * Update the specified resource in storage.
     * Note: We use POST with _method=PUT/PATCH for file uploads from HTML forms,
     * but APIs often use PUT/PATCH directly. Axios handles this.
     */
    public function update(Request $request, Teacher $teacher)
    {
        // Optional: Add Authorization Check
        // $this->authorize('update', $teacher);

        $validator = Validator::make($request->all(), [
            // Required core fields
            'national_id' => ['required', 'string', 'max:20', Rule::unique('teachers')->ignore($teacher->id)],
            'name' => 'sometimes|required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('teachers')->ignore($teacher->id)],
            'gender' => 'sometimes|required|in:ذكر,انثي',
            'qualification' => 'sometimes|required|string|max:255',
            'hire_date' => 'sometimes|required|date_format:Y-m-d',

            // Optional contact
            'phone' => 'nullable|string|max:15',
            'secondary_phone' => 'nullable|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',

            // Optional personal details
            'birth_date' => 'nullable|date_format:Y-m-d',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'document_type' => 'nullable|in:جواز سفر,البطاقة الشخصية,الرقم الوطني',
            'document_number' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:اعزب,متزوج,مطلق,ارمل',
            'number_of_children' => 'nullable|integer',
            'children_in_school' => 'nullable|integer',

            // Optional education/professional
            'highest_qualification' => 'nullable|in:جامعي,ثانوي',
            'specialization' => 'nullable|string|max:255',
            'academic_degree' => 'nullable|in:دبلوم,بكالوريوس,ماجستير,دكتوراه',
            'appointment_date' => 'nullable|date_format:Y-m-d',
            'years_of_teaching_experience' => 'nullable|integer',
            'training_courses' => 'nullable|string',

            // Files/paths
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'academic_qualifications_doc_path' => 'nullable|string',
            'personal_id_doc_path' => 'nullable|string',
            'cv_doc_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // Get the first error message from each field
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            
            // Join all error messages into a single message
            $consolidatedMessage = implode('، ', $errorMessages);
            
            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $photoPath = $teacher->photo; // Keep old path by default

        // Handle File Update
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            // Store new photo
            $photoPath = $request->file('photo')->store('teachers', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Update boolean field correctly if sent
        if ($request->has('is_active')) {
             $validatedData['is_active'] = $request->boolean('is_active');
        }

        $teacher->update($validatedData);

        return new TeacherResource($teacher->fresh()); // Return updated resource
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        // Optional: Add Authorization Check
        // $this->authorize('delete', $teacher);

        // Delete photo file if it exists
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $teacher->delete(); // Performs soft delete if trait is used

        // Return 204 No Content or a success message
        // return response()->noContent();
         return response()->json(['message' => 'تم حذف المدرس بنجاح'], 200);
    }

    /**
     * Upload multiple PDF documents for a teacher and store them under a per-teacher folder.
     * POST /api/teachers/{teacher}/documents
     */
    public function uploadDocuments(Request $request, Teacher $teacher)
    {
        // $this->authorize('update', $teacher); // Optional authorization

        $validator = Validator::make($request->all(), [
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf|max:20480', // max 20MB per file
        ]);

        if ($validator->fails()) {
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            $consolidatedMessage = implode('، ', $errorMessages);

            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
        }

        $storedFiles = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                // Store under public/teachers/{id}/documents
                $path = $file->store('teachers/' . $teacher->id . '/documents', 'public');
                if ($path) {
                    $storedFiles[] = $path;
                }
            }
        }

        return response()->json([
            'message' => 'تم رفع المستندات بنجاح',
            'files' => $storedFiles,
        ], 201);
    }

    /**
     * List teacher uploaded documents (PDFs) under public path.
     * GET /api/teachers/{teacher}/documents
     */
    public function listDocuments(Teacher $teacher)
    {
        $directory = 'teachers/' . $teacher->id . '/documents';
        $files = [];
        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);
        }
        return response()->json([
            'files' => $files,
        ]);
    }

    /**
     * Web endpoint: generate and stream a PDF profile for the teacher using TCPDF.
     */
    public function pdfWeb(Teacher $teacher)
    {
        // Helpers
        $show = function ($value) {
            return ($value === null || $value === '') ? 'غير محدد' : (is_bool($value) ? ($value ? 'نعم' : 'لا') : (string) $value);
        };
        $formatDate = function ($date) use ($show) {
            if ($date instanceof \Carbon\Carbon) {
                return $date->format('d/m/Y');
            }
            return $show($date);
        };

        // Resolve absolute path for photo if exists
        $photoAbsPath = null;
        if (!empty($teacher->photo)) {
            $candidate = public_path('storage/' . $teacher->photo);
            if (is_string($candidate) && file_exists($candidate)) {
                $photoAbsPath = $candidate;
            }
        }

        // Check for school logo
        $logoPath = public_path('logo.png');
        $logoExists = file_exists($logoPath);

		// Create new PDF document with custom header/footer (override Header/Footer)
		$pdf = new class($teacher, $logoExists, $logoPath) extends TCPDF {
			private Teacher $teacher;
			private bool $logoExists;
			private string $logoPath;
			public function __construct(Teacher $teacher, bool $logoExists, string $logoPath) {
				parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$this->teacher = $teacher;
				$this->logoExists = $logoExists;
				$this->logoPath = $logoPath;
			}
			public function Header(): void {
				$this->SetY(10);
				$this->SetFont('arial', 'B', 14);
				if ($this->logoExists) {
					$this->Image($this->logoPath, 55, 0, 50, 40, 'PNG');
				}
				$this->SetTextColor(31, 78, 121);
				$this->Cell(0, 8, 'مدارس الفنار', 0, 1, 'C');
				$this->SetFont('arial', '', 10);
				$this->SetTextColor(100, 100, 100);
				$this->Cell(0, 6, 'ملف المدرس - ' . $this->teacher->name, 0, 1, 'C');
				$this->SetTextColor(0, 0, 0);
				// Line separator
				$this->SetLineStyle(['width' => 0.5, 'color' => [31, 78, 121]]);
				$this->Line(15, 32, 195, 32);
			}
			public function Footer(): void {
				$this->SetY(-15);
				$this->SetFont('arial', 'I', 8);
				$this->SetTextColor(100, 100, 100);
				// Line separator
				$this->SetLineStyle(['width' => 0.3, 'color' => [200, 200, 200]]);
				$this->Line(15, $this->GetY() - 2, 195, $this->GetY() - 2);
				$this->Cell(0, 10, 'تاريخ الطباعة: ' . date('Y/m/d H:i') . ' | الصفحة ' . $this->getAliasNumPage() . ' من ' . $this->getAliasNbPages(), 0, 0, 'C');
			}
		};

        // Document meta
        $pdf->SetCreator('نظام إدارة المدرسة');
        $pdf->SetAuthor('نظام إدارة المدرسة');
        $pdf->SetTitle('ملف المدرس - ' . $teacher->name);
        $pdf->SetSubject('ملف المدرس');

		// Header/Footer will be rendered by overridden methods

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        // RTL and font for Arabic
        $pdf->setRTL(true);
        $pdf->SetFont('arial', '', 10);

        // Margins
        $pdf->SetMargins(15, 38, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);

        // Add a page
        $pdf->AddPage();

        // Color scheme
        $primaryColor = '#1F4E79';
        $secondaryColor = '#5B9BD5';
        $lightBg = '#F2F2F2';
        $successColor = '#70AD47';
        $dangerColor = '#C00000';

        // Header Card with Photo
        $statusColor = $teacher->is_active ? $successColor : $dangerColor;
        $statusText = $teacher->is_active ? 'نشط' : 'غير نشط';
        
        $headerCard = '<table width="100%" cellspacing="0" cellpadding="12" style="background-color:' . $lightBg . '; border-radius:8px; margin-bottom:15px;">
            <tr>
                <td width="75%" style="vertical-align:middle;">
                    <h2 style="margin:0; color:' . $primaryColor . '; font-size:18px;">' . e($teacher->name ?? 'غير محدد') . '</h2>
                    <p style="margin:4px 0 0 0; color:#666; font-size:11px;">
                        <b>الرقم الوطني:</b> ' . e($teacher->national_id ?? 'غير محدد') . ' | 
                        <b>رقم المعرف:</b> ' . e($teacher->id) . ' | 
                        <b>الحالة:</b> <span style="color:' . $statusColor . '; font-weight:bold;">' . $statusText . '</span>
                    </p>
                </td>
                <td width="25%" style="text-align:left; vertical-align:middle;">';
        
        if ($photoAbsPath) {
            $headerCard .= '<img src="' . $photoAbsPath . '" width="80" height="80" style="border:3px solid ' . $primaryColor . '; border-radius:8px;" />';
        } else {
            $headerCard .= '<div style="width:80px; height:80px; background-color:#ddd; border:3px solid ' . $primaryColor . '; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#999; font-size:10px;">لا توجد صورة</div>';
        }
        
        $headerCard .= '</td>
            </tr>
        </table>';
        $pdf->writeHTML($headerCard, true, false, true, false, 'R');

		// CSS for sections
		$sectionHeaderStyle = 'background-color:' . $secondaryColor . '; color:#fff; padding:8px; font-size:12px; font-weight:bold; margin-top:8px; border-radius:4px;';
		// Ensure row cells do not exceed available width: 20% label + 30% value + 20% label + 30% value = 100%
		$tableCellLabel = 'background-color:' . $lightBg . '; font-weight:bold; padding:6px; border:1px solid #ddd; width:20%;';
		$tableCellValue = 'padding:6px; border:1px solid #ddd; width:30%;';
		// Wrapper to allow long words to break within value cells
		$wrapOpen = '<span style="word-break:break-word; white-space:normal; display:block;">';
		$wrapClose = '</span>';

        // Section: Basic Information
		$basicHtml = '<div style="' . $sectionHeaderStyle . '">المعلومات الأساسية</div>
          <table cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
            <tr>
              <td style="' . $tableCellLabel . '">الاسم الكامل</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->name)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">الرقم الوطني</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->national_id)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">الجنس</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->gender)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">البريد الإلكتروني</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->email)) . $wrapClose . '</td>
            </tr>
          </table>';
        $pdf->writeHTML($basicHtml, true, false, true, false, 'R');

        // Section: Contact
		$contactHtml = '<div style="' . $sectionHeaderStyle . '">بيانات التواصل</div>
          <table cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
            <tr>
              <td style="' . $tableCellLabel . '">رقم الهاتف</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->phone)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">هاتف آخر</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->secondary_phone)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">رقم الواتساب</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->whatsapp_number)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">العنوان</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->address)) . $wrapClose . '</td>
            </tr>
          </table>';
        $pdf->writeHTML($contactHtml, true, false, true, false, 'R');

        // Section: Personal
		$personalHtml = '<div style="' . $sectionHeaderStyle . '">البيانات الشخصية</div>
          <table cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
            <tr>
              <td style="' . $tableCellLabel . '">تاريخ الميلاد</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($formatDate($teacher->birth_date)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">مكان الميلاد</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->place_of_birth)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">الجنسية</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->nationality)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">نوع الوثيقة</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->document_type)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">رقم الوثيقة</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->document_number)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">الحالة الاجتماعية</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->marital_status)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">عدد الأطفال</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->number_of_children)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">أطفال بالمدرسة</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->children_in_school)) . $wrapClose . '</td>
            </tr>
          </table>';
        $pdf->writeHTML($personalHtml, true, false, true, false, 'R');

        // Section: Education
		$eduHtml = '<div style="' . $sectionHeaderStyle . '">المؤهلات العلمية</div>
          <table cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
            <tr>
              <td style="' . $tableCellLabel . '">المؤهل العلمي</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->qualification)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">أعلى مؤهل</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->highest_qualification)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">الدرجة العلمية</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->academic_degree)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">التخصص</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->specialization)) . $wrapClose . '</td>
            </tr>
          </table>';
        $pdf->writeHTML($eduHtml, true, false, true, false, 'R');

        // Section: Experience
		$expHtml = '<div style="' . $sectionHeaderStyle . '">الخبرة الوظيفية</div>
          <table cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
            <tr>
              <td style="' . $tableCellLabel . '">تاريخ التعيين</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($formatDate($teacher->hire_date)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">تاريخ التعيين بالمدرسة</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($formatDate($teacher->appointment_date)) . $wrapClose . '</td>
            </tr>
            <tr>
              <td style="' . $tableCellLabel . '">سنوات الخبرة</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->years_of_teaching_experience)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">الدورات التدريبية</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($show($teacher->training_courses)) . $wrapClose . '</td>
            </tr>
          </table>';
        $pdf->writeHTML($expHtml, true, false, true, false, 'R');

        // Section: System Meta
		$metaHtml = '<div style="' . $sectionHeaderStyle . '">بيانات النظام</div>
          <table cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
            <tr>
              <td style="' . $tableCellLabel . '">تاريخ الإنشاء</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($formatDate($teacher->created_at)) . $wrapClose . '</td>
              <td style="' . $tableCellLabel . '">آخر تحديث</td>
			  <td style="' . $tableCellValue . '">' . $wrapOpen . e($formatDate($teacher->updated_at)) . $wrapClose . '</td>
            </tr>
          </table>';
        $pdf->writeHTML($metaHtml, true, false, true, false, 'R');

        // Output PDF inline to the browser
        return response($pdf->Output('teacher-' . $teacher->id . '.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
}