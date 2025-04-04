<?php
// app/Http/Controllers/SubjectController.php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\SubjectResource; // Import the API Resource class (if you create one)
use Illuminate\Support\Facades\Validator; // Import the Validator class

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return SubjectResource::collection($subjects); // Return as a collection of API Resources
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $subject = Subject::create($request->all());

        return new SubjectResource($subject); // Return the newly created subject as an API Resource , 201 Created
    }

    public function show(Subject $subject)
    {
        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404); // Not Found
        }
        return new SubjectResource($subject); // Return as an API Resource
    }

    public function update(Request $request, Subject $subject)
    {
         if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404); // Not Found
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $subject->update($request->all());

        return new SubjectResource($subject); // Return the updated subject as an API Resource
    }

    public function destroy(Subject $subject)
    {
        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404); // Not Found
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted'], 204); // No Content
    }
}