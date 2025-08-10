<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    // GradeLevel relationship
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
    // AcademicYear relationship
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    // Classroom relationship
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
