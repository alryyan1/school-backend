<?php

namespace App\Http\Controllers;

use App\Models\StudentAcademicYear;
use App\Models\Student; // Import
use App\Models\AcademicYear; // Import
use Illuminate\Http\Request;
use App\Http\Resources\StudentAcademicYearResource;
use App\Models\StudentTransportAssignment;
use App\Rules\MaxAmountBasedOnGrade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Whoops\Run;

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
    public function getAllStudentAcademicYear(){
        return StudentAcademicYear::all();
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
            'fees'=>'nullable',
            'discount'=>'nullable|integer|in:0,10,20,30,40',
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
    public function update(Request $request, StudentAcademicYear $student_enrollment) // Route model binding
    {
        $validator = Validator::make($request->all(), [
            'classroom_id' => [
                'nullable',
                'integer',
                Rule::exists('classrooms', 'id')->where(function ($query) use ($student_enrollment) {
                    // Ensure new classroom belongs to the correct school and grade
                    $query->where('school_id', $student_enrollment->school_id)
                        ->where('grade_level_id', $student_enrollment->grade_level_id);
                }),
            ],
            'status' => ['sometimes', 'required', Rule::in(['active', 'transferred', 'graduated', 'withdrawn'])],
            // Do not allow changing student, year, grade, school via update
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $student_enrollment->update($validator->validated());

        return new StudentAcademicYearResource($student_enrollment->fresh()->load(['student', 'academicYear', 'gradeLevel', 'classroom', 'school']));
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
     /**
     * Search for student enrollments by student ID or name across all years/schools.
     * GET /api/student-enrollments/search
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term' => 'required|string|min:1', // Search term is required
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Search term is required', 'errors' => $validator->errors()], 422);
        }

        $searchTerm = $request->input('term');

        $query = StudentAcademicYear::with([
            'student', // Eager load necessary relations
            'academicYear',
            'gradeLevel',
            'classroom',
            'school' // Include school
        ])
        ->join('students', 'student_academic_years.student_id', '=', 'students.id'); // Join to search student fields

        // Check if term is numeric for ID search, otherwise search name
        if (ctype_digit($searchTerm)) {
             $query->where('students.id', '=', $searchTerm);
        } else {
            $query->where('students.student_name', 'LIKE', "%{$searchTerm}%");
        }

        // Order results (e.g., by year descending, then student name)
        $query->join('academic_years', 'student_academic_years.academic_year_id', '=', 'academic_years.id')
              ->orderBy('academic_years.start_date', 'desc')
              ->orderBy('students.student_name');

        // Get results (maybe paginate if search can return many results)
        $enrollments = $query->select('student_academic_years.*')->get();
        // $enrollments = $query->select('student_academic_years.*')->paginate(25); // Example pagination


        return StudentAcademicYearResource::collection($enrollments);
    }


  /**
     * Get students enrolled in a specific grade level for a year/school,
     * filtering by those NOT assigned to a classroom.
     * GET /api/unassigned-students-for-grade
     */
    public function getUnassignedStudentsForGrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|integer|exists:schools,id',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'School, Year, and Grade Level are required', 'errors' => $validator->errors()], 422);
        }

        $unassignedEnrollments = StudentAcademicYear::with([
                'student:id,student_name,goverment_id,image', // Include student image
                'gradeLevel:id,name', // For context, though filtered by it
            ])
            ->where('school_id', $request->input('school_id'))
            ->where('academic_year_id', $request->input('academic_year_id'))
            ->where('grade_level_id', $request->input('grade_level_id'))
            ->whereNull('classroom_id') // <-- Key filter
            ->where('status', 'active')   // Only active students
            ->join('students', 'student_academic_years.student_id', '=', 'students.id')
            ->orderBy('students.student_name')
            ->select('student_academic_years.*') // Select all from the pivot
            ->get();

        return StudentAcademicYearResource::collection($unassignedEnrollments);
    }

    /**
     * Assign a student enrollment to a classroom (or unassign by passing null).
     * PUT /api/student-enrollments/{studentAcademicYear}/assign-classroom
     */
    public function assignToClassroom(Request $request, StudentAcademicYear $studentAcademicYear)
    {
        // Authorization check: Can current user manage this enrollment/classroom assignment?
        // $this->authorize('update', $studentAcademicYear);

        $validator = Validator::make($request->all(), [
            'classroom_id' => [
                'nullable', // Allow unassigning
                'integer',
                Rule::exists('classrooms', 'id')->where(function ($query) use ($studentAcademicYear) {
                    // Ensure classroom belongs to the same school and grade level as the enrollment
                    $query->where('school_id', $studentAcademicYear->school_id)
                          ->where('grade_level_id', $studentAcademicYear->grade_level_id);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Check classroom capacity (Simplified - real check might be more complex)
        $classroomId = $request->input('classroom_id');
        if ($classroomId) {
            $classroom = \App\Models\Classroom::find($classroomId);
            if ($classroom) {
                $currentOccupancy = StudentAcademicYear::where('classroom_id', $classroomId)
                                    ->where('academic_year_id', $studentAcademicYear->academic_year_id) // For current year
                                    ->where('status', 'active')
                                    ->count();
                if ($currentOccupancy >= $classroom->capacity) {
                    return response()->json(['message' => 'الفصل الدراسي ممتلئ، لا يمكن إضافة المزيد من الطلاب.'], 422);
                }
            }
        }

        $studentAcademicYear->classroom_id = $classroomId; // Assign or unassign (if null)
        $studentAcademicYear->save();

        return new StudentAcademicYearResource($studentAcademicYear->fresh()->load(['student', 'classroom', 'gradeLevel']));
    }

      /**
     * Display the specified resource.
     */
    // public function show(StudentAcademicYear $studentAcademicYear) // Uses route model binding
    // {
    //     // Optional: Authorization check
    //     // $this->authorize('view', $studentAcademicYear);

    //     // Load necessary relationships and return the resource
    //     return new StudentAcademicYearResource(
    //         $studentAcademicYear->load(['student', 'academicYear', 'gradeLevel', 'classroom', 'school'])
    //     );
    // }
}
