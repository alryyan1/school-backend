<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollMent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollMent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollMent query()
 * @mixin \Eloquent
 */
class EnrollMent extends Model
{


    protected $table = 'student_academic_years';
    // School relationship
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
    // GradeLevel relationship
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }
    // AcademicYear relationship
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
    // Classroom relationship
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
    // Fee installments for this enrollment (student academic year)
    public function feeInstallments(): HasMany
    {
        return $this->hasMany(FeeInstallment::class, 'student_academic_year_id');
    }
}
