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
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'reference_number' => $this->reference_number,
            'payment_method' => $this->payment_method,
            'metadata' => $this->metadata,
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
