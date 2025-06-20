<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,

            //null safe
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];

        // Conditionally add pivot data if loaded via relationship
        // Note: $this->whenPivotLoaded is safer than checking isset($this->pivot)
        $data['assignment_details'] = $this->whenPivotLoaded('school_grade_levels', function () {
            return [
                'basic_fees' => $this->pivot->basic_fees,
                // Include other pivot fields like created_at/updated_at if needed
                 'assigned_at' => $this->pivot->created_at?->toIso8601String(),
                 'fee_last_updated_at' => $this->pivot->updated_at?->toIso8601String(),
            ];
        });
     

        return $data;
    }
}