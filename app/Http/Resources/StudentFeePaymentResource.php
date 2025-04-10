<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeePaymentResource extends JsonResource
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
            'amount' => $this->amount, // Cast in model handles formatting
            'payment_date' => $this->payment_date, // Cast in model handles formatting
            'notes' => $this->notes,
            // Optionally load related data if needed
            // 'enrollment' => new StudentAcademicYearResource($this->whenLoaded('studentAcademicYear')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}