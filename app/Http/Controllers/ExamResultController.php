<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\ExamSchedule;
use App\Models\EnrollMent;
use Illuminate\Http\Request;
use App\Http\Resources\ExamResultResource;
use App\Http\Resources\EnrollmentResource; // For pending students
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For transaction
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExamResultController extends Controller
{
    use AuthorizesRequests; // For policy-based authorization

    /**
     * Get results for a specific ExamSchedule (all students who have results).
     * GET /api/exam-schedules/{examSchedule}/results
     */
    public function getResultsForSchedule(ExamSchedule $examSchedule)
    {
        // $this->authorize('viewResults', $examSchedule); // Example Policy check

        $results = ExamResult::with([
                'studentAcademicYear.student:id,student_name,image,goverment_id', // Select specific student fields
                // 'examSchedule:id,subject_id,max_marks', // Redundant as we have examSchedule
                // 'examSchedule.subject:id,name'
            ])
            ->where('exam_schedule_id', $examSchedule->id)
            ->get();

        return ExamResultResource::collection($results);
    }

    /**
     * Get list of students enrolled in the grade/year of an ExamSchedule
     * who DO NOT have results yet for that schedule.
     * GET /api/exam-schedules/{examSchedule}/pending-students-for-results
     */
    public function getPendingStudentsForResults(ExamSchedule $examSchedule)
    {
        // $this->authorize('enterMarks', $examSchedule); // Example Policy check

        $examSchedule->load('exam'); // Ensure parent exam is loaded for context

        if (!$examSchedule->exam) {
            return response()->json(['message' => 'Parent exam details missing for schedule.'], 400);
        }

        $studentsWithResultsIds = ExamResult::where('exam_schedule_id', $examSchedule->id)
                                        ->pluck('student_academic_year_id');

        $pendingStudentsEnrollments = EnrollMent::with('student:id,student_name,image,goverment_id')
            ->where('academic_year', $examSchedule->exam->academic_year)
            ->where('grade_level_id', $examSchedule->grade_level_id)
            ->where('school_id', $examSchedule->exam->school_id)
            ->where('status', 'active') // Only active students
            ->whereNotIn('id', $studentsWithResultsIds) // Exclude those with results
            ->join('students', 'enrollments.student_id', '=', 'students.id') // For ordering
            ->orderBy('students.student_name')
            ->select('enrollments.*') // Select all from enrollments after join
            ->get();

        // Use EnrollmentResource to include student details in a structured way
        return EnrollmentResource::collection($pendingStudentsEnrollments);
    }

    /**
     * Store or Update exam results for multiple students for a specific ExamSchedule.
     * POST /api/exam-schedules/{examSchedule}/results/bulk-upsert
     */
    public function bulkUpsertResults(Request $request, ExamSchedule $examSchedule)
    {
        // $this->authorize('enterMarks', $examSchedule); // Example Policy check

        $validator = Validator::make($request->all(), [
            'results' => 'required|array|min:1',
            'results.*.student_academic_year_id' => [
                'required'
             
            ],
            'results.*.marks_obtained' => ['nullable', 'numeric', 'min:0',
                // Marks obtained cannot exceed max_marks from the schedule
                function ($attribute, $value, $fail) use ($examSchedule, $request) {
                    $index = explode('.', $attribute)[1]; // Get the index from 'results.X.marks_obtained'
                    $isAbsent = $request->input("results.{$index}.is_absent", false);
                    if ($isAbsent == true || $isAbsent == 'true') { // If absent, marks are allowed to be null/empty
                        return;
                    }
                    if ($value === null || $value === '') { // If not absent, marks are required
                         $fail('الدرجة مطلوبة إذا لم يكن الطالب غائباً.');
                         return;
                    }
                    if ((float)$value > (float)$examSchedule->max_marks) {
                        $fail('الدرجة المحصلة لا يمكن أن تتجاوز العلامة العظمى للمادة (' . $examSchedule->max_marks . ').');
                    }
                }
            ],
            'results.*.grade_letter' => 'nullable|string|max:10',
            'results.*.is_absent' => 'required|boolean',
            'results.*.remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من صحة البيانات', 'errors' => $validator->errors()], 422);
        }

        $resultsData = $request->input('results');
        $upsertedResults = [];
        $userId = Auth::id();

        DB::transaction(function () use ($resultsData, $examSchedule, $userId, &$upsertedResults) {
            foreach ($resultsData as $resultInput) {
                $isAbsent = filter_var($resultInput['is_absent'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $marks = ($isAbsent || !isset($resultInput['marks_obtained']) || $resultInput['marks_obtained'] === '') ? null : (float)$resultInput['marks_obtained'];

                $result = ExamResult::updateOrCreate(
                    [
                        'student_academic_year_id' => $resultInput['student_academic_year_id'],
                        'exam_schedule_id' => $examSchedule->id,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'grade_letter' => $resultInput['grade_letter'] ?? null,
                        'is_absent' => $isAbsent,
                        'remarks' => $resultInput['remarks'] ?? null,
                        // Check if record is new to set entered_by, otherwise it's an update
                        // This part needs to be handled carefully or assume first write is 'entered_by'
                        // For simplicity, we can just set updated_by. A more complex logic might be needed for entered_by.
                        'updated_by_user_id' => $userId,
                        // If you want to set entered_by_user_id only on creation:
                        // 'entered_by_user_id' => $result->wasRecentlyCreated ? $userId : $result->entered_by_user_id, (This won't work directly in updateOrCreate's value array)
                    ]
                );

                // If you need to conditionally set 'entered_by_user_id' only on creation:
                if ($result->wasRecentlyCreated && !$result->entered_by_user_id) {
                    $result->entered_by_user_id = $userId;
                    $result->saveQuietly(); // Save without triggering events if any
                }

                $upsertedResults[] = $result->load(['studentAcademicYear.student:id,student_name,image']);
            }
        });

        return ExamResultResource::collection(collect($upsertedResults));
    }

    // Basic update for a single result - useful for quick edits if needed
    public function update(Request $request, ExamResult $examResult)
    {
        // $this->authorize('update', $examResult);
        $examSchedule = $examResult->examSchedule; // Get the context

        $validator = Validator::make($request->all(), [
            'marks_obtained' => ['nullable', 'numeric', 'min:0', 'max:' . $examSchedule->max_marks],
            'grade_letter' => 'nullable|string|max:10',
            'is_absent' => 'sometimes|required|boolean',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) { return response()->json(['message'=>'Validation Error', 'errors'=>$validator->errors()], 422); }

        $dataToUpdate = $validator->validated();
        if (isset($dataToUpdate['is_absent']) && $dataToUpdate['is_absent'] == true) {
            $dataToUpdate['marks_obtained'] = null;
        }
        $dataToUpdate['updated_by_user_id'] = Auth::id();

        $examResult->update($dataToUpdate);
        return new ExamResultResource($examResult->fresh()->load('studentAcademicYear.student:id,student_name,image'));
    }

    // Basic destroy for a single result
    public function destroy(ExamResult $examResult)
    {
        // $this->authorize('delete', $examResult);
        $examResult->delete();
        return response()->json(['message' => 'تم حذف نتيجة الطالب بنجاح.'], 200);
    }
}