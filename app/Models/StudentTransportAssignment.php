<?php // app/Models/StudentTransportAssignment.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTransportAssignment extends Model {
    use HasFactory;
    protected $fillable = ['student_academic_year_id', 'transport_route_id', 'pickup_point', 'dropoff_point'];

    public function studentAcademicYear(): BelongsTo { return $this->belongsTo(StudentAcademicYear::class); }
    public function transportRoute(): BelongsTo { return $this->belongsTo(TransportRoute::class); }
}