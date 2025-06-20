<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exam_id' => $this->exam_id,
            'subject_id' => $this->subject_id,
            'grade_level_id' => $this->grade_level_id,
            'classroom_id' => $this->classroom_id,
            'teacher_id' => $this->teacher_id,
            'exam_date' => $this->exam_date, // Formatted by $casts in model
            'start_time' => $this->start_time, // String 'HH:MM:SS'
            'end_time' => $this->end_time,     // String 'HH:MM:SS'
            'max_marks' => $this->max_marks,   // Formatted by $casts in model
            'pass_marks' => $this->pass_marks, // Formatted by $casts in model

            // Conditionally load relationships (controller should use ->with([...]))
            'exam' => new ExamResource($this->whenLoaded('exam')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'grade_level' => new GradeLevelResource($this->whenLoaded('gradeLevel')),
            'classroom' => new ClassroomResource($this->whenLoaded('classroom')),
            'teacher' => new UserResource($this->whenLoaded('teacher')), // Assuming invigilator is a User
            // 'results_count' => $this->whenCounted('results'), // If you load count of results


            //null safe
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}