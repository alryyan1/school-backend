<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Resources\StudentResource; // Import the API Resource class (if you create one)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Import the Validator class

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return $students;
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
        return $student;
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
