<?php // app/Http/Controllers/StudentTransportAssignmentController.php
namespace App\Http\Controllers;

use App\Models\StudentTransportAssignment;
use App\Models\StudentAcademicYear;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use App\Http\Resources\StudentTransportAssignmentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentTransportAssignmentController extends Controller
{
    /** Display assignments, filtered by route OR enrollment */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transport_route_id' => 'required_without:student_academic_year_id|integer|exists:transport_routes,id',
            'student_academic_year_id' => 'required_without:transport_route_id|integer|exists:student_academic_years,id',
        ]);
        if ($validator->fails()) return response()->json(['message' => 'يجب تحديد مسار النقل أو تسجيل الطالب', 'errors' => $validator->errors()], 422);

        $query = StudentTransportAssignment::with(['studentAcademicYear.student', 'transportRoute']); // Load needed data

        if ($request->filled('transport_route_id')) {
            $query->where('transport_route_id', $request->input('transport_route_id'));
        }
        if ($request->filled('student_academic_year_id')) {
            $query->where('student_academic_year_id', $request->input('student_academic_year_id'));
        }
        $assignments = $query->get();
        return StudentTransportAssignmentResource::collection($assignments);
    }

    /** Assign a student (via enrollment ID) to a route */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_academic_year_id' => [
                'required',
                'integer',
                'exists:student_academic_years,id',
                Rule::unique('student_transport_assignments'), // Ensure student not already assigned to a route this year
            ],
            'transport_route_id' => 'required|integer|exists:transport_routes,id',
            'pickup_point' => 'nullable|string|max:255',
            'dropoff_point' => 'nullable|string|max:255',
            // ** Add validation: Ensure enrollment's school matches route's school **
            'transport_route_id' => [
                'required',
                'integer',
                Rule::exists('transport_routes', 'id')->where(function ($query) use ($request) {
                    // Find the enrollment record to get the school ID
                    $enrollment = StudentAcademicYear::find($request->input('student_academic_year_id'));
                    if ($enrollment) {
                        $query->where('school_id', $enrollment->school_id);
                    } else {
                        // Force validation failure if enrollment not found (though exists rule handles it)
                        $query->where('id', -1); // Non-existent ID
                    }
                })
            ]
        ]);
        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق أو الطالب مسجل بالفعل في مسار آخر', 'errors' => $validator->errors()], 422);

        $assignment = StudentTransportAssignment::create($validator->validated());
        return new StudentTransportAssignmentResource($assignment->load(['studentAcademicYear.student', 'transportRoute']));
    }

    /** Update pickup/dropoff points */
    public function update(Request $request, StudentTransportAssignment $studentTransportAssignment)
    {
        $validator = Validator::make($request->all(), [
            'pickup_point' => 'nullable|string|max:255',
            'dropoff_point' => 'nullable|string|max:255',
            // Cannot change student, year or route via update
        ]);
        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        $studentTransportAssignment->update($validator->validated());
        return new StudentTransportAssignmentResource($studentTransportAssignment->fresh()->load(['studentAcademicYear.student', 'transportRoute']));
    }


    /** Unassign a student from their route */
    public function destroy(StudentTransportAssignment $studentTransportAssignment)
    {
        $studentTransportAssignment->delete();
        return response()->json(['message' => 'تم إلغاء تعيين الطالب من المسار بنجاح.'], 200);
    }
}
