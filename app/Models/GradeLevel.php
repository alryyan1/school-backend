<?php
// app/Models/GradeLevel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany; // If linking to Subjects via pivot

class GradeLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        // 'school_id', // Not in current migration
    ];

    // Timestamps are enabled by default and present in migration

    /**
     * Get the classrooms associated with this grade level.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * Get the enrollments associated with this grade level.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class); // Assumes Enrollment model exists
    }

    // In app/Models/GradeLevel.php
    public function academicYearSubjects(): HasMany
    {
        return $this->hasMany(AcademicYearSubject::class);
    }
     /**
     * The schools that have this grade level.
     */
    public function schools(): BelongsToMany // <-- Define Relationship
    {
        return $this->belongsToMany(School::class, 'school_grade_levels');
    }
    // Example: Relationship to Subjects (Requires Pivot Table 'grade_level_subject')
    // public function subjects(): BelongsToMany
    // {
    //     return $this->belongsToMany(Subject::class, 'grade_level_subject');
    // }
}
