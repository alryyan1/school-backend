<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\StudentLedger;
use App\Models\EnrollmentLog;
use Illuminate\Http\Request;
use App\Http\Resources\EnrollmentResource;
use App\Models\StudentTransportAssignment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
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
    
            // Build query with proper table references
            $query = Enrollment::with([
                'student', // Select only needed student columns
                'gradeLevel', // Select only needed grade columns
                'classroom', // Select only needed classroom columns
                'school' // Load School
            ])
                ->where('enrollments.school_id', $request->input('school_id')) // Filter by school
                ->where('enrollments.academic_year', $request->input('academic_year')); // Filter by year
    
            if ($request->filled('grade_level_id')) {
                $query->where('enrollments.grade_level_id', $request->input('grade_level_id'));
            }
            if ($request->filled('classroom_id')) {
                $query->where('enrollments.classroom_id', $request->input('classroom_id'));
            }
    
            // Join with students table for ordering
            $query->join('students', 'enrollments.student_id', '=', 'students.id')
                ->orderBy('students.student_name');
    
            $enrollments = $query->select('enrollments.*')->get(); // Select only columns from enrollments after join
    
            return EnrollmentResource::collection($enrollments);
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()],status:422);
        }
    }

    public function getAllEnrollments(){
        return Enrollment::all();
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
        $enrolledStudentIds = Enrollment::where('academic_year', $academicYear)
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

        $validatedData = $validator->validated();
        
        // Auto-fill fees from school's annual_fees if not provided
        if (!isset($validatedData['fees']) || $validatedData['fees'] === 0) {
            $school = \App\Models\School::find($request->school_id);
            if ($school && $school->annual_fees) {
                $validatedData['fees'] = $school->annual_fees;
            } else {
                $validatedData['fees'] = 0; // Default to 0 if school has no annual fees
            }
        }

        $enrollment = Enrollment::create($validatedData);

        // Automatically create student ledger entry for fees
        if ($enrollment->fees > 0) {
            try {
                \App\Models\StudentLedger::addEntry([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'transaction_type' => 'fee',
                    'description' => 'رسوم التسجيل السنوية - ' . $enrollment->academic_year,
                    'amount' => $enrollment->fees,
                    'transaction_date' => now()->format('Y-m-d'),
                    'reference_number' => 'ENR-' . $enrollment->id,
                    'metadata' => [
                        'enrollment_id' => $enrollment->id,
                        'academic_year' => $enrollment->academic_year,
                        'grade_level_id' => $enrollment->grade_level_id,
                        'school_id' => $enrollment->school_id,
                        'auto_created' => true
                    ],
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the enrollment creation
                \Log::error('Failed to create automatic student ledger entry for enrollment ' . $enrollment->id . ': ' . $e->getMessage());
            }
        }

        // Automatically create student ledger entry for discount if applied
        if ($enrollment->discount > 0) {
            try {
                $discountAmount = ($enrollment->fees * $enrollment->discount) / 100;
                \App\Models\StudentLedger::addEntry([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'transaction_type' => 'discount',
                    'description' => 'خصم على رسوم التسجيل - ' . $enrollment->discount . '% - ' . $enrollment->academic_year,
                    'amount' => $discountAmount,
                    'transaction_date' => now()->format('Y-m-d'),
                    'reference_number' => 'ENR-DISC-' . $enrollment->id,
                    'metadata' => [
                        'enrollment_id' => $enrollment->id,
                        'academic_year' => $enrollment->academic_year,
                        'grade_level_id' => $enrollment->grade_level_id,
                        'school_id' => $enrollment->school_id,
                        'discount_percentage' => $enrollment->discount,
                        'discount_amount' => $discountAmount,
                        'auto_created' => true
                    ],
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the enrollment creation
                \Log::error('Failed to create automatic student ledger entry for discount ' . $enrollment->id . ': ' . $e->getMessage());
            }
        }

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
            'enrollment_type' => ['sometimes','required', Rule::in(['regular','scholarship','free'])],
            'fees' => ['sometimes', 'nullable', 'integer'],
            'grade_level_id' => ['sometimes', 'nullable', 'integer', Rule::exists('grade_levels', 'id')],
            'academic_year' => ['sometimes', 'nullable', 'string'],
            // Do not allow changing student, year, grade, school via update
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        
        // Capture old values for logging before any changes
        $oldGradeLevelId = $enrollment->grade_level_id;
        $oldStatus = $enrollment->status;
        $oldClassroomId = $enrollment->classroom_id;
        $oldAcademicYear = $enrollment->academic_year;
        $oldDiscount = $enrollment->discount;
        
        if(isset($validatedData['grade_level_id'])){
            $enrollment->classroom_id = null;
        }
        // Auto-fill fees from school's annual_fees if fees are being updated and not provided
        if (isset($validatedData['fees']) && ($validatedData['fees'] === null || $validatedData['fees'] === 0)) {
            $school = \App\Models\School::find($enrollment->school_id);
            if ($school && $school->annual_fees) {
                $validatedData['fees'] = $school->annual_fees;
            } else {
                $validatedData['fees'] = 0; // Default to 0 if school has no annual fees
            }
        }
        
        // Check if fees were changed and create appropriate ledger entries
        $oldFees = $enrollment->fees;
        $newFees = $validatedData['fees'] ?? $oldFees;
        
        $enrollment->update($validatedData);
        
        // Log changes after update
        $this->logEnrollmentChanges($enrollment, $validatedData, [
            'old_grade_level_id' => $oldGradeLevelId,
            'old_status' => $oldStatus,
            'old_classroom_id' => $oldClassroomId,
            'old_academic_year' => $oldAcademicYear,
            'old_discount' => $oldDiscount,
            'old_fees' => $oldFees,
        ]);
        
        // If fees changed, create ledger entries
        if ($oldFees !== $newFees && $newFees > 0) {
            try {
                // If fees increased, add a fee entry
                if ($newFees > $oldFees) {
                    $feeIncrease = $newFees - $oldFees;
                    \App\Models\StudentLedger::addEntry([
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'transaction_type' => 'fee',
                        'description' => 'تحديث رسوم التسجيل - ' . $enrollment->academic_year,
                        'amount' => $feeIncrease,
                        'transaction_date' => now()->format('Y-m-d'),
                        'reference_number' => 'ENR-UPDATE-' . $enrollment->id,
                        'metadata' => [
                            'enrollment_id' => $enrollment->id,
                            'academic_year' => $enrollment->academic_year,
                            'grade_level_id' => $enrollment->grade_level_id,
                            'school_id' => $enrollment->school_id,
                            'old_fees' => $oldFees,
                            'new_fees' => $newFees,
                            'fee_change' => $feeIncrease,
                            'auto_created' => true,
                            'change_type' => 'fee_update'
                        ],
                        'created_by' => auth()->id(),
                    ]);
                }
                // If fees decreased, add an adjustment entry (negative amount)
                else if ($newFees < $oldFees) {
                    $feeDecrease = $oldFees - $newFees;
                    \App\Models\StudentLedger::addEntry([
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'transaction_type' => 'adjustment',
                        'description' => 'تخفيض رسوم التسجيل - ' . $enrollment->academic_year,
                        'amount' => -$feeDecrease, // Negative amount for fee reduction
                        'transaction_date' => now()->format('Y-m-d'),
                        'reference_number' => 'ENR-ADJUST-' . $enrollment->id,
                        'metadata' => [
                            'enrollment_id' => $enrollment->id,
                            'academic_year' => $enrollment->academic_year,
                            'grade_level_id' => $enrollment->grade_level_id,
                            'school_id' => $enrollment->school_id,
                            'old_fees' => $oldFees,
                            'new_fees' => $newFees,
                            'fee_change' => -$feeDecrease,
                            'auto_created' => true,
                            'change_type' => 'fee_reduction'
                        ],
                        'created_by' => auth()->id(),
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the enrollment update
                \Log::error('Failed to create automatic student ledger entry for enrollment fee update ' . $enrollment->id . ': ' . $e->getMessage());
            }
        }

        // Check if discount changed and create appropriate ledger entries
        $oldDiscount = $enrollment->discount;
        $newDiscount = $validatedData['discount'] ?? $oldDiscount;
        
        if ($oldDiscount !== $newDiscount) {
            try {
                if ($oldDiscount > 0) {
                    // Remove old discount entry by adding a negative adjustment
                    $oldDiscountAmount = ($enrollment->fees * $oldDiscount) / 100;
                    \App\Models\StudentLedger::addEntry([
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'transaction_type' => 'adjustment',
                        'description' => 'إلغاء الخصم السابق - ' . $oldDiscount . '% - ' . $enrollment->academic_year,
                        'amount' => -$oldDiscountAmount, // Negative to reverse the discount
                        'transaction_date' => now()->format('Y-m-d'),
                        'reference_number' => 'ENR-DISC-CANCEL-' . $enrollment->id,
                        'metadata' => [
                            'enrollment_id' => $enrollment->id,
                            'academic_year' => $enrollment->academic_year,
                            'grade_level_id' => $enrollment->grade_level_id,
                            'school_id' => $enrollment->school_id,
                            'old_discount_percentage' => $oldDiscount,
                            'old_discount_amount' => $oldDiscountAmount,
                            'auto_created' => true,
                            'change_type' => 'discount_cancellation'
                        ],
                        'created_by' => auth()->id(),
                    ]);
                }
                
                if ($newDiscount > 0) {
                    // Add new discount entry
                    $newDiscountAmount = ($enrollment->fees * $newDiscount) / 100;
                    \App\Models\StudentLedger::addEntry([
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'transaction_type' => 'discount',
                        'description' => 'تطبيق خصم جديد - ' . $newDiscount . '% - ' . $enrollment->academic_year,
                        'amount' => $newDiscountAmount,
                        'transaction_date' => now()->format('Y-m-d'),
                        'reference_number' => 'ENR-DISC-NEW-' . $enrollment->id,
                        'metadata' => [
                            'enrollment_id' => $enrollment->id,
                            'academic_year' => $enrollment->academic_year,
                            'grade_level_id' => $enrollment->grade_level_id,
                            'school_id' => $enrollment->school_id,
                            'new_discount_percentage' => $newDiscount,
                            'new_discount_amount' => $newDiscountAmount,
                            'auto_created' => true,
                            'change_type' => 'discount_application'
                        ],
                        'created_by' => auth()->id(),
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the enrollment update
                \Log::error('Failed to create automatic student ledger entry for enrollment discount update ' . $enrollment->id . ': ' . $e->getMessage());
            }
        }

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
        $assignableEnrollments = Enrollment::with('student:id,student_name,goverment_id')
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

        $query = Enrollment::with([
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
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'School, and Grade Level are required', 'errors' => $validator->errors()], 422);
        }

        $unassignedEnrollments = Enrollment::with([
                'student:id,student_name,goverment_id,image',
                'gradeLevel:id,name',
            ])
            ->where('enrollments.school_id', $request->input('school_id'))
            ->where('enrollments.grade_level_id', $request->input('grade_level_id'))
            ->whereNull('enrollments.classroom_id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->orderBy('students.student_name')
            ->select('enrollments.*')
            ->get();

        return EnrollmentResource::collection($unassignedEnrollments);
    }

    /**
     * Get students assigned to a classroom for a specific school/grade (optionally by year).
     * GET /api/assigned-students-for-grade
     */
    public function getAssignedStudentsForGrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|integer|exists:schools,id',
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'School and Grade Level are required', 'errors' => $validator->errors()], 422);
        }

        $query = Enrollment::with([
                'student:id,student_name,goverment_id,image',
                'gradeLevel:id,name',
                'classroom:id,name,capacity'
            ])
            ->where('enrollments.school_id', $request->input('school_id'))
            ->where('enrollments.grade_level_id', $request->input('grade_level_id'))
            ->whereNotNull('enrollments.classroom_id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->orderBy('students.student_name')
            ->select('enrollments.*');


        $assigned = $query->get();

        return EnrollmentResource::collection($assigned);
    }
    /**
     * Assign a student enrollment to a classroom (or unassign by passing null).
     * PUT /api/enrollments/{enrollment}/assign-classroom
     */
    public function assignToClassroom(Request $request, EnrollMent $enrollment)
    {
        // Authorization: require explicit permission to assign classroom
        // abort_unless(auth()->user() && auth()->user()->can('assign student to classroom'), 403, 'ليس لديك صلاحية لتعيين الطلاب للفصول');

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
                $currentOccupancy = Enrollment::where('classroom_id', $classroomId)
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

    /**
     * Get enrollment logs for a specific enrollment.
     */
    public function getLogs(Request $request, Enrollment $enrollment)
    {
        $logs = EnrollmentLog::where('enrollment_id', $enrollment->id)
            ->with(['user:id,name'])
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs,
            'enrollment' => new EnrollmentResource($enrollment->load(['student', 'gradeLevel', 'classroom', 'school']))
        ]);
    }

    /**
     * Get enrollment logs for a specific student.
     */
    public function getStudentLogs(Request $request, Student $student)
    {
        $logs = EnrollmentLog::where('student_id', $student->id)
            ->with(['user:id,name', 'enrollment.gradeLevel', 'enrollment.school'])
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json([
            'data' => $logs,
            'student' => [
                'id' => $student->id,
                'student_name' => $student->student_name,
            ]
        ]);
    }

    /**
     * Log enrollment changes for audit trail.
     */
    private function logEnrollmentChanges(Enrollment $enrollment, array $newData, array $oldData): void
    {
        try {
            // Log grade level changes
            if (isset($newData['grade_level_id']) && $newData['grade_level_id'] != $oldData['old_grade_level_id']) {
                $oldGradeLevel = \App\Models\GradeLevel::find($oldData['old_grade_level_id']);
                $newGradeLevel = \App\Models\GradeLevel::find($newData['grade_level_id']);
                
                EnrollmentLog::logChange(
                    $enrollment->id,
                    $enrollment->student_id,
                    'grade_level_change',
                    'grade_level_id',
                    $oldData['old_grade_level_id'],
                    $newData['grade_level_id'],
                    sprintf(
                        'تم تغيير المرحلة الدراسية من "%s" إلى "%s"',
                        $oldGradeLevel?->name ?? 'غير محدد',
                        $newGradeLevel?->name ?? 'غير محدد'
                    ),
                    [
                        'old_grade_level_name' => $oldGradeLevel?->name,
                        'new_grade_level_name' => $newGradeLevel?->name,
                        'academic_year' => $enrollment->academic_year,
                        'school_id' => $enrollment->school_id,
                    ]
                );
            }

            // Log status changes
            if (isset($newData['status']) && $newData['status'] != $oldData['old_status']) {
                EnrollmentLog::logChange(
                    $enrollment->id,
                    $enrollment->student_id,
                    'status_change',
                    'status',
                    $oldData['old_status'],
                    $newData['status'],
                    sprintf(
                        'تم تغيير حالة التسجيل من "%s" إلى "%s"',
                        $this->getStatusLabel($oldData['old_status']),
                        $this->getStatusLabel($newData['status'])
                    ),
                    [
                        'academic_year' => $enrollment->academic_year,
                        'school_id' => $enrollment->school_id,
                    ]
                );
            }

            // Log classroom changes
            if (isset($newData['classroom_id']) && $newData['classroom_id'] != $oldData['old_classroom_id']) {
                $oldClassroom = \App\Models\Classroom::find($oldData['old_classroom_id']);
                $newClassroom = \App\Models\Classroom::find($newData['classroom_id']);
                
                EnrollmentLog::logChange(
                    $enrollment->id,
                    $enrollment->student_id,
                    'classroom_change',
                    'classroom_id',
                    $oldData['old_classroom_id'],
                    $newData['classroom_id'],
                    sprintf(
                        'تم تغيير الفصل الدراسي من "%s" إلى "%s"',
                        $oldClassroom?->name ?? 'غير محدد',
                        $newClassroom?->name ?? 'غير محدد'
                    ),
                    [
                        'old_classroom_name' => $oldClassroom?->name,
                        'new_classroom_name' => $newClassroom?->name,
                        'academic_year' => $enrollment->academic_year,
                        'school_id' => $enrollment->school_id,
                    ]
                );
            }

            // Log academic year changes
            if (isset($newData['academic_year']) && $newData['academic_year'] != $oldData['old_academic_year']) {
                EnrollmentLog::logChange(
                    $enrollment->id,
                    $enrollment->student_id,
                    'academic_year_change',
                    'academic_year',
                    $oldData['old_academic_year'],
                    $newData['academic_year'],
                    sprintf(
                        'تم تغيير العام الدراسي من "%s" إلى "%s"',
                        $oldData['old_academic_year'],
                        $newData['academic_year']
                    ),
                    [
                        'school_id' => $enrollment->school_id,
                    ]
                );
            }

            // Log discount changes
            if (isset($newData['discount']) && $newData['discount'] != $oldData['old_discount']) {
                EnrollmentLog::logChange(
                    $enrollment->id,
                    $enrollment->student_id,
                    'discount_change',
                    'discount',
                    $oldData['old_discount'],
                    $newData['discount'],
                    sprintf(
                        'تم تغيير الخصم من %d%% إلى %d%%',
                        $oldData['old_discount'] ?? 0,
                        $newData['discount'] ?? 0
                    ),
                    [
                        'academic_year' => $enrollment->academic_year,
                        'school_id' => $enrollment->school_id,
                    ]
                );
            }

        } catch (\Exception $e) {
            // Log the error but don't fail the enrollment update
            \Log::error('Failed to log enrollment changes for enrollment ' . $enrollment->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Change enrollment type for a specific enrollment.
     */
    public function changeEnrollmentType(Request $request, Enrollment $enrollment)
    {
        // Authorization: change of enrollment_type requires explicit permission
        // abort_unless(auth()->user() && auth()->user()->can('set student enrollment type'), 403, 'ليس لديك صلاحية لتحديد نوع تسجيل الطالب');

        $validator = Validator::make($request->all(), [
            'enrollment_type' => ['required', Rule::in(['regular','scholarship','free'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $oldEnrollmentType = $enrollment->enrollment_type;
        $newEnrollmentType = $validatedData['enrollment_type'];

        // Check if the enrollment type is actually changing
        if ($oldEnrollmentType === $newEnrollmentType) {
            return response()->json(['message' => 'نوع التسجيل لم يتغير'], 200);
        }

        // Update the enrollment type
        $enrollment->update(['enrollment_type' => $newEnrollmentType]);

        // Log the enrollment type change
        $this->logEnrollmentChanges($enrollment, $validatedData, [
            'old_enrollment_type' => $oldEnrollmentType,
        ]);

        // Get enrollment type labels for response
        $typeLabels = [
            'regular' => 'عادي',
            'scholarship' => 'منحة',
            'free' => 'إعفاء'
        ];

        return response()->json([
            'message' => sprintf(
                'تم تغيير نوع التسجيل من %s إلى %s بنجاح',
                $typeLabels[$oldEnrollmentType] ?? $oldEnrollmentType,
                $typeLabels[$newEnrollmentType] ?? $newEnrollmentType
            ),
            'enrollment' => new EnrollmentResource($enrollment->load(['student', 'gradeLevel', 'classroom', 'school']))
        ], 200);
    }

    /**
     * Get human-readable status label.
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'active' => 'نشط',
            'transferred' => 'منقول',
            'graduated' => 'متخرج',
            'withdrawn' => 'منسحب',
            default => $status,
        };
    }
}
