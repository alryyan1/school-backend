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
use App\Models\AcademicYear;
use App\Models\AcademicYearSubject;
use Carbon\Carbon;
use DB;
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
     * Quickly add all subjects for a given grade level to an exam period,
     * using the curriculum of a specific academic year.
     * POST /api/exams/{exam}/quick-add-schedules
     */
    public function quickAddSchedulesForGrade(Request $request, Exam $exam)
    {
        // $this->authorize('create', ExamSchedule::class);

        $validator = Validator::make($request->all(), [
            'grade_level_id' => ['required', 'integer', Rule::exists('grade_levels', 'id')],
            // Ensure the grade level is actually part of the exam's school
            'grade_level_id' => Rule::exists('school_grade_levels', 'grade_level_id')->where('school_id', $exam->school_id),

            // --- Expect active_academic_year_id from frontend ---
            'active_academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', $exam->school_id)],
            // ------------------------------------------------------

            // Default values for the schedule items (optional)
            'default_start_time' => 'sometimes|required|date_format:H:i',
            'default_end_time' => 'sometimes|required|date_format:H:i|after:default_start_time',
            'default_max_marks' => 'sometimes|required|numeric|min:0',
            'default_pass_marks' => 'nullable|numeric|min:0|lte:default_max_marks',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $gradeLevelId = $request->input('grade_level_id');
        $targetAcademicYearId = $request->input('active_academic_year_id'); // <-- Use this ID

        $defaultStartTime = $request->input('default_start_time', '09:00:00');
        // ... other defaults ...

        // Get subjects assigned to this grade level IN THE SPECIFIED ACADEMIC YEAR for this school
        $subjectsForGrade = AcademicYearSubject::
            where('academic_year_id', $targetAcademicYearId) // <-- Use target year
            ->where('grade_level_id', $gradeLevelId)
            ->pluck('subject_id');

        if ($subjectsForGrade->isEmpty()) {
            return response()->json(['message' => 'لا توجد مواد معينة لهذه المرحلة الدراسية في العام الدراسي المحدد.'], 404);
        }

        // ... (rest of the logic for $schedulesToCreate, $existingSchedulesCount, DB::transaction, response message remains the same) ...
         $schedulesToCreate = [];
         $existingSchedulesCount = 0;
         $defaultExamDate = Carbon::parse($exam->start_date)->format('Y-m-d');
         $defaultMaxMarks = $request->input('default_max_marks', 100);
         $defaultPassMarks = $request->input('default_pass_marks', 50);


         foreach ($subjectsForGrade as $subjectId) {
             $exists = ExamSchedule::where('exam_id', $exam->id)
                 ->where('subject_id', $subjectId)
                 ->where('grade_level_id', $gradeLevelId)
                 ->exists();

             if ($exists) {
                 $existingSchedulesCount++;
                 continue;
             }

             $schedulesToCreate[] = [
                 'exam_id' => $exam->id,
                 'subject_id' => $subjectId,
                 'grade_level_id' => $gradeLevelId,
                 'classroom_id' => null,
                 'teacher_id' => null,
                 'exam_date' => $defaultExamDate,
                 'start_time' => $defaultStartTime,
                 'end_time' => $request->input('default_end_time', Carbon::parse($defaultStartTime)->addHours(2)->format('H:i:s')),
                 'max_marks' => $defaultMaxMarks,
                 'pass_marks' => $defaultPassMarks,
                 'created_at' => now(),
                 'updated_at' => now(),
             ];
         }

         if (empty($schedulesToCreate) && $existingSchedulesCount > 0) {
             return response()->json(['message' => 'جميع المواد لهذه المرحلة مضافة بالفعل لجدول الامتحان.'], 200);
         }
         if (empty($schedulesToCreate)) {
             return response()->json(['message' => 'لم يتم العثور على مواد جديدة لإضافتها.'], 404);
         }

         DB::transaction(function () use ($schedulesToCreate) {
             ExamSchedule::insert($schedulesToCreate);
         });

         $message = 'تمت إضافة ' . count($schedulesToCreate) . ' مادة لجدول الامتحان بنجاح.';
         if($existingSchedulesCount > 0) {
             $message .= ' تم تجاهل ' . $existingSchedulesCount . ' مادة لوجودها مسبقاً.';
         }

         return response()->json(['message' => $message, 'count' => count($schedulesToCreate)], 201);
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