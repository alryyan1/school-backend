<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Construct the photo URL if a photo exists
        $photoUrl = $this->photo ? url(Storage::url($this->photo)) : null;
        // Ensure Storage::url() points to your linked public storage path correctly

        return [
            'id' => $this->id,
            'national_id' => $this->national_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date, // Will be formatted by $casts
            'qualification' => $this->qualification,
            'hire_date' => $this->hire_date, // Will be formatted by $casts
            'address' => $this->address,
            'photo_path' => $this->photo, // Send raw path for potential internal use
            'photo_url' => $photoUrl, // Send full URL for display
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toIso8601String(), // Standard format
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}