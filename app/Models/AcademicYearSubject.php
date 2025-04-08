<?php
// app/Models/AcademicYearSubject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicYearSubject extends Model
{
    use HasFactory;

    // Define table name if it doesn't follow convention (it does here)
    // protected $table = 'academic_year_subjects';

    protected $fillable = [
        'academic_year_id',
        'grade_level_id',
        'subject_id',
        'teacher_id',
    ];

    // No need for casts unless you add custom fields

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        // Since teacher_id is nullable
        return $this->belongsTo(Teacher::class);
    }
}