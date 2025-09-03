<?php
// app/Models/Classroom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // If linking students

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $grade_level_id
 * @property int|null $teacher_id
 * @property int $capacity
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GradeLevel $gradeLevel
 * @property-read \App\Models\Teacher|null $homeroomTeacher
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom query()
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereGradeLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classroom whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade_level_id',
        'teacher_id', // Homeroom teacher (nullable)
        'capacity',
        'school_id',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Get the grade level this classroom belongs to.
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * Get the homeroom teacher assigned to this classroom (optional).
     */
    public function homeroomTeacher(): BelongsTo
    {
        // teacher_id is the default foreign key name convention
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Get the school this classroom belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
 // --- ADD THIS RELATIONSHIP ---
    /**
     * Get the student enrollment records associated with this classroom.
     */
    public function enrollments(): HasMany
    {
        // A classroom can have many student enrollment records assigned to it via 'classroom_id'
        return $this->hasMany(Enrollment::class);
    }
    /**
     * Get the students assigned to this classroom (requires student model update).
     * Example: Assuming students table has a nullable classroom_id
     */
    // public function students(): HasMany
    // {
    //     return $this->hasMany(Student::class);
    // }
}