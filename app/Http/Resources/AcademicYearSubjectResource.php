<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearSubjectResource extends JsonResource
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
            'academic_year_id' => $this->academic_year_id,
            'grade_level_id' => $this->grade_level_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            // Eager load relationships for display
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'grade_level' => new GradeLevelResource($this->whenLoaded('gradeLevel')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')), // TeacherResource might include photo_url etc.
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}