<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentWarningResource;
use App\Models\StudentAcademicYear;
use App\Models\StudentWarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\StudentWarningPdf;

class StudentWarningController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => 'required|integer|exists:student_academic_years,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $warnings = StudentWarning::where('student_academic_year_id', $request->integer('student_academic_year_id'))
            ->orderByDesc('issued_at')
            ->orderByDesc('created_at')
            ->get();

        return StudentWarningResource::collection($warnings);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->can('manage student warnings'), 403);
        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => 'required|integer|exists:student_academic_years,id',
            'severity' => ['required', Rule::in(['low','medium','high'])],
            'reason' => 'required|string|min:3',
            'issued_at' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['issued_by_user_id'] = auth()->id();
        $warning = StudentWarning::create($data);
        return new StudentWarningResource($warning);
    }

    public function update(Request $request, StudentWarning $studentWarning)
    {
        abort_unless(auth()->user() && auth()->user()->can('manage student warnings'), 403);
        $validator = Validator::make($request->all(), [
            'severity' => ['sometimes','required', Rule::in(['low','medium','high'])],
            'reason' => 'sometimes|required|string|min:3',
            'issued_at' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $studentWarning->update($validator->validated());
        return new StudentWarningResource($studentWarning->fresh());
    }

    public function destroy(StudentWarning $studentWarning)
    {
        abort_unless(auth()->user() && auth()->user()->can('manage student warnings'), 403);
        $studentWarning->delete();
        return response()->json(['message' => 'تم حذف التنبيه بنجاح']);
    }

    /**
     * Generate a warning notice PDF similar to the provided template
     * GET /api/student-warnings/{studentWarning}/pdf
     */
    public function generatePdf(StudentWarning $studentWarning)
    {
        // Load related enrollment with student & school to access student name safely
        $enrollment = \App\Models\StudentAcademicYear::with(['student','school'])
            ->find($studentWarning->student_academic_year_id);

        $pdf = new StudentWarningPdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setRTL(true);
        $font_path = public_path('\\fonts').'\\arial.ttf';
        if (file_exists($font_path)) {
            \TCPDF_FONTS::addTTFfont($font_path);
            $font = 'arial';
        } else {
            $font = 'dejavusans';
        }
        $pdf->SetFont($font, '', 12);
        $pdf->SetMargins(15, 45, 15);
        $pdf->AddPage();

        // Date line
        $pdf->Cell(0, 8, 'التاريخ: ____________', 0, 1, 'R');
        $pdf->Ln(2);
        // Guardian line
        $guardianName = $enrollment?->student?->father_name ?? '__________________';
        $studentName = $enrollment?->student?->student_name ?? '__________________';
        $pdf->Cell(0, 8, 'الأخ الفاضل ولي أمر الطالب/ة: '.$studentName, 0, 1, 'R');
        $pdf->Ln(2);
        $pdf->Cell(0, 8, 'وبعد:', 0, 1, 'R');
        $pdf->Ln(2);
        $pdf->MultiCell(0, 8, 'حرصاً من إدارة المدرسة على توفير بيئة تعليمية منضبطة وملتزمة بالأنظمة،', 0, 'R');
        $pdf->Ln(3);
        $pdf->MultiCell(0, 8, 'نحيطكم علماً بأن الإبن/الإبنة: '.$studentName.'، وقد حدث عنه:', 0, 'R');
        $pdf->Ln(3);
        // Event/reason area (dotted)
        $reason = $studentWarning->reason;
        $pdf->MultiCell(0, 8, 'الحدث: '.$reason, 0, 'R');
        $pdf->Ln(2);
        $pdf->MultiCell(0, 8, 'وعليه: ________________________________', 0, 'R');
        $pdf->Ln(15);
        $pdf->MultiCell(0, 8, 'توقيع الطالب/ة: ____________________________    بـ: ____________________', 0, 'R');
        $pdf->Ln(10);
        $pdf->MultiCell(0, 8, 'توقيع الإدارة: ____________________________', 0, 'R');

        $pdf->Output('student_warning_'.$studentWarning->id.'.pdf', 'I');
        exit;
    }
}


