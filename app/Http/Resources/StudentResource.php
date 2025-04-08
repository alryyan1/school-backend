<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage; // Import Storage facade for image URL generation

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Construct the full image URL if an image path exists
        $imageUrl = $this->image ? url(Storage::url($this->image)) : null;        // Ensure you have run `php artisan storage:link`
        // and your .env APP_URL is set correctly.

        return [
            // Core Student Info
            'id' => $this->id,
            'student_name' => $this->student_name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth, // Relies on $casts['date:Y-m-d'] in Model
            'gender' => $this->gender, // Assumes direct output is fine
            'goverment_id' => $this->goverment_id,
            'wished_level' => $this->wished_level, // Assumes direct output is fine
            'medical_condition' => $this->medical_condition,
            'referred_school' => $this->referred_school,
            'success_percentage' => $this->success_percentage,

            // Image Info
            'image_path' => $this->image, // The raw path stored in DB
            'image_url' => $imageUrl,    // The full URL for frontend display

            // Father Info
            'father_name' => $this->father_name,
            'father_job' => $this->father_job,
            'father_address' => $this->father_address,
            'father_phone' => $this->father_phone,
            'father_whatsapp' => $this->father_whatsapp,

            // Mother Info
            'mother_name' => $this->mother_name,
            'mother_job' => $this->mother_job,
            'mother_address' => $this->mother_address,
            'mother_phone' => $this->mother_phone,
            'mother_whatsapp' => $this->mother_whatsapp,

            // Other Parent Info
            'other_parent' => $this->other_parent,
            'relation_of_other_parent' => $this->relation_of_other_parent,
            'relation_job' => $this->relation_job,
            'relation_phone' => $this->relation_phone,
            'relation_whatsapp' => $this->relation_whatsapp,

            // Closest Person Info
            'closest_name' => $this->closest_name,
            'closest_phone' => $this->closest_phone,

            // Approval Info
            'approved' => $this->approved, // Relies on $casts['boolean'] in Model
            // Correct spelling 'aproove_date' based on your migration
            'approve_date' => $this->aproove_date, // Relies on $casts['datetime'] in Model
            'approved_by_user_id' => $this->approved_by_user, // Send the ID
            'message_sent' => $this->message_sent, // Relies on $casts['boolean'] in Model

            // Optional: Include nested approver details if relationship is loaded
            // Requires 'approver' relationship in Student model and UserResource
            // 'approver' => new UserResource($this->whenLoaded('approver')),

            // Timestamps
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}