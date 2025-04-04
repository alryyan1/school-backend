<?php

// app/Http/Controllers/ClassroomController.php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClassroomResource;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id', // Ensure the school_id exists in the schools table
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $classroom = Classroom::create($request->all());

    }

    public function show(Classroom $classroom)
    {
         if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }
    }

    public function update(Request $request, Classroom $classroom)
    {
         if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $classroom->update($request->all());

    }

    public function destroy(Classroom $classroom)
    {
         if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        $classroom->delete();

        return response()->json(['message' => 'Classroom deleted'], 204);
    }
}
