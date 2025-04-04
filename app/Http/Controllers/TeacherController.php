<?php
// app/Http/Controllers/TeacherController.php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\TeacherResource; // Import the API Resource class (if you create one)
use Illuminate\Support\Facades\Validator; // Import the Validator class

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::all();
        return TeacherResource::collection($teachers); // Return as a collection of API Resources
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $teacher = Teacher::create($request->all());

        return new TeacherResource($teacher); // Return the newly created teacher as an API Resource , 201 Created
    }

    public function show(Teacher $teacher)
    {
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404); // Not Found
        }
        return new TeacherResource($teacher); // Return as an API Resource
    }

    public function update(Request $request, Teacher $teacher)
    {
         if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404); // Not Found
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $teacher->update($request->all());

        return new TeacherResource($teacher); // Return the updated teacher as an API Resource
    }

    public function destroy(Teacher $teacher)
    {
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404); // Not Found
        }

        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted'], 204); // No Content
    }
}