<?php

namespace App\Http\Controllers;

use App\Models\StudentNote;
use Illuminate\Http\Request;
use App\Http\Resources\StudentNoteResource;
use Illuminate\Support\Facades\Auth;

class StudentNoteController extends Controller
{
    // List all notes for a given enrollment_id
    public function index(Request $request)
    {
        $enrollmentId = $request->query('enrollment_id');
        $notes = StudentNote::with('user')
            ->where('enrollment_id', $enrollmentId)
            ->orderByDesc('created_at')
            ->get();
        return StudentNoteResource::collection($notes);
    }

    // Store a new note
    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'note' => 'required|string',
        ]);
        $note = StudentNote::create([
            'enrollment_id' => $request->enrollment_id,
            'note' => $request->note,
            'user_id' => Auth::id() ?? 1, // Default to user ID 1 if not authenticated
        ]);
        $note->load('user');
        return new StudentNoteResource($note);
    }

    // Update a note
    public function update(Request $request, StudentNote $studentNote)
    {
        $request->validate([
            'note' => 'required|string',
        ]);
        $studentNote->update([
            'note' => $request->note,
        ]);
        $studentNote->load('user');
        return new StudentNoteResource($studentNote);
    }

    // Delete a note
    public function destroy(StudentNote $studentNote)
    {
        $studentNote->delete();
        return response()->json(['message' => 'Note deleted']);
    }
}
