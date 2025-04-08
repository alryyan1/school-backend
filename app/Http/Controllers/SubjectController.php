<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\SubjectResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $this->authorize('viewAny', Subject::class);
        $subjects = Subject::orderBy('name')->get(); // Get all, no pagination needed for subjects typically
        return SubjectResource::collection($subjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Subject::class);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            // Add validation for other fields if they exist (is_active, credit_hours, type)
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $subject = Subject::create($validator->validated());
        return new SubjectResource($subject); // 201 implicit
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        // $this->authorize('view', $subject);
        return new SubjectResource($subject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        // $this->authorize('update', $subject);
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => ['sometimes','required','string','max:50', Rule::unique('subjects')->ignore($subject->id)],
            'description' => 'nullable|string',
            // Add validation for other fields if they exist
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $subject->update($validator->validated());
        return new SubjectResource($subject->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // $this->authorize('delete', $subject);

        // ** CHECK FOR RELATIONSHIPS BEFORE DELETING **
        // Example: Check if any teacher is assigned this subject
         if ($subject->teachers()->exists()) { // Assumes teachers() relationship exists
             return response()->json(['message' => 'لا يمكن حذف المادة لوجود معلمين مرتبطين بها.'], 409); // Conflict
         }
        // Add checks for other relationships (courses, grade_level_subject pivot, etc.)

        $subject->delete(); // Use soft delete if enabled

        return response()->json(['message' => 'تم حذف المادة بنجاح'], 200);
        // return response()->noContent(); // 204 Alternative
    }
}