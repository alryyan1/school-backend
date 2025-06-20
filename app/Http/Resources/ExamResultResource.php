<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
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
            'student_academic_year_id' => $this->student_academic_year_id,
            'exam_schedule_id' => $this->exam_schedule_id,
            'marks_obtained' => $this->marks_obtained, // Already cast to decimal:2
            'grade_letter' => $this->grade_letter,
            'is_absent' => $this->is_absent, // Already cast to boolean
            'remarks' => $this->notes, // Corrected field name based on migration ('notes' vs 'remarks') -> use 'remarks'

            // For display in frontend, load these relations in the controller
            'student_enrollment' => new StudentAcademicYearResource($this->whenLoaded('studentAcademicYear')),
            'exam_schedule' => new ExamScheduleResource($this->whenLoaded('examSchedule')),
            // 'entered_by' => new UserResource($this->whenLoaded('enteredBy')), // Optional

            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}