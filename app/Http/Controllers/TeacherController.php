<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectResource;
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
     * Get the subjects assigned to a specific teacher.
     * GET /api/teachers/{teacher}/subjects
     */
    public function getSubjects(Teacher $teacher)
    {
        // $this->authorize('view', $teacher); // Optional: Check if user can view teacher details

        // Eager load the subjects relationship
        $teacher->load('subjects');

        // Return the collection of subjects using SubjectResource
        return SubjectResource::collection($teacher->subjects);
    }

    /**
     * Update/Sync the subjects assigned to a specific teacher.
     * PUT /api/teachers/{teacher}/subjects
     */
    public function updateSubjects(Request $request, Teacher $teacher)
    {
        // $this->authorize('update', $teacher); // Optional: Check if user can update teacher details

        $validator = Validator::make($request->all(), [
            // Expect an array of subject IDs. Allow empty array to remove all subjects.
            'subject_ids' => 'present|array', // 'present' ensures the key exists, even if empty array
            'subject_ids.*' => 'integer|exists:subjects,id' // Validate each item in the array
        ]);

        if ($validator->fails()) {
            // Get the first error message from each field
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            
            // Join all error messages into a single message
            $consolidatedMessage = implode('، ', $errorMessages);
            
            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
        }

        // Use sync to update the pivot table.
        // This adds missing IDs, removes IDs not present in the array.
        $teacher->subjects()->sync($validator->validated()['subject_ids']);

        // Return success response, maybe with the updated list
         $teacher->load('subjects'); // Reload the relationship
         return SubjectResource::collection($teacher->subjects);
        // Or just a success message:
        // return response()->json(['message' => 'تم تحديث مواد المدرس بنجاح']);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Optional: Add Authorization Check
        // $this->authorize('create', Teacher::class);

        $validator = Validator::make($request->all(), [
            // Required core fields
            'national_id' => 'required|string|max:20|unique:teachers,national_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:teachers,email',
            'gender' => 'required|in:ذكر,انثي',
            'qualification' => 'required|string|max:255',
            'hire_date' => 'required|date_format:Y-m-d',

            // Optional contact
            'phone' => 'nullable|string|max:15',
            'secondary_phone' => 'nullable|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',

            // Optional personal details
            'birth_date' => 'nullable|date_format:Y-m-d',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'document_type' => 'nullable|in:جواز سفر,البطاقة الشخصية,الرقم الوطني',
            'document_number' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:اعزب,متزوج,مطلق,ارمل',
            'number_of_children' => 'nullable|integer',
            'children_in_school' => 'nullable|integer',

            // Optional education/professional
            'highest_qualification' => 'nullable|in:جامعي,ثانوي',
            'specialization' => 'nullable|string|max:255',
            'academic_degree' => 'nullable|in:دبلوم,بكالوريوس,ماجستير,دكتوراه',
            'appointment_date' => 'nullable|date_format:Y-m-d',
            'years_of_teaching_experience' => 'nullable|integer',
            'training_courses' => 'nullable|string',

            // Files/paths
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'academic_qualifications_doc_path' => 'nullable|string',
            'personal_id_doc_path' => 'nullable|string',
            'cv_doc_path' => 'nullable|string',

            // Flags
            // 'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            // Get the first error message from each field
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            
            // Join all error messages into a single message
            $consolidatedMessage = implode('، ', $errorMessages);
            
            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
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
            // Required core fields
            'national_id' => ['required', 'string', 'max:20', Rule::unique('teachers')->ignore($teacher->id)],
            'name' => 'sometimes|required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('teachers')->ignore($teacher->id)],
            'gender' => 'sometimes|required|in:ذكر,انثي',
            'qualification' => 'sometimes|required|string|max:255',
            'hire_date' => 'sometimes|required|date_format:Y-m-d',

            // Optional contact
            'phone' => 'nullable|string|max:15',
            'secondary_phone' => 'nullable|string|max:15',
            'whatsapp_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',

            // Optional personal details
            'birth_date' => 'nullable|date_format:Y-m-d',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'document_type' => 'nullable|in:جواز سفر,البطاقة الشخصية,الرقم الوطني',
            'document_number' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:اعزب,متزوج,مطلق,ارمل',
            'number_of_children' => 'nullable|integer',
            'children_in_school' => 'nullable|integer',

            // Optional education/professional
            'highest_qualification' => 'nullable|in:جامعي,ثانوي',
            'specialization' => 'nullable|string|max:255',
            'academic_degree' => 'nullable|in:دبلوم,بكالوريوس,ماجستير,دكتوراه',
            'appointment_date' => 'nullable|date_format:Y-m-d',
            'years_of_teaching_experience' => 'nullable|integer',
            'training_courses' => 'nullable|string',

            // Files/paths
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'academic_qualifications_doc_path' => 'nullable|string',
            'personal_id_doc_path' => 'nullable|string',
            'cv_doc_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // Get the first error message from each field
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            
            // Join all error messages into a single message
            $consolidatedMessage = implode('، ', $errorMessages);
            
            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
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

    /**
     * Upload multiple PDF documents for a teacher and store them under a per-teacher folder.
     * POST /api/teachers/{teacher}/documents
     */
    public function uploadDocuments(Request $request, Teacher $teacher)
    {
        // $this->authorize('update', $teacher); // Optional authorization

        $validator = Validator::make($request->all(), [
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf|max:20480', // max 20MB per file
        ]);

        if ($validator->fails()) {
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            $consolidatedMessage = implode('، ', $errorMessages);

            return response()->json([
                'message' => $consolidatedMessage,
                'errors' => $validator->errors()
            ], 422);
        }

        $storedFiles = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                // Store under public/teachers/{id}/documents
                $path = $file->store('teachers/' . $teacher->id . '/documents', 'public');
                if ($path) {
                    $storedFiles[] = $path;
                }
            }
        }

        return response()->json([
            'message' => 'تم رفع المستندات بنجاح',
            'files' => $storedFiles,
        ], 201);
    }

    /**
     * List teacher uploaded documents (PDFs) under public path.
     * GET /api/teachers/{teacher}/documents
     */
    public function listDocuments(Teacher $teacher)
    {
        $directory = 'teachers/' . $teacher->id . '/documents';
        $files = [];
        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);
        }
        return response()->json([
            'files' => $files,
        ]);
    }
}