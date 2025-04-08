<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Resources\StudentResource; // Import the API Resource class (if you create one)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Import the Validator class

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return  StudentResource::collection($students);
    }
    public function updatePhoto(Request $request, Student $student)
    {
        // --- Authorization (Example using Policy) ---
        // Make sure you have a StudentPolicy with an 'update' or 'updatePhoto' method
        // $this->authorize('update', $student); // Or specific 'updatePhoto' ability
        // --- End Authorization ---

        // --- Validation ---
        $validator = Validator::make($request->all(), [
            // Validate the 'photo' field from the FormData
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB example
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من الصورة', 'errors' => $validator->errors()], 422);
        }
        // --- End Validation ---

        try {
            // --- Delete Old Photo ---
            if ($student->image && Storage::disk('public')->exists($student->image)) {
                 Storage::disk('public')->delete($student->image);
            }
            // --- End Delete Old Photo ---

            // --- Store New Photo ---
            // Store in 'storage/app/public/students_photos'
            // The 'store' method generates a unique filename
            $path = $request->file('image')->store('students_photos', 'public');
            // --- End Store New Photo ---


            // --- Update Database ---
            // Save the relative path to the database
            $student->image = $path;
            $student->save();
            // --- End Update Database ---


            // --- Return Response ---
            // Return the updated student resource (which should generate the full URL)
            // Use fresh() to ensure you get the updated model attributes.
            return new StudentResource($student->fresh());
            // --- End Return Response ---

        } catch (\Exception $e) {
             // Handle potential storage errors or other exceptions
             report($e); // Log the error
             return response()->json(['message' => 'حدث خطأ أثناء رفع الصورة. '. $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_job' => 'required|string|max:255',
            'father_address' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
            'father_whatsapp' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:255',
            'mother_job' => 'required|string|max:255',
            'mother_address' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
            'mother_whatsapp' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'wished_level' => 'required|in:روضه,ابتدائي,متوسط,ثانوي',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Concatenate the error messages into a single string
            $errorMessage = '';
            foreach ($errors->all() as $error) {
                $errorMessage .= $error . ' '; // Add a space between messages
            }

            return response()->json(['message' => $errorMessage], 422);
        }
        $data = $request->all();
        $data['approved_by_user'] = Auth::id();
        $student = Student::create($data);

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }
        return new StudentResource($student);
    }

    public function update(Request $request, Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }

        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_job' => 'required|string|max:255',
            'father_address' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
            'father_whatsapp' => 'nullable|string|max:20',
            'mother_name' => 'required|string|max:255',
            'mother_job' => 'required|string|max:255',
            'mother_address' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:20',
            'mother_whatsapp' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'wished_level' => 'required|in:روضه,ابتدائي,متوسط,ثانوي',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => implode(' ',$validator->errors()->all())], 422); // Unprocessable Entity
        }

        $student->update($request->all());
    }

    public function destroy(Student $student)
    {
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404); // Not Found
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted'], 204); // No Content (successful deletion)
    }
}
