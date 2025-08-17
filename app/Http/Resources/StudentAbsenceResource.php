<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAbsenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_academic_year_id' => $this->student_academic_year_id,
            'absent_date' => optional($this->absent_date)->format('Y-m-d'),
            'reason' => $this->reason,
            'excused' => (bool) $this->excused,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}


