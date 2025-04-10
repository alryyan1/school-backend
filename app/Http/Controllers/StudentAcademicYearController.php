<?php

namespace App\Http\Controllers;

use App\Models\StudentAcademicYear;
use App\Models\Student; // Import
use App\Models\AcademicYear; // Import
use Illuminate\Http\Request;
use App\Http\Resources\StudentAcademicYearResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentAcademicYearController extends Controller
{
    /**
     * Display a listing of the resource based on filters.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'grade_level_id' => 'sometimes|required|integer|exists:grade_levels,id', // Optional filter
            'classroom_id' => 'sometimes|required|integer|exists:classrooms,id', // Optional filter
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'الرجاء تحديد العام الدراسي على الأقل', 'errors' => $validator->errors()], 422);
        }

        $query = StudentAcademicYear::with([
            'student', // Eager load student details
            'gradeLevel',
            'classroom'
            // No need to load academicYear as we filter by it
        ])->where('academic_year_id', $request->input('academic_year_id'));

        if ($request->filled('grade_level_id')) {
            $query->where('grade_level_id', $request->input('grade_level_id'));
        }
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->input('classroom_id'));
        }

        // Order by student name?
        $query->join('students', 'student_academic_years.student_id', '=', 'students.id')
              ->orderBy('students.student_name');


        $enrollments = $query->select('student_academic_years.*')->get(); // Select only columns from enrollments after join

        return StudentAcademicYearResource::collection($enrollments);
    }


     /**
      * Get students available for enrollment in a specific academic year.
      * (Students not already in student_academic_years for that year)
      */
     public function getEnrollableStudents(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'academic_year_id' => 'required|integer|exists:academic_years,id',
         ]);

         if ($validator->fails()) {
             return response()->json(['message' => 'الرجاء تحديد العام الدراسي', 'errors' => $validator->errors()], 422);
         }

         $academicYearId = $request->input('academic_year_id');

         // Get IDs of students already enrolled in this year
         $enrolledStudentIds = StudentAcademicYear::where('academic_year_id', $academicYearId)
             ->pluck('student_id');

         // Get students who are NOT in that list
         $enrollableStudents = Student::whereNotIn('id', $enrolledStudentIds)
             ->orderBy('student_name')
             ->select('id', 'student_name', 'goverment_id') // Select needed fields
             ->get();

         return response()->json(['data' => $enrollableStudents]);
     }


    /**
     * Store a newly created resource (enroll a student).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'academic_year_id' => [
                'required','integer','exists:academic_years,id',
                // Ensure unique combination
                Rule::unique('student_academic_years')->where(function ($query) use ($request) {
                    return $query->where('student_id', $request->student_id);
                    // Unique constraint already handles academic_year_id implicitly here
                })
            ],
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
            'classroom_id' => 'nullable|integer|exists:classrooms,id',
            'status' => ['required', Rule::in(['active', 'transferred', 'graduated', 'withdrawn'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $enrollment = StudentAcademicYear::create($validator->validated());

        return new StudentAcademicYearResource($enrollment->load(['student', 'gradeLevel', 'classroom']));
    }


    /**
     * Update the specified resource (mainly status or classroom).
     */
    public function update(Request $request, StudentAcademicYear $studentAcademicYear) // Route model binding
    {
         $validator = Validator::make($request->all(), [
             // Only allow updating classroom and status
             'classroom_id' => 'nullable|integer|exists:classrooms,id',
             'status' => ['sometimes', 'required', Rule::in(['active', 'transferred', 'graduated', 'withdrawn'])],
             // Add validation: ensure classroom belongs to the correct grade level?
             'classroom_id' => [
                 'nullable', 'integer',
                 Rule::exists('classrooms', 'id')->where(function ($query) use ($studentAcademicYear) {
                     // Ensure classroom belongs to the enrollment's grade level
                     $query->where('grade_level_id', $studentAcademicYear->grade_level_id);
                 }),
             ],
         ]);

         if ($validator->fails()) {
             return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
         }

         $studentAcademicYear->update($validator->validated());

         return new StudentAcademicYearResource($studentAcademicYear->fresh()->load(['student', 'gradeLevel', 'classroom']));
    }

    /**
     * Remove the specified resource (unenroll).
     */
    public function destroy(StudentAcademicYear $studentAcademicYear)
    {
        // Add checks if needed (e.g., prevent deletion if grades exist for this enrollment)
        $studentAcademicYear->delete();
        return response()->json(['message' => 'تم حذف تسجيل الطالب بنجاح'], 200);
    }

     // show() method might not be needed for the main UI
     // public function show(StudentAcademicYear $studentAcademicYear) { ... }
}