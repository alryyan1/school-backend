<?php

namespace App\Http\Controllers;

use App\Models\StudentAcademicYear;
use App\Models\Student; // Import
use App\Models\AcademicYear; // Import
use Illuminate\Http\Request;
use App\Http\Resources\StudentAcademicYearResource;
use App\Models\StudentTransportAssignment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentAcademicYearController extends Controller
{
    /**
     * Display a listing of the resource based on filters.
     */
    public function index(Request $request)
    {
        try {
            //code...
            $validator = Validator::make($request->all(), [
                'school_id' => 'required|exists:schools,id',
                'academic_year_id' => 'required|exists:academic_years,id',
                'grade_level_id' => 'sometimes|required|exists:grade_levels,id', // Optional filter
                'classroom_id' => 'sometimes|required|exists:classrooms,id', // Optional filter
            ]);
    
            if ($validator->fails()) {
                return response()->json(['message' => 'الرجاء تحديد العام الدراسي على الأقل', 'errors' => $validator->errors()], 422);
            }
    
            // Eager load necessary relations
            $query = StudentAcademicYear::with([
                'student', // Select only needed student columns
                'gradeLevel', // Select only needed grade columns
                'classroom', // Select only needed classroom columns
                'school' // Load School
            ])
                ->where('school_id', $request->input('school_id')) // Filter by school
                ->where('academic_year_id', $request->input('academic_year_id')); // Filter by year
    
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
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['message'=>$e->getMessage()],status:422);
        }

    }


    /**
     * Get students available for enrollment in a specific academic year.
     * (Students not already in student_academic_years for that year)
     */
    public function getEnrollableStudents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'school_id' => 'required|exists:schools,id', // Add school ID validation

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'الرجاء تحديد المدرسة والعام الدراسي', 'errors' => $validator->errors()], 422);
        }

        $academicYearId = $request->input('academic_year_id');
        $schoolId = $request->input('school_id');

        // Get IDs of students already enrolled in this year
        // Get IDs of students already enrolled in this year (in any school or just this one? Assume just this one)
        $enrolledStudentIds = StudentAcademicYear::where('academic_year_id', $academicYearId)
            // ->where('school_id', $schoolId) // Optional: Only exclude if enrolled in THIS school? Or any school? Let's assume any school for simplicity now.
            ->pluck('student_id');

        // Get students who are NOT in that list.
        // Optional: Filter students further, e.g., only students associated with the selected school if students have a school_id FK.
        $enrollableStudents = Student::whereNotIn('id', $enrolledStudentIds)
            // ->where('school_id', $schoolId) // Uncomment if students belong to schools
            ->orderBy('student_name')
            ->select('id', 'student_name', 'goverment_id')
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
            'school_id' => 'required|integer|exists:schools,id', // <-- Validate school_id
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
                // Unique constraint check (student_id, academic_year_id) handles this
                // Need to ensure the academic_year belongs to the school? Add custom rule maybe.
                Rule::unique('student_academic_years')->where(function ($query) use ($request) {
                    return $query->where('student_id', $request->student_id);
                })
            ],
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
            'classroom_id' => [
                'nullable',
                'integer',
                // Ensure classroom exists AND belongs to the selected school/grade
                Rule::exists('classrooms', 'id')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id)
                        ->where('grade_level_id', $request->grade_level_id);
                })
            ],
            'status' => ['required', Rule::in(['active', 'transferred', 'graduated', 'withdrawn'])],
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $enrollment = StudentAcademicYear::create($validator->validated());

        return new StudentAcademicYearResource($enrollment->load(['student', 'gradeLevel', 'classroom', 'school']));
    }


    /**
     * Update the specified resource (mainly status or classroom).
     */
    public function update(Request $request, StudentAcademicYear $studentAcademicYear) // Route model binding
    {
        $validator = Validator::make($request->all(), [
            'classroom_id' => [
                'nullable',
                'integer',
                Rule::exists('classrooms', 'id')->where(function ($query) use ($studentAcademicYear) {
                    // Ensure new classroom belongs to the correct school and grade
                    $query->where('school_id', $studentAcademicYear->school_id)
                        ->where('grade_level_id', $studentAcademicYear->grade_level_id);
                }),
            ],
            'status' => ['sometimes', 'required', Rule::in(['active', 'transferred', 'graduated', 'withdrawn'])],
            // Do not allow changing student, year, grade, school via update
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $studentAcademicYear->update($validator->validated());

        return new StudentAcademicYearResource($studentAcademicYear->fresh()->load(['student', 'academicYear', 'gradeLevel', 'classroom', 'school']));
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
    public function getAssignableStudentsForTransport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'school_id' => 'required|integer|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'الرجاء تحديد المدرسة والعام الدراسي', 'errors' => $validator->errors()], 422);
        }

        $academicYearId = $request->input('academic_year_id');
        $schoolId = $request->input('school_id');

        // Get IDs of StudentAcademicYear records already assigned to ANY route for this year
        $assignedEnrollmentIds = StudentTransportAssignment::whereHas('studentAcademicYear', function ($query) use ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
            // We don't need to filter by school here, as the enrollment itself belongs to the school via the academic year link implicitly,
            // or more explicitly if we queried through StudentAcademicYear first.
        })->pluck('student_academic_year_id');

        // Get StudentAcademicYear records for the selected school and year that are NOT in the assigned list
        $assignableEnrollments = StudentAcademicYear::with('student:id,student_name,goverment_id') // Select necessary student fields
            ->where('academic_year_id', $academicYearId)
            ->where('school_id', $schoolId) // Ensure enrollment is for the correct school
            ->whereNotIn('id', $assignedEnrollmentIds)
            ->where('status', 'active') // Only assign active students
            ->get();

        // Transform the data for the frontend dropdown (AssignableStudentInfo format)
        $assignableStudents = $assignableEnrollments->map(function ($enrollment) {
            return [
                'student_academic_year_id' => $enrollment->id, // The ID needed for assignment
                'student_id' => $enrollment->student->id,
                'student_name' => $enrollment->student->student_name,
                'goverment_id' => $enrollment->student->goverment_id, // Include for potential display
                // Include Grade/Class if useful for selection context
                // 'grade_level_name' => $enrollment->gradeLevel->name ?? null,
                // 'classroom_name' => $enrollment->classroom->name ?? null,
            ];
        })->sortBy('student_name')->values(); // Sort by name and re-index

        return response()->json(['data' => $assignableStudents]);
    }
}
