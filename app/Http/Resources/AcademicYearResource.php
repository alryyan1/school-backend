<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearResource extends JsonResource
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
            'start_date' => $this->start_date, // Formatted by cast
            'end_date' => $this->end_date,   // Formatted by cast
            'is_current' => $this->is_current,
            'school_id' => $this->school_id,
            // Eager load school relationship in controller for better performance
            'school' => new SchoolResource($this->whenLoaded('school')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}