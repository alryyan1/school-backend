<?php

namespace App\Http\Controllers;

use App\Models\AcademicYearSubject;
use App\Models\AcademicYear; // Import other models
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\AcademicYearSubjectResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AcademicYearSubjectController extends Controller
{
    /**
     * Display a listing of the resource, filtered by year and grade level.
     * This is the primary method for the UI we're building.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'الرجاء تحديد العام الدراسي والمرحلة', 'errors' => $validator->errors()], 422);
        }

        $academicYearId = $request->input('academic_year_id');
        $gradeLevelId = $request->input('grade_level_id');

        // Get assigned subjects/teachers for this year/grade
        $assignedSubjects = AcademicYearSubject::with(['subject', 'teacher']) // Eager load
            ->where('academic_year_id', $academicYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->get();

        // Optional: Get subjects NOT yet assigned to this grade/year for adding
        // $assignedSubjectIds = $assignedSubjects->pluck('subject_id');
        // $availableSubjects = Subject::whereNotIn('id', $assignedSubjectIds)->orderBy('name')->get();

        return AcademicYearSubjectResource::collection($assignedSubjects);
        // Or return a combined response:
        // return response()->json([
        //     'assigned' => AcademicYearSubjectResource::collection($assignedSubjects),
        //     'available' => SubjectResource::collection($availableSubjects) // Use SubjectResource
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     * Assigns a subject (and optionally teacher) to a year/grade.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
            'subject_id' => [
                'required', 'integer', 'exists:subjects,id',
                // Ensure combination is unique for the academic year and grade level
                Rule::unique('academic_year_subjects')->where(function ($query) use ($request) {
                    return $query->where('academic_year_id', $request->academic_year_id)
                                 ->where('grade_level_id', $request->grade_level_id);
                })
            ],
            'teacher_id' => 'nullable|integer|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $academicYearSubject = AcademicYearSubject::create($validator->validated());

        return new AcademicYearSubjectResource($academicYearSubject->load(['subject', 'teacher']));
    }

     /**
     * Update the specified resource in storage.
     * Primarily used to change the assigned teacher for a subject in a specific year/grade.
     */
    public function update(Request $request, AcademicYearSubject $academicYearSubject) // Route model binding
    {
        // Note: Changing subject_id, grade_level_id, or academic_year_id via update
        // is usually discouraged; better to delete and create new if structure changes.
        // This update focuses on the teacher_id.
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            // Add other updatable fields if necessary
        ]);

         if ($validator->fails()) {
             return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
         }

         // Only update the teacher_id (or other specified fields)
         $academicYearSubject->update($validator->validated());

         return new AcademicYearSubjectResource($academicYearSubject->fresh()->load(['subject', 'teacher']));
    }

    /**
     * Remove the specified resource from storage.
     * Unassigns a subject from a year/grade.
     */
    public function destroy(AcademicYearSubject $academicYearSubject)
    {
        // Add checks if needed before deletion (e.g., if student results exist)
        $academicYearSubject->delete();
        return response()->json(['message' => 'تم إلغاء تعيين المادة بنجاح'], 200);
    }

    // Note: show() method might not be strictly needed for this UI approach.
    // public function show(AcademicYearSubject $academicYearSubject) { ... }
}