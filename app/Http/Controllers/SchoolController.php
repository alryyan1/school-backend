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
        $schools = School::latest()->get(); // Use get() instead of paginate()

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
            'email' => 'required|email|max:255|unique:schools,email',
            'principal_name' => 'nullable|string|max:255',
            'establishment_date' => 'nullable|date_format:Y-m-d',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Logo validation
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
}
