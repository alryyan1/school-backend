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
            // Mobile app expects 'name' key
            'name' => $this->student_name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth, // Relies on $casts['date:Y-m-d'] in Model
            'gender' => $this->gender, // Assumes direct output is fine
            'goverment_id' => $this->goverment_id,
            'wished_school' => $this->wished_school,
            'wished_school_details' => $this->whenLoaded('wishedSchool', function () {
                return [
                    'id' => $this->wishedSchool->id,
                    'name' => $this->wishedSchool->name,
                    'code' => $this->wishedSchool->code,
                ];
            }),
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
            'aproove_date' => $this->aproove_date, // Relies on $casts['datetime'] in Model
            'approved_by_user_id' => $this->approved_by_user, // Send the ID
            'approved_by_user' => $this->whenLoaded('approvedByUser', function () {
                return [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                ];
            }),
            'message_sent' => $this->message_sent, // Relies on $casts['boolean'] in Model

            // Timestamps
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,

            // Add enrollments with nested data
            'enrollments' => $this->whenLoaded('enrollments', function () {
                return $this->enrollments->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'status' => $enrollment->status,
                        'enrollment_type' => $enrollment->enrollment_type,
                        'fees' => $enrollment->fees,
                        'discount' => $enrollment->discount,
                        'school' => $enrollment->school ? [
                            'id' => $enrollment->school->id,
                            'name' => $enrollment->school->name,
                        ] : null,
                        'grade_level' => $enrollment->gradeLevel ? [
                            'id' => $enrollment->gradeLevel->id,
                            'name' => $enrollment->gradeLevel->name,
                        ] : null,
                        'academic_year' => $enrollment->academicYear ? [
                            'id' => $enrollment->academicYear->id,
                            'name' => $enrollment->academicYear->name,
                            'start_date' => optional($enrollment->academicYear->start_date)->format('Y-m-d'),
                            'end_date' => optional($enrollment->academicYear->end_date)->format('Y-m-d'),
                        ] : null,
                        'classroom' => $enrollment->classroom ? [
                            'id' => $enrollment->classroom->id,
                            'name' => $enrollment->classroom->name,
                        ] : null,
                        // Aggregated fees info for this enrollment
                        'total_amount_required' => (float) ($enrollment->feeInstallments?->sum('amount_due') ?? 0),
                        'total_amount_paid' => (float) ($enrollment->feeInstallments?->sum('amount_paid') ?? 0),
                        'created_at' => $enrollment->created_at,
                        'updated_at' => $enrollment->updated_at,
                    ];
                });
            }),

            // Shortcut fields for the latest academic year totals
            'latest_academic_year_totals' => $this->whenLoaded('enrollments', function () {
                $latest = $this->enrollments->sortByDesc(function ($enrollment) {
                    // Prefer academic year end_date, fallback to created_at
                    $end = optional($enrollment->academicYear?->end_date);
                    return $end?->timestamp ?? $enrollment->created_at?->timestamp ?? 0;
                })->first();

                if (!$latest) {
                    return null;
                }

                return [
                    'student_academic_year_id' => $latest->id,
                    'enrollment_type' => $latest->enrollment_type,
                    'academic_year' => [
                        'id' => $latest->academicYear?->id,
                        'name' => $latest->academicYear?->name,
                        'start_date' => optional($latest->academicYear?->start_date)->format('Y-m-d'),
                        'end_date' => optional($latest->academicYear?->end_date)->format('Y-m-d'),
                    ],
                    'total_amount_required' => (float) ($latest->feeInstallments?->sum('amount_due') ?? 0),
                    'total_amount_paid' => (float) ($latest->feeInstallments?->sum('amount_paid') ?? 0),
                ];
            }),
        ];
    }
}