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
            'classrooms_count'=>$this->whenCounted('classrooms'),
            'principal_name' => $this->principal_name,
            'establishment_date' => $this->establishment_date, // Formatted by $casts
            'logo_path' => $this->logo, // Raw path
            'logo_url' => $logoUrl, // Full URL for display
            'annual_fees' => $this->annual_fees, // Annual fees amount
            'user_id' => $this->user_id, // Manager/User ID
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'username' => $this->user->username,
                    'email' => $this->user->email,
                ];
            }),
            // 'is_active' => $this->is_active, // Uncomment if added later
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}