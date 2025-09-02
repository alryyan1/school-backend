<?php
// app/Models/GradeLevel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany; // If linking to Subjects via pivot

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Classroom> $classrooms
 * @property-read int|null $classrooms_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EnrollMent> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\School> $schools
 * @property-read int|null $schools_count
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeLevel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        return $this->hasMany(EnrollMent::class); // Assumes EnrollMent model exists
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
        return $this->belongsToMany(School::class, 'school_grade_levels')->withPivot('basic_fees') // <-- Include pivot data
            ->withTimestamps();
    }
    // Example: Relationship to Subjects (Requires Pivot Table 'grade_level_subject')
    // public function subjects(): BelongsToMany
    // {
    //     return $this->belongsToMany(Subject::class, 'grade_level_subject');
    // }
}
