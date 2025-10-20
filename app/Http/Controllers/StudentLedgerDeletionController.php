<?php

namespace App\Http\Controllers;

use App\Models\StudentLedgerDeletion;
use App\Http\Resources\StudentLedgerDeletionResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentLedgerDeletionController extends Controller
{
    /**
     * Get all deletion records with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'student_id' => 'nullable|exists:students,id',
            'enrollment_id' => 'nullable|exists:enrollments,id',
            'deleted_by' => 'nullable|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = StudentLedgerDeletion::with([
            'enrollment.student', 
            'enrollment.school', 
            'enrollment.gradeLevel', 
            'enrollment.classroom',
            'student',
            'originalCreator',
            'deletedBy'
        ])
        ->orderBy('deleted_at', 'desc');

        // Apply filters
        if ($request->start_date) {
            $query->where('deleted_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('deleted_at', '<=', $request->end_date);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->enrollment_id) {
            $query->where('enrollment_id', $request->enrollment_id);
        }

        if ($request->deleted_by) {
            $query->where('deleted_by', $request->deleted_by);
        }

        $perPage = $request->per_page ?? 20;
        $deletions = $query->paginate($perPage);

        return response()->json([
            'deletions' => StudentLedgerDeletionResource::collection($deletions),
            'pagination' => [
                'current_page' => $deletions->currentPage(),
                'last_page' => $deletions->lastPage(),
                'per_page' => $deletions->perPage(),
                'total' => $deletions->total(),
            ]
        ]);
    }

    /**
     * Get a specific deletion record.
     */
    public function show($id): JsonResponse
    {
        $deletion = StudentLedgerDeletion::with([
            'enrollment.student', 
            'enrollment.school', 
            'enrollment.gradeLevel', 
            'enrollment.classroom',
            'student',
            'originalCreator',
            'deletedBy'
        ])->findOrFail($id);

        return response()->json([
            'deletion' => new StudentLedgerDeletionResource($deletion)
        ]);
    }
}
