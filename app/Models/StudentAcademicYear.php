<?php
// app/Models/StudentAcademicYear.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property int $student_id
 * @property int $school_id
 * @property int $academic_year_id
 * @property int $grade_level_id
 * @property int|null $classroom_id
 * @property string $status
 * @property int $fees
 * @property int $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Classroom|null $classroom
 * @property-read \App\Models\GradeLevel $gradeLevel
 * @property-read \App\Models\School $school
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\StudentTransportAssignment|null $transportAssignment
 * @method static \Database\Factories\StudentAcademicYearFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereGradeLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicYear whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentAcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade_level_id',
        'classroom_id', // Nullable
        'status',
         'enrollment_type',
        'fees',
        'discount',
        'school_id', // <-- Add school_id
    ];

    // Optional: Cast status if you create a PHP Enum later
    // protected $casts = [ 'status' => EnrollmentStatusEnum::class ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
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
    // --- NEW RELATIONSHIP ---
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
    public function transportAssignment(): HasOne // A student has one assignment per year
    {
        return $this->hasOne(StudentTransportAssignment::class);
    }
    public function payments():HasMany {
        return $this->hasMany(StudentFeePayment::class);
    }
    // --- Add this relationship ---
    public function feeInstallments(): HasMany {
        return $this->hasMany(FeeInstallment::class)->orderBy('due_date'); // Order by due date
    }
}
