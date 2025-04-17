<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exam_id' => $this->exam_id,
            'subject_id' => $this->subject_id,
            'grade_level_id' => $this->grade_level_id,
            'classroom_id' => $this->classroom_id,
            'teacher_id' => $this->teacher_id, // Invigilator ID
            'exam_date' => $this->exam_date, // Cast in model
            'start_time' => $this->start_time, // Usually string 'HH:MM:SS'
            'end_time' => $this->end_time,     // Usually string 'HH:MM:SS'
            'max_marks' => $this->max_marks,   // Cast in model
            'pass_marks' => $this->pass_marks, // Cast in model
            // Relationships (Load in Controller)
            'exam' => new ExamResource($this->whenLoaded('exam')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'grade_level' => new GradeLevelResource($this->whenLoaded('gradeLevel')),
            'classroom' => new ClassroomResource($this->whenLoaded('classroom')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')), // Invigilator details
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}