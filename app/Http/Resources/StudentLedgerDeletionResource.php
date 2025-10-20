<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentLedgerDeletionResource extends JsonResource
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
            'ledger_entry_id' => $this->ledger_entry_id,
            'enrollment_id' => $this->enrollment_id,
            'student_id' => $this->student_id,
            'transaction_type' => $this->transaction_type,
            'description' => $this->description,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date?->format('Y-m-d'),
            'balance_before' => $this->balance_before,
            'balance_after' => $this->balance_after,
            'reference_number' => $this->reference_number,
            'payment_method' => $this->payment_method,
            'metadata' => $this->metadata,
            'original_created_by' => $this->whenLoaded('originalCreator', function () {
                return [
                    'id' => $this->originalCreator->id,
                    'name' => $this->originalCreator->name,
                ];
            }),
            'original_created_at' => $this->original_created_at?->format('Y-m-d H:i:s'),
            'deleted_by' => $this->whenLoaded('deletedBy', function () {
                return [
                    'id' => $this->deletedBy->id,
                    'name' => $this->deletedBy->name,
                ];
            }),
            'deletion_reason' => $this->deletion_reason,
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'enrollment' => $this->whenLoaded('enrollment', function () {
                return [
                    'id' => $this->enrollment->id,
                    'student' => [
                        'id' => $this->enrollment->student->id,
                        'student_name' => $this->enrollment->student->student_name,
                    ],
                    'school' => [
                        'id' => $this->enrollment->school->id ?? null,
                        'name' => $this->enrollment->school->name ?? null,
                    ],
                    'grade_level' => [
                        'id' => $this->enrollment->gradeLevel->id ?? null,
                        'name' => $this->enrollment->gradeLevel->name ?? null,
                    ],
                    'classroom' => [
                        'id' => $this->enrollment->classroom->id ?? null,
                        'name' => $this->enrollment->classroom->name ?? null,
                    ],
                ];
            }),
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'student_name' => $this->student->student_name,
                    'phone_number' => $this->student->phone_number,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
