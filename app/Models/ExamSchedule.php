<?php
// app/Models/ExamSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',          // Link to the overall Exam period
        'subject_id',       // Which subject
        'grade_level_id',   // Which grade level is taking this specific scheduled exam
        'classroom_id',     // Optional: Specific room
        'teacher_id',       // Optional: Invigilator (User ID)
        'exam_date',        // Date of this specific exam
        'start_time',       // Start time (e.g., '09:00:00')
        'end_time',         // End time (e.g., '11:00:00')
        'max_marks',        // Maximum marks for this exam instance
        'pass_marks',       // Passing marks for this exam instance
    ];

    protected $casts = [
        'exam_date' => 'date:Y-m-d', // Ensures it's treated as a date object and formatted
        // Times are often stored as strings (TIME type in DB), casting is optional
        // 'start_time' => 'datetime:H:i:s', // If you want Carbon instances for time
        // 'end_time' => 'datetime:H:i:s',
        'max_marks' => 'decimal:2',    // Store with 2 decimal places
        'pass_marks' => 'decimal:2',   // Store with 2 decimal places
    ];

    /**
     * The Exam period this schedule belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * The Subject for this scheduled exam.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * The GradeLevel for this scheduled exam.
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * The Classroom where this exam is scheduled (optional).
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class); // classroom_id is nullable
    }

    /**
     * The Teacher assigned as invigilator (optional).
     * Assumes 'teacher_id' on exam_schedules links to 'id' on 'users' table if teachers are users.
     * Or, if you have a separate Teachers model, link to that.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id'); // Or Teacher::class if you have a dedicated Teacher model
    }

    /**
     * Get the results associated with this specific exam schedule.
     */
    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}