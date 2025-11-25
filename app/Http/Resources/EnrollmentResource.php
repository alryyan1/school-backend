<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'school_id' => $this->school_id,
            'academic_year' => $this->academic_year,
            'grade_level_id' => $this->grade_level_id,
            'classroom_id' => $this->classroom_id,
            'fees' => $this->fees,
            'discount' => $this->discount,
            
            'status' => $this->status,
            'enrollment_type' => $this->enrollment_type,
            
            // Deportation fields
            'deportation' => $this->deportation ?? false,
            'deportation_type' => $this->deportation_type,
            'deportation_path_id' => $this->deportation_path_id,
            'nearest_station' => $this->nearest_station,
            
            // Load relationships in controller
            'student' => new StudentResource($this->whenLoaded('student')),
            'grade_level' => new GradeLevelResource($this->whenLoaded('gradeLevel')),
            'classroom' => new ClassroomResource($this->whenLoaded('classroom')),
            'school' => new SchoolResource($this->whenLoaded('school')),
            'deportation_path' => new DeportationPathResource($this->whenLoaded('deportationPath')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
