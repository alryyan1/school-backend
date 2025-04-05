<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\TeacherResource; // Import the resource
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import Storage
use Illuminate\Validation\Rule; // For unique validation on update

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optional: Add Authorization Check
        // $this->authorize('viewAny', Teacher::class);

        // Use pagination for better performance
        $teachers = Teacher::latest()->paginate(15); // Example: 15 per page
        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Optional: Add Authorization Check
        // $this->authorize('create', Teacher::class);

        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string|max:20|unique:teachers,national_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:teachers,email',
            'phone' => 'nullable|string|max:15',
            'gender' => 'required|in:ذكر,انثي', // Match enum in migration
            'birth_date' => 'nullable|date_format:Y-m-d', // Expect YYYY-MM-DD format
            'qualification' => 'required|string|max:255',
            'hire_date' => 'required|date_format:Y-m-d', // Expect YYYY-MM-DD format
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            // 'is_active' => 'sometimes|boolean', // Allow boolean (true/false, 1/0)
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $photoPath = null;

        // Handle File Upload
        if ($request->hasFile('photo')) {
            // Store in 'public/teachers' directory, returns 'teachers/filename.jpg'
            $photoPath = $request->file('photo')->store('teachers', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Ensure is_active defaults correctly if not sent
        $validatedData['is_active'] = $request->boolean('is_active', true);

        $teacher = Teacher::create($validatedData);

        return new TeacherResource($teacher); // Return resource with 201 status (implicit)
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher) // Route model binding
    {
        // Optional: Add Authorization Check
        // $this->authorize('view', $teacher);

        return new TeacherResource($teacher);
    }

    /**
     * Update the specified resource in storage.
     * Note: We use POST with _method=PUT/PATCH for file uploads from HTML forms,
     * but APIs often use PUT/PATCH directly. Axios handles this.
     */
    public function update(Request $request, Teacher $teacher)
    {
        // Optional: Add Authorization Check
        // $this->authorize('update', $teacher);

        $validator = Validator::make($request->all(), [
             'national_id' => ['required', 'string', 'max:20', Rule::unique('teachers')->ignore($teacher->id)],
             'name' => 'sometimes|required|string|max:255', // sometimes = only validate if present
             'email' => ['required', 'email', 'max:255', Rule::unique('teachers')->ignore($teacher->id)],
             'phone' => 'nullable|string|max:15',
             'gender' => 'sometimes|required|in:ذكر,انثي',
             'birth_date' => 'nullable|date_format:Y-m-d',
             'qualification' => 'sometimes|required|string|max:255',
             'hire_date' => 'sometimes|required|date_format:Y-m-d',
             'address' => 'nullable|string',
             'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate new photo if uploaded
             'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $photoPath = $teacher->photo; // Keep old path by default

        // Handle File Update
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            // Store new photo
            $photoPath = $request->file('photo')->store('teachers', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Update boolean field correctly if sent
        if ($request->has('is_active')) {
             $validatedData['is_active'] = $request->boolean('is_active');
        }

        $teacher->update($validatedData);

        return new TeacherResource($teacher->fresh()); // Return updated resource
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        // Optional: Add Authorization Check
        // $this->authorize('delete', $teacher);

        // Delete photo file if it exists
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $teacher->delete(); // Performs soft delete if trait is used

        // Return 204 No Content or a success message
        // return response()->noContent();
         return response()->json(['message' => 'تم حذف المدرس بنجاح'], 200);
    }
}