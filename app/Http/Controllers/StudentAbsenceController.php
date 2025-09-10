<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentAbsenceResource;
use App\Models\StudentAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentAbsenceController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enrollment_id' => 'required|integer|exists:enrollments,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $absences = StudentAbsence::where('enrollment_id', $request->integer('enrollment_id'))
            ->orderByDesc('absent_date')
            ->orderByDesc('created_at')
            ->get();

        return StudentAbsenceResource::collection($absences);
    }

    public function store(Request $request)
    {
        // Adjust permission if you have a specific one
        abort_unless(auth()->check(), 403);
        $validator = Validator::make($request->all(), [
            'enrollment_id' => 'required|integer|exists:enrollments,id',
            'absent_date' => 'required|date',
            'reason' => 'nullable|string',
            'excused' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $absence = StudentAbsence::create($validator->validated());
        return new StudentAbsenceResource($absence);
    }

    public function update(Request $request, StudentAbsence $studentAbsence)
    {
        abort_unless(auth()->check(), 403);
        $validator = Validator::make($request->all(), [
            'absent_date' => 'sometimes|required|date',
            'reason' => 'nullable|string',
            'excused' => 'sometimes|required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }
        $studentAbsence->update($validator->validated());
        return new StudentAbsenceResource($studentAbsence->fresh());
    }

    public function destroy(StudentAbsence $studentAbsence)
    {
        abort_unless(auth()->check(), 403);
        $studentAbsence->delete();
        return response()->json(['message' => 'تم حذف سجل الغياب بنجاح']);
    }
}


