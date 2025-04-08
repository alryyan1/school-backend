<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use Illuminate\Http\Request;
use App\Http\Resources\GradeLevelResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GradeLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $this->authorize('viewAny', GradeLevel::class); // Optional Authorization
        // Load counts if needed for display: ->withCount('classrooms')
        $gradeLevels = GradeLevel::orderBy('name')->get(); // Get all, order by name
        return GradeLevelResource::collection($gradeLevels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', GradeLevel::class); // Optional Authorization

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:grade_levels,code',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $gradeLevel = GradeLevel::create($validator->validated());

        return new GradeLevelResource($gradeLevel); // 201 implicit
    }

    /**
     * Display the specified resource.
     */
    public function show(GradeLevel $gradeLevel) // Route Model Binding
    {
         // $this->authorize('view', $gradeLevel); // Optional Authorization
        // Load counts if needed: ->loadCount('classrooms')
        return new GradeLevelResource($gradeLevel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GradeLevel $gradeLevel)
    {
         // $this->authorize('update', $gradeLevel); // Optional Authorization

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('grade_levels')->ignore($gradeLevel->id)],
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $gradeLevel->update($validator->validated());

        return new GradeLevelResource($gradeLevel->fresh()); // Return updated
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeLevel $gradeLevel)
    {
        // $this->authorize('delete', $gradeLevel); // Optional Authorization

        // **Important Deletion Check:** Prevent deletion if related data exists
        if ($gradeLevel->classrooms()->exists() || $gradeLevel->enrollments()->exists()) {
             // Or check other relationships like subjects if implemented
            return response()->json(['message' => 'لا يمكن حذف المرحلة لوجود صفوف أو طلاب مسجلين بها.'], 409); // 409 Conflict
        }

        $gradeLevel->delete();

        return response()->json(['message' => 'تم حذف المرحلة الدراسية بنجاح.'], 200);
        // return response()->noContent(); // Alternative 204
    }
}