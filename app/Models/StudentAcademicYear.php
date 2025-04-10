<?php
// app/Models/StudentAcademicYear.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade_level_id',
        'classroom_id', // Nullable
        'status',
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
}