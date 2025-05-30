<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class SchoolResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Construct the logo URL if a logo exists
        $logoUrl = $this->logo ? Storage::url($this->logo) : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'classrooms_count'=>$this->whenCounted('classrooms'),
            'principal_name' => $this->principal_name,
            'establishment_date' => $this->establishment_date, // Formatted by $casts
            'logo_path' => $this->logo, // Raw path
            'logo_url' => $logoUrl, // Full URL for display
            // 'is_active' => $this->is_active, // Uncomment if added later
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}