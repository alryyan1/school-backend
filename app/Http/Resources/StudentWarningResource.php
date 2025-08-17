<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentWarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_academic_year_id' => $this->student_academic_year_id,
            'issued_by_user_id' => $this->issued_by_user_id,
            'severity' => $this->severity,
            'reason' => $this->reason,
            'issued_at' => optional($this->issued_at)->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}


