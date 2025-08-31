<?php // app/Models/StudentTransportAssignment.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $student_academic_year_id
 * @property int $transport_route_id
 * @property string|null $pickup_point
 * @property string|null $dropoff_point
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EnrollMent $studentAcademicYear
 * @property-read \App\Models\TransportRoute $transportRoute
 * @method static \Database\Factories\StudentTransportAssignmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment whereDropoffPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment wherePickupPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment whereStudentAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment whereTransportRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentTransportAssignment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentTransportAssignment extends Model {
    use HasFactory;
    protected $fillable = ['student_academic_year_id', 'transport_route_id', 'pickup_point', 'dropoff_point'];

    public function studentAcademicYear(): BelongsTo { return $this->belongsTo(EnrollMent::class); }
    public function transportRoute(): BelongsTo { return $this->belongsTo(TransportRoute::class); }
}