<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Enrollment Model
 *
 * @property int $id
 * @property int $student_id
 * @property int $school_id
 * @property string $academic_year
 * @property int $grade_level_id
 * @property int|null $classroom_id
 * @property string $status
 * @property string|null $enrollment_type
 * @property int $fees
 * @property int $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Classroom|null $classroom
 * @property-read \App\Models\GradeLevel $gradeLevel
 * @property-read \App\Models\School $school
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\StudentTransportAssignment|null $transportAssignment
 * @method static \Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereAcademicYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereGradeLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EnrollMent extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    protected $fillable = [
        'student_id',
        'school_id',
        'academic_year',
        'grade_level_id',
        'classroom_id', // Nullable
        'status',
        'enrollment_type',
        'fees',
        'discount',
    ];

    // Optional: Cast status if you create a PHP Enum later
    // protected $casts = [ 'status' => EnrollmentStatusEnum::class ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function classroom(): BelongsTo
    {
        // Handles nullable classroom_id
        return $this->belongsTo(Classroom::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function transportAssignment(): HasOne // A student has one assignment per year
    {
        return $this->hasOne(StudentTransportAssignment::class, 'student_academic_year_id');
    }

    public function payments(): HasMany {
        return $this->hasMany(StudentFeePayment::class, 'student_academic_year_id');
    }

    public function feeInstallments(): HasMany {
        return $this->hasMany(FeeInstallment::class, 'enrollment_id', 'id')->orderBy('due_date'); // Order by due date
    }

        public function notes(): HasMany
        {
            return $this->hasMany(StudentNote::class);
        }
}
