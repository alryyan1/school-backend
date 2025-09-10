<?php

namespace App\Http\Controllers;

use App\Models\StudentNote;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Resources\StudentNoteResource;
use Illuminate\Support\Facades\Auth;
use App\Helpers\StudentEnrollmentNotesPdf;

class StudentNoteController extends Controller
{
    // List all notes for a given enrollment_id
    public function index(Request $request)
    {
        $enrollmentId = $request->query('enrollment_id');
        $notes = StudentNote::with('user')
            ->where('enrollment_id', $enrollmentId)
            ->orderByDesc('created_at')
            ->get();
        return StudentNoteResource::collection($notes);
    }

    // Store a new note
    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'note' => 'required|string',
        ]);
        $note = StudentNote::create([
            'enrollment_id' => $request->enrollment_id,
            'note' => $request->note,
            'user_id' => Auth::id() ?? 1, // Default to user ID 1 if not authenticated
        ]);
        $note->load('user');
        return new StudentNoteResource($note);
    }

    // Update a note
    public function update(Request $request, StudentNote $studentNote)
    {
        $request->validate([
            'note' => 'required|string',
        ]);
        $studentNote->update([
            'note' => $request->note,
        ]);
        $studentNote->load('user');
        return new StudentNoteResource($studentNote);
    }

    // Delete a note
    public function destroy(StudentNote $studentNote)
    {
        $studentNote->delete();
        return response()->json(['message' => 'Note deleted']);
    }

    // Generate PDF for student enrollment notes
    public function generatePdf(Request $request)
    {
        $enrollmentId = $request->query('enrollment_id');
        
        if (!$enrollmentId) {
            return response()->json(['error' => 'enrollment_id is required'], 400);
        }

        // Get enrollment with student data
        $enrollment = Enrollment::with(['student', 'gradeLevel'])->find($enrollmentId);
        
        if (!$enrollment) {
            return response()->json(['error' => 'Enrollment not found'], 404);
        }

        // Get notes for this enrollment
        $notes = StudentNote::with('user')
            ->where('enrollment_id', $enrollmentId)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();

        // Create PDF
        $pdf = new StudentEnrollmentNotesPdf();
        $pdf->studentName = $enrollment->student->student_name ?? 'غير محدد';
        $pdf->enrollmentInfo = sprintf(
            'السنة الأكاديمية: %s | الصف: %s',
            $enrollment->academic_year ?? 'غير محدد',
            $enrollment->gradeLevel->name ?? 'غير محدد'
        );

        $pdf->SetTitle('ملاحظات تسجيل الطالب');
        $pdf->SetAuthor('نظام إدارة المدرسة');
        $pdf->SetSubject('ملاحظات تسجيل الطالب');
        $pdf->SetKeywords('ملاحظات, طالب, تسجيل');

        $pdf->AddPage();
        $pdf->generateNotesTable($notes);

        $filename = 'student_notes_' . $enrollmentId . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
