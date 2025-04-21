<?php // app/Models/TransportRoute.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $school_id
 * @property string $name
 * @property string|null $description
 * @property int|null $driver_id
 * @property string|null $fee_amount
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $driver
 * @property-read \App\Models\School $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentTransportAssignment> $studentAssignments
 * @property-read int|null $student_assignments_count
 * @method static \Database\Factories\TransportRouteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereFeeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRoute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TransportRoute extends Model {
    use HasFactory;
    protected $fillable = ['school_id', 'name', 'description', 'driver_id', 'fee_amount', 'is_active'];
    protected $casts = ['fee_amount' => 'decimal:2', 'is_active' => 'boolean'];

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function driver(): BelongsTo { return $this->belongsTo(User::class, 'driver_id'); } // Assuming driver is a User
    public function studentAssignments(): HasMany { return $this->hasMany(StudentTransportAssignment::class); }
}