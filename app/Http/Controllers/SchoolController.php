<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Resources\SchoolResource; // Import resource
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import Storage
use Illuminate\Validation\Rule; // For unique validation on update
use App\Http\Resources\GradeLevelResource;
use App\Models\GradeLevel;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optional: Authorization check
        // $this->authorize('viewAny', School::class);

        // Get all schools, ordered if desired
        $schools = School::withCount('classrooms')->with('user')->orderBy('name')->get(); // Use get() instead of paginate()

        // Return a resource collection (still good practice for consistent format)
        return SchoolResource::collection($schools);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Optional: Authorization check
        // $this->authorize('create', School::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools,code',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'string',
            'principal_name' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date_format:Y-m-d',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Logo validation
            'user_id' => 'nullable|integer|exists:users,id', // User/Manager assignment
            // 'is_active' => 'sometimes|boolean', // Uncomment if added later
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من البيانات', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $logoPath = null;

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            // Store in 'public/schools_logos' directory
            $logoPath = $request->file('logo')->store('schools_logos', 'public');
            $validatedData['logo'] = $logoPath;
        }

        // Uncomment and handle if is_active is added
        // $validatedData['is_active'] = $request->boolean('is_active', true);

        $school = School::create($validatedData);

        return new SchoolResource($school); // 201 status implicit
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school) // Route model binding
    {
        // Optional: Authorization check
        // $this->authorize('view', $school);
        $school->load('user'); // Load the user relationship
        return new SchoolResource($school);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        // Optional: Authorization check
        // $this->authorize('update', $school);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('schools')->ignore($school->id)],
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('schools')->ignore($school->id)],
            'principal_name' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date_format:Y-m-d',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate new logo
            'user_id' => 'nullable|integer|exists:users,id', // User/Manager assignment
            // 'is_active' => 'sometimes|boolean', // Uncomment if added later
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من البيانات', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $logoPath = $school->logo; // Keep old path by default

        // Handle Logo Update
        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            // Store new logo
            $logoPath = $request->file('logo')->store('schools_logos', 'public');
            $validatedData['logo'] = $logoPath;
        }

        // Uncomment and handle if is_active is added
        // if ($request->has('is_active')) {
        //     $validatedData['is_active'] = $request->boolean('is_active');
        // }

        $school->update($validatedData);

        return new SchoolResource($school->fresh()); // Return updated resource
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        // Optional: Authorization check
        // $this->authorize('delete', $school);

        // Delete logo file if it exists
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete(); // Performs soft delete if trait is used & column exists

        return response()->json(['message' => 'تم حذف المدرسة بنجاح'], 200);
        // return response()->noContent(); // Alternative 204 response
    }
    /**
     * Get the Grade Levels assigned to a specific School.
     * GET /api/schools/{school}/grade-levels
     */
    public function getAssignedGradeLevels(School $school)
    {
        // Optional: Authorization check if user can view school details
        // $this->authorize('view', $school);

        // Return only the IDs for simplicity, or full resources
        // return response()->json(['data' => $school->gradeLevels()->pluck('grade_levels.id')]);

        // Or return full resources
        return GradeLevelResource::collection($school->gradeLevels()->orderBy('id')->get());
    }
    /**
     * Update/Sync the Grade Levels assigned to a specific School.
     * PUT /api/schools/{school}/grade-levels
     */
    public function updateAssignedGradeLevels(Request $request, School $school)
    {
        // Optional: Authorization check if user can update school details
        // $this->authorize('update', $school);

        $validator = Validator::make($request->all(), [
            // Expect an array of grade level IDs. Allow empty array.
            'grade_level_ids' => 'present|array',
            'grade_level_ids.*' => 'integer|exists:grade_levels,id' // Validate each ID
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من المراحل الدراسية', 'errors' => $validator->errors()], 422);
        }

        // Use sync() to update the pivot table.
        // It automatically adds/removes associations based on the provided array.
        $school->gradeLevels()->sync($validator->validated()['grade_level_ids']);

        // Return success response
        return response()->json(['message' => 'تم تحديث المراحل الدراسية للمدرسة بنجاح.']);

        // Or return the updated list:
        // return GradeLevelResource::collection($school->gradeLevels()->orderBy('id')->get());
    }
    /**
     * Attach one or more Grade Levels to a School with their basic fees.
     * POST /api/schools/{school}/grade-levels
     */
    public function attachGradeLevels(Request $request, School $school) {
        // $this->authorize('update', $school); // Or a more specific permission

        $validator = Validator::make($request->all(), [
            // Expect an array of objects: [{ grade_level_id: 1, basic_fees: 5000 }, ...]
            'grade_level_id' => [
                'required', 'integer',
                Rule::exists('grade_levels','id'),
                // Ensure this grade level isn't already assigned to this school
                Rule::unique('school_grade_levels')->where(function ($query) use ($school) {
                    return $query->where('school_id', $school->id);
                })
            ],
            'assignments.*.basic_fees' => 'required|integer|min:0', // Changed to integer as per migration
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $assignmentsData = [];
        $grade_level_id = $request->input('grade_level_id');
        $basic_fees = $request->input('basic_fees');

        $school->gradeLevels()->attach([
            $grade_level_id => ['basic_fees'=>$basic_fees]
        ]);

        // Return the newly assigned grades (optional) or just success
         return GradeLevelResource::collection($school->gradeLevels()->whereIn('grade_levels.id', array_keys($assignmentsData))->get());
        // return response()->json(['message' => 'تم تعيين المراحل بنجاح']);
    }
      /**
     * Update the basic_fees for a specific School-GradeLevel assignment.
     * PUT /api/schools/{school}/grade-levels/{grade_level}
     */
    public function updateGradeLevelFee(Request $request, School $school, GradeLevel $gradeLevel) {
        // $this->authorize('update', $school);

        $validator = Validator::make($request->all(), [
            'basic_fees' => 'required|integer|min:0', // Validate the fee
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        // Check if the grade is actually assigned to the school first
        if (!$school->gradeLevels()->where('grade_levels.id', $gradeLevel->id)->exists()) {
             return response()->json(['message' => 'المرحلة المحددة غير معينة لهذه المدرسة.'], 404);
        }

        // Use updateExistingPivot to update the extra pivot field
        $school->gradeLevels()->updateExistingPivot($gradeLevel->id, [
            'basic_fees' => $validator->validated()['basic_fees'],
        ]);

        // Fetch the updated relationship data to return
        $updatedGrade = $school->gradeLevels()->find($gradeLevel->id);
        return new GradeLevelResource($updatedGrade);
        // return response()->json(['message' => 'تم تحديث الرسوم بنجاح']);
    }

    /**
     * Detach/Unassign a Grade Level from a School.
     * DELETE /api/schools/{school}/grade-levels/{grade_level}
     */
    public function detachGradeLevel(School $school, GradeLevel $gradeLevel) {
        // $this->authorize('update', $school); // Use update permission?

        // Add checks here: Prevent detach if classrooms or enrollments exist for this school/grade combo?
        // Example check (requires relationships defined):
        // if (Classroom::where('school_id', $school->id)->where('grade_level_id', $gradeLevel->id)->exists()) {
        //    return response()->json(['message' => 'لا يمكن إلغاء تعيين المرحلة لوجود فصول مرتبطة بها.'], 409);
        // }

        $detached = $school->gradeLevels()->detach($gradeLevel->id); // Returns number of records detached

        if ($detached) {
            return response()->json(['message' => 'تم إلغاء تعيين المرحلة بنجاح.']);
        } else {
            return response()->json(['message' => 'المرحلة لم تكن معينة لهذه المدرسة.'], 404);
        }
    }

}
