<?php // app/Http/Resources/FeeInstallmentResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StudentResource;

class FeeInstallmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'title' => $this->title,
            'amount_due' => $this->amount_due,
            'amount_paid' => $this->amount_paid,
            'due_date' => $this->due_date, // Cast in model
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
      
            // Use 'student_enrollment' as key for clarity in JSON,
            // but load 'studentAcademicYear' relationship from the model
            'student' => new StudentResource($this->whenLoaded('student')),            // ---------------------------

            // Optional: You might already have payments loaded here if needed elsewhere
            // 'payments' => StudentFeePaymentResource::collection($this->whenLoaded('payments')),
            // Optional: Include related data if needed and loaded
            // 'student_enrollment' => new StudentAcademicYearResource($this->whenLoaded('studentAcademicYear')),
            // 'payments' => StudentFeePaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
