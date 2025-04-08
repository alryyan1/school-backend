<?php
// app/Models/Subject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // If linking to teachers/grades
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;
    // use SoftDeletes; // Uncomment if added to migration

    protected $fillable = [
        'name',
        'code',
        'description',
        // 'is_active', // Add if uncommented in migration
        // 'credit_hours', // Add if uncommented in migration
        // 'type', // Add if uncommented in migration
    ];

    protected $casts = [
        // 'is_active' => 'boolean', // Add if uncommented in migration
    ];

    /**
     * The teachers that teach this subject (if pivot table exists).
     */
    public function teachers(): BelongsToMany
    {
        // Make sure 'teacher_subjects' table and relationship exist
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }
    // In app/Models/Subject.php
    public function academicYearSubjects(): HasMany
    {
        return $this->hasMany(AcademicYearSubject::class);
    }
    // Add other relationships (GradeLevel, etc.) if implemented
}
