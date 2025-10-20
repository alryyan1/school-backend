<?php
// app/Models/Teacher.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $national_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $gender
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string $qualification
 * @property \Illuminate\Support\Carbon $hire_date
 * @property string|null $address
 * @property string|null $photo
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademicYearSubject> $academicYearSubjects
 * @property-read int|null $academic_year_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Database\Factories\TeacherFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereHireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereQualification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'place_of_birth',
        'nationality',
        'document_type',
        'document_number',
        'marital_status',
        'number_of_children',
        'children_in_school',
        'secondary_phone',
        'whatsapp_number',
        'qualification',
        'highest_qualification',
        'specialization',
        'academic_degree',
        'hire_date',
        'appointment_date',
        'years_of_teaching_experience',
        'training_courses',
        'address',
        'photo', // Path to photo
        'academic_qualifications_doc_path',
        'personal_id_doc_path',
        'cv_doc_path',
        'is_active',
        
    ];
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher'); // Assumes pivot table name
    }
    protected $casts = [
        'birth_date' => 'date:Y-m-d', // Cast to date, format on serialization
        'hire_date' => 'date:Y-m-d',  // Cast to date, format on serialization
        'appointment_date' => 'date:Y-m-d',  // Cast to date, format on serialization
        'is_active' => 'boolean',
        'number_of_children' => 'integer',
        'children_in_school' => 'integer',
        'years_of_teaching_experience' => 'integer',
    ];
    // In app/Models/Teacher.php
    public function academicYearSubjects(): HasMany
    {
        return $this->hasMany(AcademicYearSubject::class);
    }
    
}
