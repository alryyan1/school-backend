<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\GradeLevel; // Import related models
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\ClassroomResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     * Allow filtering by school_id and grade_level_id
     */
    // public function index(Request $request)
    // {
    //     // $this->authorize('viewAny', Classroom::class);

    //     $query = Classroom::with(['school', 'gradeLevel', 'homeroomTeacher'])->latest();

    //     // Filter by School
    //     if ($request->filled('school_id')) {
    //         $query->where('school_id', $request->input('school_id'));
    //     }

    //      // Filter by Grade Level
    //      if ($request->filled('grade_level_id')) {
    //         $query->where('grade_level_id', $request->input('grade_level_id'));
    //     }

    //     $classrooms = $query->get(); // Get all filtered results (no pagination)

    //     return ClassroomResource::collection($classrooms);
    // }
    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|integer|exists:schools,id',
            'grade_level_id' => 'nullable|integer|exists:grade_levels,id',
        ]);
        if ($validator->fails()) return response()->json(['message'=>'School is required', 'errors'=>$validator->errors()], 422);

        $schoolId = $request->input('school_id');
        $gradeLevelId = $request->input('grade_level_id');

        $query = Classroom::with(['gradeLevel:id,name'])
            ->where('school_id', $schoolId);

        // Only filter by grade level if it's provided
        if ($gradeLevelId) {
            $query->where('grade_level_id', $gradeLevelId);
        }

        $classrooms = $query->orderBy('name')->get();
        return ClassroomResource::collection($classrooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Classroom::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'grade_level_id' => ['required', 'integer', Rule::exists('grade_levels', 'id')],
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
            'capacity' => 'required|integer|min:1',
            'school_id' => ['required', 'integer', Rule::exists('schools', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $classroom = Classroom::create($validator->validated());

        return new ClassroomResource($classroom->load(['school', 'gradeLevel', 'homeroomTeacher'])); // Load relations
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        // $this->authorize('view', $classroom);
        return new ClassroomResource($classroom->load(['school', 'gradeLevel', 'homeroomTeacher']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        // $this->authorize('update', $classroom);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'grade_level_id' => ['sometimes', 'required', 'integer', Rule::exists('grade_levels', 'id')],
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')], // Allow setting to null
            'capacity' => 'sometimes|required|integer|min:1',
            // Usually school_id is not updated, but allow if needed
            // 'school_id' => ['sometimes', 'required', 'integer', Rule::exists('schools', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $classroom->update($validator->validated());

        return new ClassroomResource($classroom->fresh()->load(['school', 'gradeLevel', 'homeroomTeacher']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        // $this->authorize('delete', $classroom);

        // ** CHECK FOR RELATIONSHIPS **
        // Add check for students if implemented
        // if ($classroom->students()->exists()) {
        //     return response()->json(['message' => 'لا يمكن حذف الفصل لوجود طلاب مسجلين به.'], 409); // Conflict
        // }

        $classroom->delete();

        return response()->json(['message' => 'تم حذف الفصل الدراسي بنجاح.'], 200);
    }
}
