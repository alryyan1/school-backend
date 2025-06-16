<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomResource extends JsonResource
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
            'name' => $this->name,
            'capacity' => $this->capacity,
            'grade_level_id' => $this->grade_level_id,
            'teacher_id' => $this->teacher_id, // Homeroom teacher ID
            'school_id' => $this->school_id,
            // Include nested resources for frontend display (Load these in Controller)
            'grade_level' => new GradeLevelResource($this->whenLoaded('gradeLevel')),
            'homeroom_teacher' => new TeacherResource($this->whenLoaded('homeroomTeacher')),
            'school' => new SchoolResource($this->whenLoaded('school')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'students_count' => $this->whenCounted('studentAssignments', $this->students_count),
            'student_enrollments' => StudentAcademicYearResource::collection($this->whenLoaded('enrollments')), // <-- Add this

        ];
    }
}