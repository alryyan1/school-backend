<?php
// app/Models/Classroom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // If linking students

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

    /**
     * Get the students assigned to this classroom (requires student model update).
     * Example: Assuming students table has a nullable classroom_id
     */
    // public function students(): HasMany
    // {
    //     return $this->hasMany(Student::class);
    // }
}