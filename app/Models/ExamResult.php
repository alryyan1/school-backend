<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_academic_year_id',
        'exam_schedule_id',
        'marks_obtained',
        'grade_letter',
        'is_absent',
        'remarks',
        'entered_by_user_id', // User who entered the marks
        'updated_by_user_id', // User who last updated the marks
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2', // Store marks with 2 decimal places
        'is_absent' => 'boolean',
    ];

    /**
     * Get the student enrollment record this result belongs to.
     */
    public function studentAcademicYear(): BelongsTo
    {
        return $this->belongsTo(StudentAcademicYear::class, 'student_academic_year_id');
    }

    /**
     * Get the specific scheduled exam this result is for.
     */
    public function examSchedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id');
    }

    /**
     * Get the user who entered these marks.
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by_user_id');
    }

    /**
     * Get the user who last updated these marks.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}