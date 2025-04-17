<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\Exam; // Import other models
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\ExamScheduleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExamScheduleController extends Controller
{
    /**
     * Display a listing of schedules for a specific Exam.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', ExamSchedule::class);

        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|integer|exists:exams,id',
            'grade_level_id' => 'sometimes|required|integer|exists:grade_levels,id',
            // Add other filters if needed (classroom, teacher)
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'معرف دورة الامتحان مطلوب', 'errors' => $validator->errors()], 422);
        }

        $query = ExamSchedule::with(['subject', 'gradeLevel', 'classroom', 'teacher'])
                              ->where('exam_id', $request->input('exam_id'));

        if ($request->filled('grade_level_id')) {
             $query->where('grade_level_id', $request->input('grade_level_id'));
        }
        // Add other filters here

        $schedules = $query->orderBy('exam_date')->orderBy('start_time')->get();

        return ExamScheduleResource::collection($schedules);
    }

    /**
     * Store a newly created exam schedule entry.
     */
    public function store(Request $request)
    {
         // $this->authorize('create', ExamSchedule::class);

         // Need to get school context from the Exam to validate related IDs
         $exam = Exam::find($request->input('exam_id'));
         if (!$exam) {
             return response()->json(['message' => 'دورة الامتحان المحددة غير موجودة'], 404);
         }
         $schoolId = $exam->school_id;

         $validator = Validator::make($request->all(), [
            'exam_id' => 'required|integer|exists:exams,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'grade_level_id' => ['required', 'integer', Rule::exists('grade_levels', 'id')],
            'classroom_id' => ['nullable', 'integer', Rule::exists('classrooms', 'id')->where('school_id', $schoolId)], // Classroom must be in same school
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')], // Teacher must exist
            'exam_date' => ['required', 'date_format:Y-m-d',
                // Ensure date is within the parent Exam's start/end date
                Rule::when($exam, function () use ($exam) {
                    return 'after_or_equal:' . $exam->start_date->format('Y-m-d');
                }),
                 Rule::when($exam, function () use ($exam) {
                    return 'before_or_equal:' . $exam->end_date->format('Y-m-d');
                })
            ],
            'start_time' => 'required|date_format:H:i:s', // Validate HH:MM or H:i:s
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'max_marks' => 'required|numeric|min:0',
            'pass_marks' => 'nullable|numeric|min:0|lte:max_marks', // Pass marks <= Max marks
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        // **Add check:** Ensure Subject is relevant for the GradeLevel (requires curriculum data - skip for now if too complex)

        $schedule = ExamSchedule::create($validator->validated());

        return new ExamScheduleResource($schedule->load(['subject', 'gradeLevel', 'classroom', 'teacher']));
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamSchedule $examSchedule) // Route model binding
    {
        // $this->authorize('view', $examSchedule);
        return new ExamScheduleResource($examSchedule->load(['exam','subject', 'gradeLevel', 'classroom', 'teacher']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSchedule $examSchedule)
    {
         // $this->authorize('update', $examSchedule);

         $exam = $examSchedule->exam; // Get related exam for date validation
         $schoolId = $exam->school_id;

          $validator = Validator::make($request->all(), [
              // Don't allow changing exam_id, subject_id, grade_level_id easily?
              'classroom_id' => ['nullable', 'integer', Rule::exists('classrooms', 'id')->where('school_id', $schoolId)],
              'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
              'exam_date' => ['sometimes','required', 'date_format:Y-m-d',
                  Rule::when($exam, fn() => 'after_or_equal:' . $exam->start_date->format('Y-m-d')),
                  Rule::when($exam, fn() => 'before_or_equal:' . $exam->end_date->format('Y-m-d'))
              ],
              'start_time' => 'sometimes|required|date_format:H:i',
              'end_time' => ['sometimes','required','date_format:H:i','after:' . ($request->input('start_time', $examSchedule->start_time))],
              'max_marks' => 'sometimes|required|numeric|min:0',
              'pass_marks' => 'nullable|numeric|min:0|lte:' . ($request->input('max_marks', $examSchedule->max_marks)),
          ]);


         if ($validator->fails()) {
             return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
         }

         $examSchedule->update($validator->validated());

         return new ExamScheduleResource($examSchedule->fresh()->load(['subject', 'gradeLevel', 'classroom', 'teacher']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamSchedule $examSchedule)
    {
        // $this->authorize('delete', $examSchedule);

        // ** CHECK FOR RELATIONSHIPS (e.g., results) **
        // if ($examSchedule->results()->exists()) {
        //     return response()->json(['message' => 'لا يمكن حذف الجدول لوجود نتائج مرتبطة به.'], 409);
        // }

        $examSchedule->delete();

        return response()->json(['message' => 'تم حذف جدول الامتحان بنجاح.'], 200);
    }
}