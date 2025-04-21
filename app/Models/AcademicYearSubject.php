<?php
// app/Models/AcademicYearSubject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $academic_year_id
 * @property int $grade_level_id
 * @property int $subject_id
 * @property int|null $teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\GradeLevel $gradeLevel
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereGradeLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicYearSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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