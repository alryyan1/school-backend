<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentLedgerResource extends JsonResource
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
            'enrollment_id' => $this->enrollment_id,
            'student_id' => $this->student_id,
            'transaction_type' => $this->transaction_type,
            'description' => $this->description,
            'amount' => $this->amount,
            'balance_after' => $this->balance_after,
            'transaction_date' => $this->transaction_date ? $this->transaction_date->format('Y-m-d') : null,
            'reference_number' => $this->reference_number,
            'payment_method' => $this->payment_method,
            'metadata' => $this->metadata,
            'created_by' => $this->whenLoaded('createdBy', function () {
                if (!$this->createdBy) {
                    return null;
                }
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                ];
            }),
            'enrollment' => $this->whenLoaded('enrollment', function () {
                if (!$this->enrollment) {
                    return null;
                }
                return [
                    'id' => $this->enrollment->id,
                    'student' => $this->enrollment->relationLoaded('student') && $this->enrollment->student ? [
                        'id' => $this->enrollment->student->id,
                        'student_name' => $this->enrollment->student->student_name,
                    ] : null,
                    'school' => $this->enrollment->relationLoaded('school') && $this->enrollment->school ? [
                        'id' => $this->enrollment->school->id,
                        'name' => $this->enrollment->school->name,
                    ] : null,
                    'grade_level' => $this->enrollment->relationLoaded('gradeLevel') && $this->enrollment->gradeLevel ? [
                        'id' => $this->enrollment->gradeLevel->id,
                        'name' => $this->enrollment->gradeLevel->name,
                    ] : null,
                    'classroom' => $this->enrollment->relationLoaded('classroom') && $this->enrollment->classroom ? [
                        'id' => $this->enrollment->classroom->id,
                        'name' => $this->enrollment->classroom->name,
                    ] : null,
                ];
            }),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
