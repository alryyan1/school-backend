<?php
// app/Models/ExamSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $exam_id
 * @property int $subject_id
 * @property int $grade_level_id
 * @property int|null $classroom_id
 * @property int|null $teacher_id
 * @property \Illuminate\Support\Carbon $exam_date
 * @property string $start_time
 * @property string $end_time
 * @property string $max_marks
 * @property string|null $pass_marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Classroom|null $classroom
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\GradeLevel $gradeLevel
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Database\Factories\ExamScheduleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereExamDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereGradeLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereMaxMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule wherePassMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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