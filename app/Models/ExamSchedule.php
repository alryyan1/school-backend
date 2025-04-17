<?php
// app/Models/ExamSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'grade_level_id',
        'classroom_id',
        'teacher_id', // Invigilator
        'exam_date',
        'start_time',
        'end_time',
        'max_marks',
        'pass_marks',
    ];

    protected $casts = [
        'exam_date' => 'date:Y-m-d',
        // Times are often fine as strings, but casting can help
        // 'start_time' => 'datetime:H:i', // Or just use string
        // 'end_time' => 'datetime:H:i',   // Or just use string
        'max_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
    ];

    // Relationships
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function gradeLevel(): BelongsTo { return $this->belongsTo(GradeLevel::class); }
    public function classroom(): BelongsTo { return $this->belongsTo(Classroom::class); } // Nullable
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }   // Nullable (Invigilator)

    // Add relationship to results later if needed
    // public function results() { return $this->hasMany(ExamResult::class); }
}