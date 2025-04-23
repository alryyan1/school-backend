<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAcademicYearResource extends JsonResource
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
            'student_id' => $this->student_id,
            'academic_year_id' => $this->academic_year_id,
            'grade_level_id' => $this->grade_level_id,
            'classroom_id' => $this->classroom_id,
            'fees' => $this->fees,
            'school_id' => $this->school_id, // <-- Add school_id
            'school'=> new SchoolResource($this->whenLoaded('school')),
            'status' => $this->status,
            // Load relationships in controller
            'student' => new StudentResource($this->whenLoaded('student')),
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'grade_level' => new GradeLevelResource($this->whenLoaded('gradeLevel')),
            'classroom' => new ClassroomResource($this->whenLoaded('classroom')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

        ];
    }
}