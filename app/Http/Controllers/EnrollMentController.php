<?php

namespace App\Http\Controllers;

use App\Models\EnrollMent;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Resources\EnrollmentResource;
use App\Models\StudentTransportAssignment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EnrollMentController extends Controller
{
    /**
     * Display a listing of the resource based on filters.
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'school_id' => 'required|exists:schools,id',
                'academic_year' => 'required|string',
                'grade_level_id' => 'sometimes|required|exists:grade_levels,id', // Optional filter
                'classroom_id' => 'sometimes|required|exists:classrooms,id', // Optional filter
            ]);
    
            if ($validator->fails()) {
                return response()->json(['message' => 'الرجاء تحديد العام الدراسي على الأقل', 'errors' => $validator->errors()], 422);
            }
    
            // Eager load necessary relations
            $query = EnrollMent::with([
                'student', // Select only needed student columns
                'gradeLevel', // Select only needed grade columns
                'classroom', // Select only needed classroom columns
                'school' // Load School
            ])
                ->where('school_id', $request->input('school_id')) // Filter by school
                ->where('academic_year', $request->input('academic_year')); // Filter by year
    
            if ($request->filled('grade_level_id')) {
                $query->where('grade_level_id', $request->input('grade_level_id'));
            }
            if ($request->filled('classroom_id')) {
                $query->where('classroom_id', $request->input('classroom_id'));
            }
    
            // Order by student name?
            $query->join('students', 'enrollments.student_id', '=', 'students.id')
                ->orderBy('students.student_name');
    
            $enrollments = $query->select('enrollments.*')->get(); // Select only columns from enrollments after join
    
            return EnrollmentResource::collection($enrollments);
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()],status:422);
        }
    }

    public function getAllEnrollments(){
        return EnrollMent::all();
    }

    /**
     * Get students available for enrollment in a specific academic year.
     * (Students not already in enrollments for that year)
     */
    public function getEnrollableStudents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|string',
            'school_id' => 'required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'الرجاء تحديد المدرسة والعام الدراسي', 'errors' => $validator->errors()], 422);
        }

        $academicYear = $request->input('academic_year');
        $schoolId = $request->input('school_id');

        // Get IDs of students already enrolled in this year
        $enrolledStudentIds = EnrollMent::where('academic_year', $academicYear)
            ->pluck('student_id');

        // Get students who are NOT in that list.
        $enrollableStudents = Student::whereNotIn('id', $enrolledStudentIds)
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
        // If a discount is being applied, ensure the user has the proper permission
        if ($request->filled('discount') && intval($request->input('discount')) > 0) {
            abort_unless(auth()->user() && auth()->user()->can('apply fee discount'), 403, 'ليس لديك صلاحية لتطبيق الخصم');
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'school_id' => 'required|integer|exists:schools,id',
            'academic_year' => [
                'required',
                'string',
                // Unique constraint check (student_id, academic_year) handles this
                Rule::unique('enrollments')->where(function ($query) use ($request) {
                    return $query->where('student_id', $request->student_id);
                })
            ],
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
            'fees'=>'nullable|integer',
            'discount'=>'nullable|integer|in:0,5,10,15,20,25,30,40,50',
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
            'enrollment_type' => ['sometimes', Rule::in(['regular','scholarship'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $enrollment = EnrollMent::create($validator->validated());

        return new EnrollmentResource($enrollment->load(['student', 'gradeLevel', 'classroom', 'school']));
    }

    /**
     * Update the specified resource (mainly status or classroom).
     */
    public function update(Request $request, EnrollMent $enrollment)
    {
        // Discount permission check when attempting to change discount
        if ($request->has('discount')) {
            $discount = $request->input('discount');
            if (!is_null($discount) && intval($discount) > 0) {
                abort_unless(auth()->user() && auth()->user()->can('apply fee discount'), 403, 'ليس لديك صلاحية لتطبيق الخصم');
            }
        }
        // Authorization: change of enrollment_type requires explicit permission
        if ($request->has('enrollment_type')) {
            abort_unless(auth()->user() && auth()->user()->can('set student enrollment type'), 403, 'ليس لديك صلاحية لتحديد نوع تسجيل الطالب');
        }

        $validator = Validator::make($request->all(), [
            'classroom_id' => [
                'nullable',
                'integer',
                Rule::exists('classrooms', 'id')->where(function ($query) use ($enrollment) {
                    // Ensure new classroom belongs to the correct school and grade
                    $query->where('school_id', $enrollment->school_id)
                        ->where('grade_level_id', $enrollment->grade_level_id);
                }),
            ],
            'discount' => ['sometimes','nullable','integer','in:0,5,10,15,20,25,30,40,50'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'transferred', 'graduated', 'withdrawn'])],
            'enrollment_type' => ['sometimes','required', Rule::in(['regular','scholarship'])],
            'fees' => ['sometimes', 'nullable', 'integer'],
            // Do not allow changing student, year, grade, school via update
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $enrollment->update($validator->validated());

        return new EnrollmentResource($enrollment->fresh()->load(['student', 'gradeLevel', 'classroom', 'school']));
    }

    /**
     * Remove the specified resource (unenroll).
     */
    public function destroy(EnrollMent $enrollment)
    {
        // Add checks if needed (e.g., prevent deletion if grades exist for this enrollment)
        $enrollment->delete();
        return response()->json(['message' => 'تم حذف تسجيل الطالب بنجاح'], 200);
    }

    public function getAssignableStudentsForTransport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|string',
            'school_id' => 'required|integer|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'الرجاء تحديد المدرسة والعام الدراسي', 'errors' => $validator->errors()], 422);
        }

        $academicYear = $request->input('academic_year');
        $schoolId = $request->input('school_id');

        // Get IDs of Enrollment records already assigned to ANY route for this year
        $assignedEnrollmentIds = StudentTransportAssignment::whereHas('studentAcademicYear', function ($query) use ($academicYear) {
            $query->where('academic_year', $academicYear);
        })->pluck('student_academic_year_id');

        // Get Enrollment records for the selected school and year that are NOT in the assigned list
        $assignableEnrollments = EnrollMent::with('student:id,student_name,goverment_id')
            ->where('academic_year', $academicYear)
            ->where('school_id', $schoolId)
            ->whereNotIn('id', $assignedEnrollmentIds)
            ->where('status', 'active')
            ->get();

        // Transform the data for the frontend dropdown
        $assignableStudents = $assignableEnrollments->map(function ($enrollment) {
            return [
                'student_academic_year_id' => $enrollment->id,
                'student_id' => $enrollment->student->id,
                'student_name' => $enrollment->student->student_name,
                'goverment_id' => $enrollment->student->goverment_id,
            ];
        })->sortBy('student_name')->values();

        return response()->json(['data' => $assignableStudents]);
    }

    /**
     * Search for student enrollments by student ID or name across all years/schools.
     * GET /api/enrollments/search
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'term' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Search term is required', 'errors' => $validator->errors()], 422);
        }

        $searchTerm = $request->input('term');

        $query = EnrollMent::with([
            'student',
            'gradeLevel',
            'classroom',
            'school'
        ])
        ->join('students', 'enrollments.student_id', '=', 'students.id');

        // Check if term is numeric for ID search, otherwise search name
        if (ctype_digit($searchTerm)) {
             $query->where('students.id', '=', $searchTerm);
        } else {
            $query->where('students.student_name', 'LIKE', "%{$searchTerm}%");
        }

        // Order results by academic year descending, then student name
        $query->orderBy('enrollments.academic_year', 'desc')
              ->orderBy('students.student_name');

        $enrollments = $query->select('enrollments.*')->get();

        return EnrollmentResource::collection($enrollments);
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
            'academic_year' => 'required|string',
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'School, Year, and Grade Level are required', 'errors' => $validator->errors()], 422);
        }

        $unassignedEnrollments = EnrollMent::with([
                'student:id,student_name,goverment_id,image',
                'gradeLevel:id,name',
            ])
            ->where('school_id', $request->input('school_id'))
            ->where('academic_year', $request->input('academic_year'))
            ->where('grade_level_id', $request->input('grade_level_id'))
            ->whereNull('classroom_id')
            ->where('status', 'active')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->orderBy('students.student_name')
            ->select('enrollments.*')
            ->get();

        return EnrollmentResource::collection($unassignedEnrollments);
    }

    /**
     * Assign a student enrollment to a classroom (or unassign by passing null).
     * PUT /api/enrollments/{enrollment}/assign-classroom
     */
    public function assignToClassroom(Request $request, EnrollMent $enrollment)
    {
        // Authorization: require explicit permission to assign classroom
        abort_unless(auth()->user() && auth()->user()->can('assign student to classroom'), 403, 'ليس لديك صلاحية لتعيين الطلاب للفصول');

        $validator = Validator::make($request->all(), [
            'classroom_id' => [
                'nullable',
                'integer',
                Rule::exists('classrooms', 'id')->where(function ($query) use ($enrollment) {
                    $query->where('school_id', $enrollment->school_id)
                          ->where('grade_level_id', $enrollment->grade_level_id);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Check classroom capacity
        $classroomId = $request->input('classroom_id');
        if ($classroomId) {
            $classroom = \App\Models\Classroom::find($classroomId);
            if ($classroom) {
                $currentOccupancy = EnrollMent::where('classroom_id', $classroomId)
                                    ->where('academic_year', $enrollment->academic_year)
                                    ->where('status', 'active')
                                    ->count();
                if ($currentOccupancy >= $classroom->capacity) {
                    return response()->json(['message' => 'الفصل الدراسي ممتلئ، لا يمكن إضافة المزيد من الطلاب.'], 422);
                }
            }
        }

        $enrollment->classroom_id = $classroomId;
        $enrollment->save();

        return new EnrollmentResource($enrollment->fresh()->load(['student', 'classroom', 'gradeLevel']));
    }
}
