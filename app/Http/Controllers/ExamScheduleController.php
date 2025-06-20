<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\Classroom;
use App\Models\User; // Or Teacher model
use Illuminate\Http\Request;
use App\Http\Resources\ExamScheduleResource;
use App\Models\AcademicYearSubject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // For date validation
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExamScheduleController extends Controller
{
    use AuthorizesRequests; // For policy-based authorization

    /**
     * Display a listing of schedules for a specific Exam.
     * GET /api/exam-schedules?exam_id=X[&grade_level_id=Y]
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', ExamSchedule::class); // Policy

        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|integer|exists:exams,id',
            'grade_level_id' => 'sometimes|nullable|integer|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'معرف دورة الامتحان مطلوب', 'errors' => $validator->errors()], 422);
        }

        $query = ExamSchedule::with([
            'subject:id,name,code', // Select specific fields
            'gradeLevel:id,name',
            'classroom:id,name',
            'teacher:id,name' // Invigilator
        ])->where('exam_id', $request->input('exam_id'));

        if ($request->filled('grade_level_id')) {
             $query->where('grade_level_id', $request->input('grade_level_id'));
        }

        $schedules = $query->orderBy('exam_date')->orderBy('start_time')->get();

        return ExamScheduleResource::collection($schedules);
    }

    /**
     * Store a newly created exam schedule entry.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', ExamSchedule::class); // Policy

        $exam = Exam::find($request->input('exam_id')); // For context like school_id and date range
        if (!$exam) return response()->json(['message' => 'دورة الامتحان المحددة غير موجودة'], 404);
        $schoolId = $exam->school_id;

        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|integer|exists:exams,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'grade_level_id' => ['required', 'integer', Rule::exists('grade_levels', 'id')],
            'classroom_id' => ['nullable', 'integer', Rule::exists('classrooms', 'id')->where('school_id', $schoolId)],
            'teacher_id' => ['nullable', 'integer', Rule::exists('users', 'id')], // Or teachers table
            'exam_date' => ['required', 'date_format:Y-m-d',
                'after_or_equal:' . $exam->start_date->format('Y-m-d'),
                'before_or_equal:' . $exam->end_date->format('Y-m-d')
            ],
            'start_time' => 'required|date_format:H:i:s,H:i', // Allow HH:MM or HH:MM:SS
            'end_time' => 'required|date_format:H:i:s,H:i|after:start_time',
            'max_marks' => 'required|numeric|min:0',
            'pass_marks' => 'nullable|numeric|min:0|lte:max_marks',
            // Optional unique check:
            // Rule::unique('exam_schedules')->where(fn ($query) => $query->where('exam_id', $request->exam_id)->where('subject_id', $request->subject_id)->where('grade_level_id', $request->grade_level_id)),
        ]);

        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);

        $validatedData = $validator->validated();
        // Ensure time is stored consistently, e.g., with seconds
        $validatedData['start_time'] = Carbon::parse($validatedData['start_time'])->format('H:i:s');
        $validatedData['end_time'] = Carbon::parse($validatedData['end_time'])->format('H:i:s');


        $schedule = ExamSchedule::create($validatedData);

        return new ExamScheduleResource($schedule->load(['subject', 'gradeLevel', 'classroom', 'teacher']));
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamSchedule $examSchedule)
    {
        // $this->authorize('view', $examSchedule); // Policy
        return new ExamScheduleResource($examSchedule->load(['exam','subject', 'gradeLevel', 'classroom', 'teacher']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSchedule $examSchedule)
    {
        // $this->authorize('update', $examSchedule); // Policy

        $exam = $examSchedule->exam;
        $schoolId = $exam->school_id;

        $validator = Validator::make($request->all(), [
            // Typically, exam_id, subject_id, grade_level_id are not changed on update
            'classroom_id' => ['nullable', 'integer', Rule::exists('classrooms', 'id')->where('school_id', $schoolId)],
            'teacher_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'exam_date' => ['sometimes','required', 'date_format:Y-m-d',
                'after_or_equal:' . $exam->start_date->format('Y-m-d'),
                'before_or_equal:' . $exam->end_date->format('Y-m-d')
            ],
            'start_time' => 'sometimes|required|date_format:H:i:s,H:i',
            'end_time' => ['sometimes','required','date_format:H:i:s,H:i','after:' . ($request->input('start_time', $examSchedule->start_time))],
            'max_marks' => 'sometimes|required|numeric|min:0',
            'pass_marks' => ['nullable','numeric','min:0', 'lte:' . ($request->input('max_marks', $examSchedule->max_marks))],
        ]);

        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);

        $validatedData = $validator->validated();
        if(isset($validatedData['start_time'])) $validatedData['start_time'] = Carbon::parse($validatedData['start_time'])->format('H:i:s');
        if(isset($validatedData['end_time'])) $validatedData['end_time'] = Carbon::parse($validatedData['end_time'])->format('H:i:s');

        $examSchedule->update($validatedData);

        return new ExamScheduleResource($examSchedule->fresh()->load(['subject', 'gradeLevel', 'classroom', 'teacher']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamSchedule $examSchedule)
    {
        // $this->authorize('delete', $examSchedule); // Policy

        if ($examSchedule->results()->exists()) { // Check if results are entered
            return response()->json(['message' => 'لا يمكن حذف الموعد لوجود نتائج مسجلة له.'], 409); // Conflict
        }
        $examSchedule->delete();
        return response()->json(['message' => 'تم حذف موعد الامتحان بنجاح.'], 200);
    }

    /**
     * Quickly add all subjects for a given grade level to an exam period.
     * POST /api/exams/{exam}/quick-add-schedules
     */
    public function quickAddSchedulesForGrade(Request $request, Exam $exam)
    {
        // $this->authorize('create', ExamSchedule::class); // Policy

        $validator = Validator::make($request->all(), [
            'grade_level_id' => ['required', 'integer', Rule::exists('school_grade_levels', 'grade_level_id')->where('school_id', $exam->school_id)],
            'active_academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', $exam->school_id)],
            'default_start_time' => 'sometimes|required|date_format:H:i',
            'default_end_time' => 'sometimes|required|date_format:H:i|after:default_start_time',
            'default_max_marks' => 'sometimes|required|numeric|min:0',
            'default_pass_marks' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);

        $gradeLevelId = $request->input('grade_level_id');
        $targetAcademicYearId = $request->input('active_academic_year_id');
        $defaultStartTime = Carbon::parse($request->input('default_start_time', '09:00'))->format('H:i:s');
        $defaultEndTime = Carbon::parse($request->input('default_end_time', '11:00'))->format('H:i:s');
        $defaultMaxMarks = $request->input('default_max_marks', 100);
        $defaultPassMarks = $request->input('default_pass_marks', ($defaultMaxMarks / 2));


        $subjectsForGrade = AcademicYearSubject::where('school_id', $exam->school_id)
            ->where('academic_year_id', $targetAcademicYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->pluck('subject_id');

        if ($subjectsForGrade->isEmpty()) {
            return response()->json(['message' => 'لا توجد مواد معينة لهذه المرحلة الدراسية في العام الدراسي المحدد.'], 404);
        }

        $schedulesToCreate = [];
        $existingSchedulesCount = 0;
        $defaultExamDate = Carbon::parse($exam->start_date)->format('Y-m-d'); // Default to first day of exam period

        foreach ($subjectsForGrade as $subjectId) {
            $exists = ExamSchedule::where('exam_id', $exam->id)
                ->where('subject_id', $subjectId)
                ->where('grade_level_id', $gradeLevelId)
                ->exists();
            if ($exists) { $existingSchedulesCount++; continue; }

            $schedulesToCreate[] = [
                'exam_id' => $exam->id, 'subject_id' => $subjectId, 'grade_level_id' => $gradeLevelId,
                'classroom_id' => null, 'teacher_id' => null, 'exam_date' => $defaultExamDate,
                'start_time' => $defaultStartTime, 'end_time' => $defaultEndTime,
                'max_marks' => $defaultMaxMarks, 'pass_marks' => $defaultPassMarks,
                'created_at' => now(), 'updated_at' => now(),
            ];
        }

        if (empty($schedulesToCreate) && $existingSchedulesCount > 0) return response()->json(['message' => 'جميع المواد لهذه المرحلة مضافة بالفعل لجدول الامتحان.'], 200);
        if (empty($schedulesToCreate)) return response()->json(['message' => 'لم يتم العثور على مواد جديدة لإضافتها.'], 404);

        DB::transaction(function () use ($schedulesToCreate) { ExamSchedule::insert($schedulesToCreate); });

        $message = 'تمت إضافة ' . count($schedulesToCreate) . ' مادة لجدول الامتحان بنجاح.';
        if($existingSchedulesCount > 0) $message .= ' تم تجاهل ' . $existingSchedulesCount . ' مادة لوجودها مسبقاً.';
        return response()->json(['message' => $message, 'count' => count($schedulesToCreate)], 201);
    }
}