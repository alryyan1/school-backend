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
        $photoUrl = $this->photo ? url(Storage::url(path: $this->photo)) : null;
        // Ensure Storage::url() points to your linked public storage path correctly

        return [
            'id' => $this->id,
            'national_id' => $this->national_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            // Casts in the model ensure Y-m-d; explicitly cast to string or null for clarity
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'place_of_birth' => $this->place_of_birth,
            'nationality' => $this->nationality,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'marital_status' => $this->marital_status,
            'number_of_children' => $this->number_of_children,
            'children_in_school' => $this->children_in_school,
            'secondary_phone' => $this->secondary_phone,
            'whatsapp_number' => $this->whatsapp_number,
            'qualification' => $this->qualification,
            'highest_qualification' => $this->highest_qualification,
            'specialization' => $this->specialization,
            'academic_degree' => $this->academic_degree,
            'hire_date' => $this->hire_date ? $this->hire_date->format('Y-m-d') : null,
            'appointment_date' => $this->appointment_date ? $this->appointment_date->format('Y-m-d') : null,
            'years_of_teaching_experience' => $this->years_of_teaching_experience,
            'training_courses' => $this->training_courses,
            'address' => $this->address,
            'photo_path' => $this->photo, // Send raw path for potential internal use
            'photo_url' => $photoUrl, // Send full URL for display
            'academic_qualifications_doc_path' => $this->academic_qualifications_doc_path,
            'personal_id_doc_path' => $this->personal_id_doc_path,
            'cv_doc_path' => $this->cv_doc_path,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toIso8601String(), // Standard format
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}