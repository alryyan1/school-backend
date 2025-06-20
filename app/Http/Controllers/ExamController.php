<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\School; // Import
use Illuminate\Http\Request;
use App\Http\Resources\ExamResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource, optionally filtered by school.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Exam::class);

        $query = Exam::with('school')->latest(); // Eager load school

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->input('school_id'));
        }

        $exams = $query->get(); // Get all filtered results

        return ExamResource::collection($exams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Exam::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'school_id' => ['required', 'integer', Rule::exists('schools', 'id')],
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $exam = Exam::create($validator->validated());

        return new ExamResource($exam->load('school')); // Load school relation
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        // $this->authorize('view', $exam);
        return new ExamResource($exam->load('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        // $this->authorize('update', $exam);

        $validator = Validator::make($request->all(), [
             'name' => 'sometimes|required|string|max:255',
             // 'school_id' => ['sometimes', 'required', 'integer', Rule::exists('schools', 'id')], // Usually not changed
             'start_date' => 'sometimes|required|date_format:Y-m-d',
             'end_date' => [
                'sometimes', 'required', 'date_format:Y-m-d',
                // Validate end_date against start_date (either from request or existing model)
                 'after_or_equal:' . ($request->input('start_date', $exam->start_date->format('Y-m-d')))
             ],
             'description' => 'nullable|string',
        ]);

         if ($validator->fails()) {
             return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
         }

         $exam->update($validator->validated());

         return new ExamResource($exam->fresh()->load('school'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        // $this->authorize('delete', $exam);

        // ** CHECK FOR RELATIONSHIPS **
        // Example: If you have an 'exam_schedules' table
        // if ($exam->schedules()->exists()) { // Assuming schedules() relationship exists
        //     return response()->json(['message' => 'لا يمكن حذف دورة الامتحان لوجود جداول مرتبطة بها.'], 409);
        // }
        // Add checks for results etc.

        $exam->delete();

        return response()->json(['message' => 'تم حذف دورة الامتحان بنجاح.'], 200);
    }
     /**
     * Get exams relevant to a specific student, typically for their
     * current school and active academic year.
     * GET /api/students/{student}/relevant-exams
     */
    public function getRelevantExamsForStudent(Request $request, Student $student)
    {
        // $this->authorize('viewExamsForStudent', $student);

        // Find the student's active enrollment for the system's active year (or a passed year_id)
        $activeAcademicYear = AcademicYear::where('is_current', true)
                                          // Optionally filter by school if student is linked to ONE school
                                          // ->where('school_id', $student->school_id) // If student has a school_id
                                          ->first();

        if (!$activeAcademicYear) {
            // Or, if student can have multiple enrollments, find their latest active one.
            // This logic depends on how you determine a student's "current" context.
            // For simplicity, using system's active year for now.
            return response()->json(['data' => []]); // No active year, no relevant exams
        }

        // Find the student's enrollment for this active year
        $enrollment = $student->enrollments() // Assumes Student model has enrollments() relationship
                              ->where('academic_year_id', $activeAcademicYear->id)
                              ->where('status', 'active')
                              ->first();

        if (!$enrollment) {
            return response()->json(['data' => []]); // Not actively enrolled in the current year
        }

        // Get exams for the school of this enrollment
        $exams = Exam::with('school')
                     ->where('school_id', $enrollment->school_id) // Use school_id from enrollment
                     ->where('academic_year_id', $activeAcademicYear->id) // Filter by academic year of the exam
                     ->orderBy('start_date', 'desc')
                     ->get();

        return ExamResource::collection($exams);
    }
}