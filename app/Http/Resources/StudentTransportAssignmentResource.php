<?php // app/Http/Resources/StudentTransportAssignmentResource.php
namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
class StudentTransportAssignmentResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id, // The assignment ID itself
            'student_academic_year_id' => $this->student_academic_year_id,
            'transport_route_id' => $this->transport_route_id,
            'pickup_point' => $this->pickup_point,
            'dropoff_point' => $this->dropoff_point,
            // Load necessary details in controller
            'student_enrollment' => new EnrollmentResource($this->whenLoaded('studentAcademicYear')),
            'transport_route' => new TransportRouteResource($this->whenLoaded('transportRoute')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}