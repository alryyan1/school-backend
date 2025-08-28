<?php

namespace App\Http\Controllers;

use App\Http\Resources\GradeLevelSubjectResource;
use App\Models\GradeLevelSubject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GradeLevelSubjectController extends Controller
{
    /**
     * Get all subjects for a specific grade level.
     */
    public function getAllByGradeLevel(int $gradeLevelId): JsonResponse
    {
        $assignments = GradeLevelSubject::where('grade_level_id', $gradeLevelId)
            ->with(['subject', 'teacher', 'gradeLevel'])
            ->get();

        return response()->json([
            'data' => GradeLevelSubjectResource::collection($assignments)
        ]);
    }

    /**
     * Create a new subject assignment for a grade level.
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'grade_level_id' => 'required|integer|exists:grade_levels,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if this subject is already assigned to this grade level
        $existingAssignment = GradeLevelSubject::where('grade_level_id', $request->grade_level_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'message' => 'This subject is already assigned to this grade level'
            ], 409);
        }

        $assignment = GradeLevelSubject::create($request->all());
        $assignment->load(['subject', 'teacher', 'gradeLevel']);

        return response()->json([
            'message' => 'Subject assigned successfully',
            'data' => new GradeLevelSubjectResource($assignment)
        ], 201);
    }

    /**
     * Update a subject assignment.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $assignment = GradeLevelSubject::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'grade_level_id' => 'sometimes|integer|exists:grade_levels,id',
            'subject_id' => 'sometimes|integer|exists:subjects,id',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // If changing grade_level_id or subject_id, check for duplicates
        if (($request->has('grade_level_id') && $request->grade_level_id != $assignment->grade_level_id) ||
            ($request->has('subject_id') && $request->subject_id != $assignment->subject_id)) {
            
            $newGradeLevelId = $request->grade_level_id ?? $assignment->grade_level_id;
            $newSubjectId = $request->subject_id ?? $assignment->subject_id;

            $existingAssignment = GradeLevelSubject::where('grade_level_id', $newGradeLevelId)
                ->where('subject_id', $newSubjectId)
                ->where('id', '!=', $id)
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'message' => 'This subject is already assigned to this grade level'
                ], 409);
            }
        }

        $assignment->update($request->all());
        $assignment->load(['subject', 'teacher', 'gradeLevel']);

        return response()->json([
            'message' => 'Assignment updated successfully',
            'data' => new GradeLevelSubjectResource($assignment)
        ]);
    }

    /**
     * Delete a subject assignment.
     */
    public function delete(int $id): JsonResponse
    {
        $assignment = GradeLevelSubject::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'message' => 'Assignment deleted successfully'
        ]);
    }
}
