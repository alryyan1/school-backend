<?php
// app/Models/Subject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // If linking to teachers/grades
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademicYearSubject> $academicYearSubjects
 * @property-read int|null $academic_year_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }
    // In app/Models/Subject.php
    public function academicYearSubjects(): HasMany
    {
        return $this->hasMany(AcademicYearSubject::class);
    }
 
    // Add other relationships (GradeLevel, etc.) if implemented
}
