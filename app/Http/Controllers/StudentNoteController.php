<?php

namespace App\Http\Controllers;

use App\Models\StudentNote;
use Illuminate\Http\Request;
use App\Http\Resources\StudentNoteResource;
use Illuminate\Support\Facades\Auth;

class StudentNoteController extends Controller
{
    // List all notes for a given student_academic_years_id
    public function index(Request $request)
    {
        $studentAcademicYearId = $request->query('student_academic_years_id');
        $notes = StudentNote::with('user')
            ->where('student_academic_years_id', $studentAcademicYearId)
            ->orderByDesc('created_at')
            ->get();
        return StudentNoteResource::collection($notes);
    }

    // Store a new note
    public function store(Request $request)
    {
        $request->validate([
            'student_academic_years_id' => 'required|exists:student_academic_years,id',
            'note' => 'required|string',
        ]);
        $note = StudentNote::create([
            'student_academic_years_id' => $request->student_academic_years_id,
            'note' => $request->note,
            'user_id' => Auth::id(),
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
